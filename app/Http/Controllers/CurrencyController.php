<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CurrencyRequest;
use App\Solid\Interfaces\CurrencyRepositoryInterface;
use DataTables;

class CurrencyController extends Controller
{
    protected $CurrencyRepository;
    
    public function __construct( CurrencyRepositoryInterface $CurrencyRepository) {
      $this->CurrencyRepository = $CurrencyRepository;
    }
    public function save_currency(CurrencyRequest $request){
        date_default_timezone_set('Asia/Manila');
        $data = $request->validated();
        if(isset($request->id)){ // update
            $data['updated_by'] = $_SESSION['rapidx_user_id'];

            return $this->CurrencyRepository->update($request->id, $data);
            
        }else{ // create
            $data['created_by'] = $_SESSION['rapidx_user_id'];
            $data['created_at'] = NOW();

            return $this->CurrencyRepository->insert($data);
        }
    }

    public function dt_get_currency(Request $request){
        $currency = $this->CurrencyRepository->getAllCurrency();

        return DataTables::of($currency)
        ->addColumn('action', function($currency){
            $result = "";
            if(is_null($currency->deleted_at)){
                $result .= "<button class='btn btn-sm btn-secondary btnEditCurrency' data-id='{$currency->id}' data-currency='{$currency->currency}'><i class='fas fa-edit'></i></button>";
                $result .= "<button class='btn btn-sm btn-danger btnEditStatus ml-1' data-id='{$currency->id}' data-status='0'><i class='fas fa-ban'></i></button>";
            }
            else{
                $result .= "<button class='btn btn-sm btn-success btnEditStatus ml-1' data-id='{$currency->id}' data-status='1'><i class='fas fa-undo'></i></button>";

            }
            return $result;
        })
        ->addColumn('status', function($currency){
            $result = "";
            if(is_null($currency->deleted_at)){
                $result .= "<span class='badge badge-success'>Active</span>";
            }
            else{
                $result .= "<span class='badge badge-danger'>Inactive</span>";
            }
            return $result;
        })
        ->rawColumns(['action', 'status'])
        ->make(true);
    }

    public function update_status(Request $request){
        date_default_timezone_set('Asia/Manila');
        $data = array(
            'deleted_at' => null
        );
        if($request->status == 0){
            $data['deleted_at'] = NOW();
        }
        return $this->CurrencyRepository->update($request->id, $data);
    }

    public function get_currency(Request $request){
        $condition = array(
            'deleted_at' => null,
        );
        return $this->CurrencyRepository->getCurrencyWithCondition($condition);
    }
}
