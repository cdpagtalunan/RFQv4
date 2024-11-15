<?php

namespace App\Http\Controllers;

use DataTables;
use Illuminate\Http\Request;
use App\Http\Requests\AssignRequest;
use App\Http\Requests\QuotationRequest;
use Illuminate\Support\Facades\Storage;
use App\Solid\Interfaces\EmailRepositoryInterface;
use App\Solid\Interfaces\RequestRepositoryInterface;
use App\Solid\Interfaces\SupplierRepositoryInterface;
use App\Solid\Interfaces\UserAccessRepositoryInterface;

class TransactionController extends Controller
{
    protected $RequestRepository;
    protected $UserAccessRepository;
    protected $EmailRepository;
    protected $SupplierRepository;
    
    public function __construct( 
        RequestRepositoryInterface $RequestRepository,
        UserAccessRepositoryInterface $UserAccessRepository,
        EmailRepositoryInterface $EmailRepository,
        SupplierRepositoryInterface $SupplierRepository
    ){
        $this->RequestRepository    = $RequestRepository;
        $this->UserAccessRepository = $UserAccessRepository;
        $this->EmailRepository      = $EmailRepository;
        $this->SupplierRepository   = $SupplierRepository;
    }
    public function dt_get_log_request(Request $request){
        $relations = array(
            'category_details',
            'item_details',
            'created_by_details'
        );
        $conditions = array(
            'status'     => $request->status,
            'deleted_at' => null
        );
        $quotation = $this->RequestRepository->getQuotationRequestWithConditionAndRelation($conditions, $relations);

        return DataTables::of($quotation)
        ->addColumn('action', function($quotation){
            $result = "";
            $result .= "<center>";
            $result .= "<button class='btn btn-primary btn-sm btnViewRequest' data-id='{$quotation->id}' data-request='".json_encode($quotation)."' data-status='{$quotation->status}' title='View Request'><i class='fas fa-eye'></i></button>";
            switch ($quotation->status) {
                case 1:
                    $result .= "<button class='btn btn-sm btn-info btnAssignRequest ml-1' title='Assign Request' data-id='{$quotation->id}' data-ctrl='{$quotation->ctrl_no}' data-status='{$quotation->status}'><i class='fas fa-user-check'></i></button>";
                    break;
                case 2:
                    $result .= "<button class='btn btn-sm btn-info btnAddSupplier ml-1' title='Add Supplier' data-id='{$quotation->id}' data-ctrl='{$quotation->ctrl_no}' data-request='".json_encode($quotation)."' data-status='{$quotation->status}'><i class='fas fa-address-book'></i></button>";
                    break;
                default:
                    break;
            }
            $result .= "</center>";
            return $result;
        })
        ->rawColumns(['action'])
        ->make(true);
    }

    public function get_purchasing_staff(Request $request){
        $conditions = array(
            'user_access' => 1,
        );
        $relations = array(
            'rapidx_details',
        );
        return $this->UserAccessRepository->getUserWithRelationAndCondition($conditions, $relations);
    }

    public function assign_request(AssignRequest $request){
        date_default_timezone_set('Asia/Manila');
        $data = $request->validated();
        
        $data['assigned_by'] = $_SESSION['rapidx_user_id'];
        $data['status']      = 2;
        $data['assigned_at'] = NOW();
        $update_result = $this->RequestRepository->update($request->request_id, $data);
        if(isset($update_result)){
            /**
             *
             * @param array $emailArray
            */
            $emailArray = array(
                'to'            => [],
                'cc'            => [],
                'bcc'           => [],
                'subject'       => '',
                'data'          => [],
                'emailFilePath' => '',
                'body'          => '',
            );

            $to_conditions = array(
                'rapidx_id' => $data['assigned_to'],
            );
            $to_relations = array(
                'rapidx_details',
            );
            $to_user = $this->UserAccessRepository->getUserWithRelationAndCondition($to_conditions, $to_relations);

            $rfq_conditions = array(
                'id' => $request->request_id,
            );
            $rfq_relations = [
                'category_details',
                'created_by_details',
                'assigned_to_details'

            ];
            $rfq = $this->RequestRepository->getQuotationRequestWithConditionAndRelation($rfq_conditions, $rfq_relations);
            $rfq_collection = collect($rfq)->first();

            $emailArray['data'] = $rfq_collection;
            $emailArray['to'] = collect($to_user)->pluck('rapidx_details.email')->toArray();
            $emailArray['cc'] = explode(',',$rfq_collection->cc);
            array_push($emailArray['cc'],$rfq_collection->created_by_details->email);
            $emailArray['subject'] = "RFQv4 - {$rfq_collection->ctrl_no} Request Assigned";
            $emailArray['emailFilePath'] = 'transaction_email';
            $emailArray['body'] = "Please be informed that RFQ is assigned to {$rfq_collection->assigned_to_details->name}";
            $this->EmailRepository->sendEmail($emailArray);
        }

        return $update_result;
    }

    public function dt_get_items_supplier(Request $request){
        $condition = array(
            'fk_quotation_requests_id' => $request->id
        );
        $relations = array('item_quotation_details');
        $items = $this->RequestRepository->getRequestItemWithConditionAndRelation($condition, $relations);

        return DataTables::of($items)
        ->addColumn('action', function($items){
            $result = "";
            $result .= "<center>";
            $result .= "<button class='btn btn-sm btn-primary btnAddQuotation' title='Add Supplier' 
                data-item-id='{$items->id}'
                data-item-name='{$items->item_name}'
                data-item-qty='{$items->qty}'
                data-item-uom='{$items->uom}'
            >
            <i class='fas fa-file-circle-plus'></i></button>";

            $result .= "</center>";
            return $result; 
        })
        ->make(true);
    }

    public function save_quotation(QuotationRequest $request){

        $data = $request->validated();
        /*
            * Manage the attachment 
        */
        $original_filename = null;
        if($request->file('attachment')){
            $original_filename = $request->file('attachment')->getClientOriginalName();

            Storage::putFileAs('public/quotation_attachments', $request->attachment, $original_filename);
        }
        $data['attachment'] = $original_filename;
        /*
            * Manage the supplier
        */
        $supplier_condition = array(
            'supplier_name' => trim($request->supplier_name),
            'deleted_at' => null
        );
        $existing_supplier = $this->SupplierRepository->getSupplierWithCondition($supplier_condition);
        $existing_supplier = collect($existing_supplier)->first();

        if(!$existing_supplier){
            $supplier_data = array(
                'supplier_name' => trim($request->supplier_name),
                'created_by'    => $_SESSION['rapidx_user_id']
            );
            $this->SupplierRepository->insert($supplier_data);
        }

        // * Save Item Quotation
        $data['created_by'] = $_SESSION['rapidx_user_id'];
        return $this->RequestRepository->insertItemQuotation($data);
    }

    public function dt_get_supplier_quotation(Request $request){
        $condition = array(
            'deleted_at' => null
        );
        $supplier_quotation = $this->RequestRepository->getSupplierQuotationWithCondition($condition);
        
        return DataTables::of($supplier_quotation)
        ->addColumn('attachment_link', function($supplier_quotation){
            $result = "";
            $result .= "<a href='download/{$supplier_quotation->attachment}' target='_blank'>{$supplier_quotation->attachment}</a>";
            return $result;
        })
        ->rawColumns(['attachment_link'])
        ->make(true);
    }
}
