<?php

namespace App\Http\Controllers;

use DataTables;
use Illuminate\Http\Request;
use App\Http\Requests\AssignRequest;
use App\Solid\Interfaces\EmailRepositoryInterface;
use App\Solid\Interfaces\RequestRepositoryInterface;
use App\Solid\Interfaces\UserAccessRepositoryInterface;

class TransactionController extends Controller
{
    protected $RequestRepository;
    protected $UserAccessRepository;
    protected $EmailRepository;
    
    public function __construct( 
        RequestRepositoryInterface $RequestRepository,
        UserAccessRepositoryInterface $UserAccessRepository,
        EmailRepositoryInterface $EmailRepository
    ){
        $this->RequestRepository = $RequestRepository;
        $this->UserAccessRepository = $UserAccessRepository;
        $this->EmailRepository = $EmailRepository;
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
        $items = $this->RequestRepository->getRequestItemWithCondition($condition);

        return DataTables::of($items)
        ->addColumn('action', function($items){
            $result = "";
            $result .= "<center>";
            $result .= "<button class='btn btn-sm btn-primary btnAddSupplier' title='Add Supplier' data-item-id='{$items->id}'><i class='fas fa-file-circle-plus'></i></button>";
            $result .= "</center>";
            return $result; 
        })
        ->make(true);
    }
}
