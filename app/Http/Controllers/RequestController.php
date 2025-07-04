<?php

namespace App\Http\Controllers;

use DataTables;
use Carbon\Carbon;

use App\Models\RequestItem;
use Illuminate\Http\Request;
use App\Models\QuotationRequest;
use App\Http\Requests\ItemRequest;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\RequestDetailRequest;
use App\Solid\Interfaces\EmailRepositoryInterface;
use App\Solid\Interfaces\RequestRepositoryInterface;
use App\Solid\Interfaces\UserAccessRepositoryInterface;

use Mail;

class RequestController extends Controller
{
    protected $RequestRepository;
    protected $EmailRepository;
    protected $UserAccessRepository;
    
    public function __construct( 
        RequestRepositoryInterface $RequestRepository,
        EmailRepositoryInterface $EmailRepository,
        UserAccessRepositoryInterface $UserAccessRepository
    ) {
      $this->RequestRepository = $RequestRepository;
      $this->EmailRepository = $EmailRepository;
      $this->UserAccessRepository = $UserAccessRepository;
    }
    
    public function dt_get_request_item(Request $request){
        $condition = array(
            'fk_quotation_requests_id' => $request->id,
            'deleted_at'               => null,
        );
        $relations = array();
        $rfq = $this->RequestRepository->getRequestItemWithConditionAndRelation($condition, $relations);
        return DataTables::of($rfq)
        ->addColumn('action', function($rfq){
            $result = "";
            $result .= "<center>";
            $result .= "<button class='btn btn-sm btn-danger btnRemoveReqItem' data-id='{$rfq->id}' title='Remove Item'><i class='fas fa-xmark'></i></button>";
            $result .= "</center>";
            return $result;
        })
        ->rawColumns(['action'])
        ->make(true);
    }

    public function save_req_details(RequestDetailRequest $request){
        date_default_timezone_set('Asia/Manila');
        $data = $request->validated();
        $attachment = array();
        
        try{
            if ($request->hasFile('attachment')) {
                foreach ($request->file('attachment') as $file) {
    
                    $original_filename =  $file->getClientOriginalName();
                    Storage::putFileAs('public/file_attachments', $file, $original_filename);
                    array_push($attachment, $original_filename);
                }
            }
            $data['cc'] = $request->cc;
    
            if(isset($request->id)){ // Update
                if($request->checkedReupload == 'true'){
                    $data['attachment'] = implode('||', $attachment);
                }
                $data['updated_by'] = $_SESSION['rapidx_user_id'];
                return $this->RequestRepository->update($request->id, $data);
            }
            else{ // Create
                /**
                 * Generate Control number
                 */
                $relations = [];
                $conditions = array();
                $request_repository_data = $this->RequestRepository->getQuotationRequestWithConditionAndRelation($conditions, $relations);
        
        
                $currentMonth = Carbon::now()->format('m');
                $currentYear = Carbon::now()->format('y');
        
                $collection = collect($request_repository_data)->filter(function ($request) use ($currentMonth) {
                    $month = Carbon::parse($request['created_at'])->format('m');
                    return $month === $currentMonth; // Filter by month
                })->sortBy([['id', 'DESC']]);
                
                $new_count = count($collection) + 1;
                if($new_count < 999){
                    $new_count = str_pad($new_count, 3, '0', STR_PAD_LEFT);
                }
                $new_ctrl_no = "RFQ-{$currentYear}{$currentMonth}-{$new_count}";
    
    
                // Data
                if(count($attachment) > 0){
                    $data['attachment'] = implode('||', $attachment);
                }
                $data['ctrl_no'] = $new_ctrl_no;
                $data['created_by'] = $_SESSION['rapidx_user_id'];
                $data['created_at'] = NOW();
    
                return $this->RequestRepository->insertGetId($data);
    
            }
        }catch(Exemption $e){
            DB::rollback();
            return $e;
        }
       
    }

    public function save_item(ItemRequest $request){
        date_default_timezone_set('Asia/Manila');
        $data = $request->validated();

        // if(isset($request->id)){ // Update
            
        // }
        // else{ // Create
            $data['created_by'] = $_SESSION['rapidx_user_id'];
            $data['created_at'] = NOW();
            
            return $this->RequestRepository->insertItem($data);
        // }
    }

