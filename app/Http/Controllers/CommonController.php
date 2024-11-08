<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Solid\Interfaces\RapidxRepositoryInterface;
use App\Solid\Interfaces\UserAccessRepositoryInterface;

class CommonController extends Controller
{
    protected $UserAccessRepository;
    
    public function __construct( UserAccessRepositoryInterface $UserAccessRepository) {
        $this->UserAccessRepository = $UserAccessRepository;
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

            return $_SESSION;
            // return response()->json([
            //     'rfq_user_details' => $user_access,
            //     'session' => $_SESSION
            // ]);
            return $user_access;
        }
    }
}
