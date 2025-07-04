<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Solid\Interfaces\EmailRepositoryInterface;
use App\Solid\Interfaces\RapidxRepositoryInterface;
use App\Solid\Interfaces\RequestRepositoryInterface;
use App\Solid\Interfaces\UserAccessRepositoryInterface;

class CommonController extends Controller
{
    protected $UserAccessRepository;
    
    public function __construct( 
        UserAccessRepositoryInterface $UserAccessRepository,
        RequestRepositoryInterface $RequestRepository,
        EmailRepositoryInterface $EmailRepository
    ) {
        $this->UserAccessRepository = $UserAccessRepository;
        $this->RequestRepository    = $RequestRepository;
        $this->EmailRepository    = $EmailRepository;
    }

    public function check_access(Request $request){
        if(!in_array("39", array_column($_SESSION['rapidx_user_accesses'], 'module_id'))){
            return response()->json(['msg' => 'User Dont Have Access', 'access' => $_SESSION['rapidx_user_accesses']], 401);
        }
        else{
            $condition = array(
                'rapidx_id' => $_SESSION['rapidx_user_id'],
                'deleted_at' => NULL
            );
            $user_access = $this->UserAccessRepository->getUAccessWithCondition($condition);
            $user_access = collect($user_access)->first();

            if(!isset($user_access)){
                return response()->json(['msg' => 'User Dont Have Access '], 401);
            }

            $_SESSION['rfq_type'] = $user_access->user_type;
            $_SESSION['rfq_access'] = $user_access->user_access;
            $_SESSION['rfq_approver'] = $user_access->approver;

            return $_SESSION;
            // return response()->json([
            //     'rfq_user_details' => $user_access,
            //     'session' => $_SESSION
            // ]);
            // return $user_access;
        }
    }

    public function download(Request $request, $file){
        $link = "public/quotation_attachments/$file";
        return Storage::download($link);
    }

    public function get_stat(Request $request){
        $conditions = array(
            'deleted_at' => null
        );
        $relations = array();
        $rfqs = $this->RequestRepository->getQuotationRequestWithConditionAndRelation($conditions, $relations);

        $open = collect($rfqs)->where('status', 1)->flatten(1)->count();
        $for_quotation = collect($rfqs)->where('status', 2)->flatten(1)->count();
        $for_approval = collect($rfqs)->where('status', 3)->flatten(1)->count();
        $served = collect($rfqs)->where('status', 4)->flatten(1)->count();

        return response()->json([
            'open'          => $open,
            'for_quotation' => $for_quotation,
            'for_approval'  => $for_approval,
            'served'        => $served
        ]);
    }

    public function get_pending_requests(Request $request){
        $conditions = array(
            'status' => ['1', '2', '3'],
            'deleted_at' => null,
        );
        $relations = array(
            'created_by_details',
            'category_details'
        );
        $pending_rfqs = $this->RequestRepository->getQuotationRequestWithConditionAndRelation($conditions, $relations);
        $grouped_rfq = collect($pending_rfqs)->groupBy('status');

        $conditions_purch_user = array(
            'user_access' => 1
        );
        $relations = ['rapidx_details'];
        $purchasing_user = $this->UserAccessRepository->getUserWithRelationAndCondition($conditions_purch_user, $relations);

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
        
        $emailArray['to'] = collect($purchasing_user)->pluck('rapidx_details.email')->toArray(); // Getting of logistics emails
        $emailArray['bcc'] = ['cpagtalunan@pricon.ph']; // Getting of logistics emails
       

        foreach($conditions['status'] AS $status){

            $emailArray['cc'] = array();
            if(isset($grouped_rfq[$status])){
                
                $collection = collect($grouped_rfq[$status])->filter(function ($rfq) {
                    return $rfq['created_at'] < Carbon::now()->subDay(2)->format('Y-m-d');
                })->flatten(1);
                /** 
                    * @var $status will accept only $conditions['status'] above.
                    * 1 = for log manager assignment
                    * 2 = for purchasing quotation
                    * 3 = for log head approval
                */
                $emailArray['cc'] = collect($collection)->pluck('created_by_details.email')->unique()->flatten(1)->toArray(); // get email of RFQ creator
                $emailArray['data'] = $collection;
                $emailArray['emailFilePath'] = 'pending_notification';
                switch ($status) {
                    case '1':
                        $emailArray['subject'] = "ALERT!! RFQv4 Notification - For Assignment <Do Not Reply>";
                        $emailArray['body'] = "Please be informed that RFQ is pending for assignment";
                        break;
                    case '2':
                        $emailArray['subject'] = "ALERT!! RFQv4 Notification - For Purchasing Quotation <Do Not Reply>";
                        $emailArray['body'] = "Please be informed that RFQ is pending for purchasing quotation";
                        break; 
                    case '3':
                        $emailArray['subject'] = "ALERT!! RFQv4 Notification - For Logistics Head Approval <Do Not Reply>";
                        $emailArray['body'] = "Please be informed that RFQ is pending for approval";
                        break;
                    default:
                        break;
                }
                $this->EmailRepository->sendEmailFollowup($emailArray);
                // $this->EmailRepository->sendEmail($emailArray);
            }
        }
    }

    public function download_attachments(Request $request, $file){
        $link = "public/file_attachments/$file";
        return Storage::download($link);
    }
}