    public function done_request(Request $request){
        date_default_timezone_set('Asia/Manila');
        $data = array(
            'status' => 1,
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
                'body'          => ''
            );

            $conditions = array(
                'id' => $request->id,
            );
            $relations = [
                'category_details',
                'created_by_details',
                'item_details'
            ];
            $rfq = $this->RequestRepository->getQuotationRequestWithConditionAndRelation($conditions, $relations);
            $rfq_collection = collect($rfq)->first();
            $conditions = array(
                'user_access' => 1,
                'deleted_at' => null
            );
            $relations = ['rapidx_details'];
            $purchasing_user = $this->UserAccessRepository->getUserWithRelationAndCondition($conditions, $relations);
            $emailArray['data'] = $rfq_collection;
            if(!is_null($rfq_collection->cc)){
                $emailArray['cc'] = explode(',',$rfq_collection->cc);
            }
            array_push($emailArray['cc'],$rfq_collection->created_by_details->email);
            $emailArray['to'] = collect($purchasing_user)->pluck('rapidx_details.email')->toArray();
            $emailArray['subject'] = "RFQv4 - $rfq_collection->ctrl_no For Logistics Assignment <Do Not Reply!>";
            $emailArray['emailFilePath'] = 'transaction_email';
            $emailArray['body'] = 'Please be informed that RFQ is for purchasing assignment.';


            // return $emailArray;
            $this->EmailRepository->sendEmail($emailArray);
        }

        return $update_result;
    }

    public function dt_get_request(Request $request){
        $relations = [
            'category_details',
            'assigned_to_details',
        ];
        $conditions = array(
            'created_by' => $_SESSION['rapidx_user_id'],
            'deleted_at' => null,
        );

        $quotation_request = $this->RequestRepository->getQuotationRequestWithConditionAndRelation($conditions, $relations);
        return DataTables::of($quotation_request)
        ->addColumn('action', function($quotation_request){
            $result = "";
            $result .= "<center>";
            $result .= "<button class='btn btn-sm btn-primary btnViewRequest' title='View Request' 
            data-id='$quotation_request->id'>
                <i class='fas fa-eye'></i>
            </button>";
            if($quotation_request->status == 0){
                $result .= "<button class='btn btn-sm btn-secondary ml-1 btnEditRequest' title='Edit Request' 
                data-id='$quotation_request->id'>
                    <i class='fas fa-edit'></i>
                </button>";
                $result .= "<button class='btn btn-sm btn-danger btnCancelRequest ml-1' title='Cancel Request' data-id='$quotation_request->id'>
                    <i class='fas fa-xmark'></i>
                </button>";
            }
            $result .= "</center>";
            return $result;
        })
        ->addColumn('status', function($quotation_request){
            $result = "";
            $result .= "<center>";
            switch ($quotation_request->status) {
                case 0:
                    $result .= "<label class='badge badge-secondary'>For Edit</label>";
                    break;
                case 1:
                    $result .= "<label class='badge badge-warning text-dark'>For Purchasing Assignment</label>";
                    break;
                case 2:
                    $result .= "<label class='badge badge-info'>For Purchasing Quotation</label>";
                    break;
                case 3:
                    $result .= "<label class='badge badge-primary'>For Head Approval</label>";
                    break;
                case 4:
                    $result .= "<label class='badge badge-success'>Served</label>";
                    break;
                case 5:
                    $result .= "<label class='badge badge-danger'>Cancelled</label><br><p style='font-size: 0.875em'>{$quotation_request->log_cancel_remarks}</p>";
                    break;
                default:
                    $result .= "<label class='badge badge-warning'>For Purchasing Quotation</label>";
                    break;
            }
            $result .= "</center>";
            return $result;
        })
        ->rawColumns(['action', 'status'])
        ->make(true);
    }

    public function get_request_details_by_id(Request $request){
        $relations = [
            'item_details',
            'category_details'
        ];
        $conditions = array(
            'id' => $request->id
        );
        $data = $this->RequestRepository->getQuotationRequestWithConditionAndRelation($conditions, $relations);
        return collect($data)->first();
    }

    public function remove_item(Request $request){
        return $this->RequestRepository->deleteItem($request->id);
    }

    public function cancel_request(Request $request){
        date_default_timezone_set('Asia/Manila');
        $data = array(
            'deleted_at' => NOW(),
        );
        return $this->RequestRepository->update($request->id, $data);
    }
}
