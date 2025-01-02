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
        // return $_SESSION;
        $relations = array(
            'category_details',
            'item_details',
            'created_by_details',
            'assigned_by_details',
            'assigned_to_details'
        );
        $conditions = array(
            'status'     => $request->status,
            'deleted_at' => null,
            // 'assigned_to' => $_SESSION['rapidx_user_id']

        );
        if($request->status == 2 && $_SESSION['rfq_type'] != 2){
            $conditions['assigned_to'] = $_SESSION['rapidx_user_id'];
        }
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
                    if($quotation->assigned_to == $_SESSION['rapidx_user_id']){
                        $result .= "<button class='btn btn-sm btn-info btnAddSupplier ml-1' title='Add Supplier' data-id='{$quotation->id}' data-ctrl='{$quotation->ctrl_no}' data-request='".json_encode($quotation)."' data-status='{$quotation->status}'><i class='fas fa-address-book'></i></button>";
                    }
                    break;
                case 3:
                    $result .= "<button class='btn btn-sm btn-danger btnDisapproveQuot ml-1' title='Disapprove Quotation' data-id='{$quotation->id}'><i class='fas fa-xmark'></i></button>";
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
        $relations = array(
            'item_quotation_details',
            'request_details'
        );
        $items = $this->RequestRepository->getRequestItemWithConditionAndRelation($condition, $relations);

        return DataTables::of($items)
        ->addColumn('action', function($items){
            $result = "";
            $result .= "<center>";
            $collection = collect($items->item_quotation_details)->where('selected_quotation', 1);
            // return $collection;
            
           
            if(count($collection) > 0 && $items->request_details->status == 3){
                $result .= "<button class='btn btn-sm btn-success btnAddQuotation' title='View Suppliers' 
                    data-item-id='{$items->id}'
                    data-item-name='{$items->item_name}'
                    data-item-qty='{$items->qty}'
                    data-item-uom='{$items->uom}'
                >
                <i class='fas fa-eye'></i></button>";
            }
            else{
                $result .= "<button class='btn btn-sm btn-primary btnAddQuotation' title='View Suppliers' 
                    data-item-id='{$items->id}'
                    data-item-name='{$items->item_name}'
                    data-item-qty='{$items->qty}'
                    data-item-uom='{$items->uom}'
                >
                <i class='fas fa-eye'></i></button>";
            }
            $result .= "</center>";
            return $result; 
        })
        ->addColumn('no_of_quotation', function($items){
            $condition = array(
                'deleted_at' => null,
                'request_item_id' => $items->id
            );
            $quotations = $this->RequestRepository->getSupplierQuotationWithCondition($condition);
            // return count($quotations);
            return "<center><span class='badge badge-info'>".count($quotations)."</span></center>";
            // return "<center>".count($quotations)."</center>";
        })
        ->addColumn('raw_sel_quotation_status', function($items){
            /*
                * This additional column can be used in logistics head module
                * This is to determine if the requested item have a selected winning quotation.
            */
            $result = 0;
            $collection = collect($items->item_quotation_details)->where('selected_quotation', 1);
            if(count($collection) > 0 && $items->request_details->status == 3){
               $result = 1;
            }
            return $result;
        })
        ->rawColumns(['action', 'no_of_quotation'])
        ->make(true);
    }

    public function save_quotation(QuotationRequest $request){

        $data = $request->validated();
        // return $data;
        /*
            * Manage the attachment 
        */
        $original_filename = null;
        if(isset($request->id)){
            $original_filename = $request->attachment;
        }
        if($request->file('attachment')){
            $original_filename = $request->file('attachment')->getClientOriginalName();
            
            Storage::putFileAs('public/quotation_attachments', $request->attachment, $original_filename);
        }
        $data['attachment'] = $original_filename;
        /*
            * Manage the supplier
        */
        // $supplier_condition = array(
        //     'supplier_name' => trim($request->supplier_name),
        //     'deleted_at' => null
        // );
        // $existing_supplier = $this->SupplierRepository->getSupplierWithCondition($supplier_condition);
        // $existing_supplier = collect($existing_supplier)->first();

        // if(!$existing_supplier){
        //     $supplier_data = array(
        //         'supplier_name' => trim($request->supplier_name),
        //         'created_by'    => $_SESSION['rapidx_user_id']
        //     );
        //     $this->SupplierRepository->insert($supplier_data);
        // }
        // * Save Item Quotation
        if(isset($request->id)){
            $data['updated_by'] = $_SESSION['rapidx_user_id'];
            $data['updated_at'] = NOW();
            return $this->RequestRepository->updateItemQuotation($request->id, $data);
          
        }
        else{
            $data['created_by'] = $_SESSION['rapidx_user_id'];
            $data['created_at'] = NOW();
            return $this->RequestRepository->insertItemQuotation($data);
        }
        
    }

    public function dt_get_supplier_quotation(Request $request){
        $condition = array(
            'request_item_id' => $request->item_id,
            'deleted_at' => null
        );
        $supplier_quotation = $this->RequestRepository->getSupplierQuotationWithCondition($condition);
        
        return DataTables::of($supplier_quotation)
        ->addColumn('action', function($supplier_quotation){
            $result = "";
            $result .= "<center class='d-flex flex-row'>";
            $result .= "<button class='btn btn-sm btn-secondary btnEditQuotation' data-id='{$supplier_quotation->id}' data-quotation='".json_encode($supplier_quotation)."'><i class='fas fa-edit'></i></button>";
            $result .= "<button class='btn btn-sm btn-danger btnDeleteQuotation ml-1' data-id='{$supplier_quotation->id}'><i class='fas fa-trash'></i></button>";
            $result .= "</center>";
            return $result;
        })
        ->addColumn('attachment_link', function($supplier_quotation){
            $result = "";
            $result .= "<a href='download/{$supplier_quotation->attachment}' target='_blank'>{$supplier_quotation->attachment}</a>";
            return $result;
        })
        ->rawColumns(['attachment_link', 'action'])
        ->make(true);
    }

    public function delete_quotation(Request $request){
        return $this->RequestRepository->deleteQuotation($request->id);
    }

    public function proceed_approval(Request $request){
        $data = array(
            'status' => 3,
            'updated_by' => $_SESSION['rapidx_user_id']
        );
        $update_result = $this->RequestRepository->update($request->id, $data);

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

            $request_conditions = array(
                'id' => $request->id,
            );
            $request_relations = array(
                'item_details',
                'item_details.item_quotation_details',
                'created_by_details',
                'assigned_to_details',
                'category_details'
            );

            $request_details = $this->RequestRepository->getQuotationRequestWithConditionAndRelation($request_conditions, $request_relations);
            $request_details = collect($request_details)->first();

            

            $emailArray['data'] = $request_details;
            // $emailArray['to'] = collect($to_user)->pluck('rapidx_details.email')->toArray();
            $emailArray['cc'] = explode(',',$request_details->cc);
            array_push($emailArray['cc'],$request_details->created_by_details->email);
            $emailArray['subject'] = "RFQv4 - {$request_details->ctrl_no} For Logistics Head Approval";
            $emailArray['emailFilePath'] = 'transaction_email';
            $emailArray['body'] = "Please be informed that RFQ is now for logistics head approval.";
            $this->EmailRepository->sendEmail($emailArray);
        }
        return $update_result;
    }

    public function dt_get_quotation_summary(Request $request){
        $conditions = array(
            'request_item_id' => $request->req_id
        );
        $quotations = $this->RequestRepository->getSupplierQuotationWithCondition($conditions);

        return DataTables::of($quotations)
        ->addColumn('action', function($quotations){
            $result = "";
            $result = "<input type='radio' name='radioSelect' value='{$quotations->id}' data-selected='{$quotations->selected_quotation}'/> {$quotations->currency} {$quotations->price}";
            return $result;
        })
        ->rawColumns(['action', 'raw_price'])
        ->make(true);
    }

    public function select_winning_quotation(Request $request){
        $to_edit = array(
            'selected_quotation' => 0
        );
        $conditions = array(
            'request_item_id' => $request->req_item_id
        );
        $relation = array();
        $this->RequestRepository->updateItemQuotationWithConditionAndRelation($conditions, $to_edit, $relation);


        $to_edit = array(
            'selected_quotation' => 1,
            'updated_by' => $_SESSION['rapidx_user_id']
        );
        return $this->RequestRepository->updateItemQuotation($request->req_item_quot, $to_edit);
    }

    public function disapprove_quotation(Request $request){
        $edit_array = array(
            'status' => 2,
            'updated_by' => $_SESSION['rapidx_user_id']
        );
        $quotationRequest =  $this->RequestRepository->update($request->request_id, $edit_array);
        if(isset($quotationRequest)){
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

            $request_conditions = array(
                'id' => $request->request_id,
            );
            $request_relations = array(
                'item_details',
                'item_details.item_quotation_details',
                'created_by_details',
                'assigned_to_details',
                'category_details'
            );

            $request_details = $this->RequestRepository->getQuotationRequestWithConditionAndRelation($request_conditions, $request_relations);
            $request_details = collect($request_details)->first();

            $emailArray['data'] = $request_details;
            // $emailArray['to'] = collect($to_user)->pluck('rapidx_details.email')->toArray();
            $emailArray['cc'] = explode(',',$request_details->cc);
            array_push($emailArray['cc'],$request_details->created_by_details->email);
            $emailArray['subject'] = "RFQv4 - {$request_details->ctrl_no} Disapproved Quotation";
            $emailArray['emailFilePath'] = 'transaction_email';
            $emailArray['body'] = "Please be informed that RFQ has been disapproved.";
            $this->EmailRepository->sendEmail($emailArray);
        }

        return $quotationRequest;
    }

    public function serve_quotation(Request $request){
        // return $request->all();
        $to_edit = array(
            'selected_quotation' => 0
        );
        $conditions = array(
            // 'request_item_id' => $request->req_item_id,
            'request_item_details.fk_quotation_requests_id' => $request->id
        );
        $relations = array(
            'request_item_details'
        );
        $this->RequestRepository->updateItemQuotationWithConditionAndRelation($conditions, $to_edit, $relations);

        $to_edit = array(
            'selected_quotation' => 1,
            'updated_by' => $_SESSION['rapidx_user_id']
        );
        foreach ($request->selected_winner as $sel_winner) {
            $this->RequestRepository->updateItemQuotation($sel_winner, $to_edit);
        }

        $edit_array = array(
            'status' => 4,
            'updated_by' => $_SESSION['rapidx_user_id']
        );
        $quotationRequest =  $this->RequestRepository->update($request->id, $edit_array);
        if(isset($quotationRequest)){
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

            $request_conditions = array(
                'id' => $request->id,
            );
            $request_relations = array(
                'item_details',
                'item_details.item_quotation_details',
                'created_by_details',
                'assigned_to_details',
                'category_details'
            );

            $request_details = $this->RequestRepository->getQuotationRequestWithConditionAndRelation($request_conditions, $request_relations);
            $request_details = collect($request_details)->first();

            $emailArray['data'] = $request_details;
            $emailArray['to'] = $request_details->created_by_details->email;
            if(isset($request_details->cc)){
                $emailArray['cc'] = explode(',',$request_details->cc);
            }
            array_push($emailArray['cc'],$request_details->assigned_to_details->email);

            $emailArray['subject'] = "RFQv4 - {$request_details->ctrl_no} Served Quotation";
            $emailArray['emailFilePath'] = 'transaction_email';
            $emailArray['body'] = "Please be informed that RFQ has been served and ready for EPRPO upload.";
            $this->EmailRepository->sendEmail($emailArray);
        }

        return $quotationRequest;
    }
    public function get_quotations(Request $request){
        $condtion = array(
            'request_item_id' => $request->id
        );
        return $this->RequestRepository->getSupplierQuotationWithCondition($condtion);
        
    }

    public function get_request_details(Request $request){
        $relations = [
            'item_details',
            'item_details.item_quotation_details',
            'category_details'
        ];
        $conditions = array(
            'id' => $request->id
        );

        $data = $this->RequestRepository->getQuotationRequestWithConditionAndRelation($conditions, $relations);

        /**
            * Collection to group the supplier names 
        */
        $supplierNames = collect($data)
        ->pluck('item_details') // Get the item details from the data
        ->flatten(1) // Flatten the nested array
        ->pluck('item_quotation_details') // Get the item_quotation_details for each item
        ->flatten(1) // Flatten the nested array
        ->pluck('supplier_name') // Get the supplier names
        ->groupBy(function ($supplier) {
            return $supplier; // Group by supplier name
        })
        ->keys();


        $item_details = collect($data)
        ->pluck('item_details')
        ->flatten(1);

        $unique_other_details_per_supplier = collect($data)
        ->pluck('item_details') // Get the item details from the data
        ->flatten(1) // Flatten the nested array
        ->pluck('item_quotation_details') // Get the item_quotation_details for each item
        ->flatten(1) // Flatten the nested array
        ->whereNotNull('price')
        ->groupBy('supplier_name')
        ->toArray();

        // return $unique_other_details_per_supplier;
        return response()->json([
            'rfqDetails'     => $data,
            'supplierNames' => $supplierNames,
            'itemDetails'   => $item_details,
            'uniqueOtherDetailsPerSupplier' => $unique_other_details_per_supplier
        ]);
    }
}
