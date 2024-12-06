<?php

namespace App\Http\Controllers;

use DataTables;
use Illuminate\Http\Request;
use App\Http\Requests\UOMRequest;
use App\Solid\Interfaces\UomRepositoryInterface;

class UomController extends Controller
{
    protected $UomRepository;
    
    public function __construct( UomRepositoryInterface $UomRepository) {
      $this->UomRepository = $UomRepository;
    }
    public function get_uom(Request $request){
        $condition = array(
            'deleted_at' => null,
        );
        return $this->UomRepository->getUomWithCondition($condition);
    }
    public function dt_get_uom(Request $request){
        $condition = array();
        $uoms = $this->UomRepository->getUomWithCondition($condition);
        return DataTables::of($uoms)
        ->addColumn('action', function($uoms){
            $result = "";
            if(is_null($uoms->deleted_at)){
                $result .= "<button class='btn btn-sm btn-primary btnEditUom' data-uoms='$uoms'><i class='fas fa-edit'></i></button>";
                $result .= "<button class='ml-1 btn btn-sm btn-danger btnEditStatus' data-id='$uoms->id' data-status='1'><i class='fas fa-ban'></i></button>";
            }
            else{
                $result .= "<button class=' btn btn-sm btn-success btnEditStatus' data-id='$uoms->id' data-status='0'><i class='fas fa-undo'></i></button>";
            }
            return $result;
        })
        ->addColumn('status', function($uoms){
            $result = "";

            return $result;
        })
        ->rawColumns(['action', 'status'])
        ->make(true);
    }

    public function save_uom(UOMRequest $request){
        date_default_timezone_set('Asia/Manila');
        $data = $request->validated();

        if(isset($request->id)){ // Edit
            $data['updated_by'] = $_SESSION['rapidx_user_id'];
            $data['updated_at'] = NOW();
            return $this->UomRepository->update($request->id, $data);
        }
        else{ // Create
            $data['created_by'] = $_SESSION['rapidx_user_id'];
            $data['created_at'] = NOW();
            return $this->UomRepository->insert($data);
        }
    }

    public function change_uom_status(Request $request){
        $deleted_at = null;
        if($request->status == 1){
            $deleted_at = NOW();
        }

        $data = array(
            'deleted_at' => $deleted_at
        );
        return $this->UomRepository->update($request->id, $data);
    }
}
