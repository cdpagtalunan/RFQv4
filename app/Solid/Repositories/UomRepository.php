<?php
namespace App\Solid\Repositories;

use App\Models\Uom;
use Illuminate\Support\Facades\DB;

/**
 * Import Interfaces
 */
use Illuminate\Support\Facades\Auth;
use App\Solid\Interfaces\UomRepositoryInterface;


class UomRepository implements UomRepositoryInterface
{
    public function getUomWithCondition(array $condition){
        $query = Uom::query();
        foreach ($condition as $key => $value) {
            $query->where($key, $value);
        }
        return $query->get();
    }

    public function insert(array $data){
        DB::beginTransaction();
        
        try{
            Uom::insert($data);
            
            DB::commit();
            return response()->json([
                'result' => true,
                'msg' => 'Transaction Success!',
            ]);
        }catch(Exemption $e){
            DB::rollback();
            return $e;
        }
       
    }

    public function update(int $id, array $data){
        DB::beginTransaction();
        try{
            Uom::where('id', $id)
            ->update($data);
            
            DB::commit();
            return response()->json([
                'result' => true,
                'msg' => 'Transaction Success!',
            ]);
        }catch(Exemption $e){
            DB::rollback();
            return $e;
        }
    }
}