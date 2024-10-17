<?php

namespace App\Http\Controllers;

use DataTables;

use App\Models\UserAccess;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use Spatie\QueryBuilder\QueryBuilder;
use App\Solid\Interfaces\RapidxRepositoryInterface;
use App\Solid\Interfaces\UserAccessRepositoryInterface;

class UserController extends Controller
{
  protected $UserAccessRepository;
  protected $RapidxRepository;

  public function __construct( 
    UserAccessRepositoryInterface $UserAccessRepository,
    RapidxRepositoryInterface $RapidxRepository
  ){
    $this->UserAccessRepository = $UserAccessRepository;
    $this->RapidxRepository = $RapidxRepository;
  }

  public function get_users(Request $request){
    /**
     * Add relations
     *
     * @param array $relation
     */
    $relation = array(
      'rapidx_details'
    );

    /**
     * Add codition
     *
     * @param array $condition
     */
    $condition = array();

    $user = $this->UserAccessRepository->getUserWithRelationAndCondition($condition, $relation);

    return DataTables::of($user)
    ->addColumn('action', function($user){
      $result = "";
      if(is_null($user->deleted_at)){
        $result .= "<button class='btn btn-sm btn-secondary btnEditUser' 
        data-id='$user->id'
        data-rapidx-id='$user->rapidx_id'
        data-u-type='$user->user_type'
        data-u-access='$user->user_access'
        ><i class='fas fa-edit'></i></button>";
        $result .= "<button class='btn btn-sm btn-danger btnUpdateUser ml-1' data-id='$user->id' data-status='0'><i class='fas fa-trash'></i></button>";
      }
      else{
        $result .= "<button class='btn btn-sm btn-success btnUpdateUser' data-id='$user->id' data-status='1'><i class='fas fa-redo'></i></button>";
      }
      return $result;
    })
    ->addColumn('status', function($user){
      $result = "";
      if(is_null($user->deleted_at)){
        $result .= "<span class='badge badge-success'>Active</span>";
      }
      else{
        $result .= "<span class='badge badge-danger'>Inctive</span>";

      }
      return $result;
    })
    ->addColumn('u_type', function($user){
      $result = "";
      switch($user->user_type){
        case 0:
          $result .= "<span>User</span>";
        break;
        case 1:
          $result .= "<span>Admin</span>";
        break;
        case 2:
          $result .= "<span>Super User</span>";
        break;
        default:
          $result .= "<span>---</span>";
        break;
      }
      return $result;
    })
    ->addColumn('u_access', function($user){
      $result = "";
      switch($user->user_access){
        case 0:
          $result .= "<span>Requestor</span>";
        break;
        case 1:
          $result .= "<span>Purchasing</span>";
        break;
        default:
          $result .= "<span>---</span>";
        break;
      }
      return $result;
    })
    ->rawColumns(['status', 'action', 'u_type','u_access'])
    ->make(true);
  }

  public function get_rapidx_user_list(Request $request){
    $condition = array(
      'user_stat' => 1
    );
    return $this->RapidxRepository->getRapidxUserWithCondition($condition); 
  }

  public function save_user(UserRequest $request){
    date_default_timezone_set('Asia/Manila');
    $data = $request->validated();

    if(isset($request->id)){ // Update
      $data['updated_by'] = $_SESSION['rapidx_username'];

      return $this->UserAccessRepository->update($request->id, $data);

    }
    else{ // Create
      $data['created_by'] = $_SESSION['rapidx_username'];
      $data['created_at'] = NOW();
      return $this->UserAccessRepository->insert($data);
    }
  }

  public function update_status(Request $request){
    date_default_timezone_set('Asia/Manila');
    $to_update = array();
    if($request->status == 0){
      $to_update['deleted_at'] = NOW();
    }
    else{
      $to_update['deleted_at'] = NULL;

    }
    return $this->UserAccessRepository->update($request->id, $to_update);
  }

}
