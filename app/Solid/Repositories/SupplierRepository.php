<?php
namespace App\Solid\Repositories;

use App\Models\Supplier;
use Illuminate\Support\Facades\DB;

/**
 * Import Interfaces
 */
use Illuminate\Support\Facades\Auth;
use App\Solid\Interfaces\SupplierRepositoryInterface;


class SupplierRepository implements SupplierRepositoryInterface
{
    public function getSupplierWithCondition(array $condition){
        $query = Supplier::query();
        foreach ($condition as $key => $value) {
            $query->where($key, $value);
        }
        return $query->get();
    }

    public function insert(array $data){
        DB::beginTransaction();
        try{
            Supplier::insert($data);
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
}