<?php
namespace App\Solid\Repositories;

use App\Models\Currency;
use Illuminate\Support\Facades\DB;

/**
 * Import Interfaces
 */
use Illuminate\Support\Facades\Auth;
use App\Solid\Interfaces\CurrencyRepositoryInterface;


class CurrencyRepository implements CurrencyRepositoryInterface
{
    public function insert(array $data){
        DB::beginTransaction();
        try{
            Currency::insert($data);
            DB::commit();

            return response()->json([
                'result' => true,
                'msg' => 'Transaction Success!'
            ]);
        }catch(Exemption $e){
            DB::rollback();
            return $e;
        }
    }

    public function getAllCurrency(){
        return Currency::all();
    }

    public function update(int $id, array $data){
        DB::beginTransaction();
        
        try{
            Currency::where('id', $id)
            ->update($data);

            DB::commit();
            return response()->json([
                'result' => true,
                'msg' => 'Transaction Success!'
            ]);
        }
        catch(Exemption $e){
            DB::rollback();
            return $e;
        }
    }
}