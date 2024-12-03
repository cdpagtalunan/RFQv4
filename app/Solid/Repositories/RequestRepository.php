<?php
namespace App\Solid\Repositories;

use App\Models\RequestItem;
use App\Models\QuotationRequest;

/**
 * Import Interfaces
 */
use Illuminate\Support\Facades\DB;
use App\Models\RequestItemQuotation;
use Illuminate\Support\Facades\Auth;
use App\Solid\Interfaces\RequestRepositoryInterface;


class RequestRepository implements RequestRepositoryInterface
{
    public function insertGetId(array $data){
        DB::beginTransaction();
        
        try{
            $id = QuotationRequest::insertGetId($data);

            DB::commit();

            return response()->json([
                'result' => true,
                'msg' => 'Transaction Success',
                'id' => $id,
                'ctrl_no' => $data['ctrl_no']
            ]);
        }catch(Exemption $e){
            DB::rollback();
            return $e;
        }
    }

    public function update(int $id, array $data){
        DB::beginTransaction();
        
        try{
            QuotationRequest::where('id', $id)
            ->update($data);
            
            DB::commit();

            return response()->json([
                'result' => true,
                'msg' => 'Transaction Success',
                'id' => $id
            ]);
        }catch(Exemption $e){
            DB::rollback();
            return $e;
        }
    }

    // public function getRequestItemWithCondition(array $condition){
    public function getRequestItemWithConditionAndRelation(array $condition, array $relation){
        $query = RequestItem::query();
        $query->with($relation);
        foreach ($condition as $key => $value) {
            $query->where($key, $value);
        }
        return $query->get();
    }

    public function insertItem(array $data){
        DB::beginTransaction();
        
        try{
            RequestItem::insert($data);

            DB::commit();

            return response()->json([
                'result' => true,
                'msg' => 'Transaction Success',
            ]);
        }catch(Exemption $e){
            DB::rollback();
            return $e;
        }
    }

    public function getQuotationRequestWithConditionAndRelation(array $conditions, array $relations){
        $query = QuotationRequest::query();
        $query->with($relations);
        foreach ($conditions as $key => $value) {
            $query->where($key, $value);
        }
        return $query->get();
    }

    public function insertItemQuotation(array $data){
        DB::beginTransaction();
        try{
            RequestItemQuotation::insert($data);
            DB::commit();
            
            return response()->json([
                'result' => true,
                'msg'    => 'Transaction Success!',
                'fn'     => 'insertItemQuotation'
            ]);
        }catch(Exemption $e){
            DB::rollback();
            return $e;
        }
    }

    public function getSupplierQuotationWithCondition(array $condition){
        $query = RequestItemQuotation::query();
        foreach ($condition as $key => $value) {
            $query->where($key, $value);
        }
        return $query->get();
    }

    public function updateItemQuotation(int $id, array $data){
        DB::beginTransaction();
        try{
            RequestItemQuotation::where('id', $id)
            ->update($data);

            DB::commit();
            return response()->json([
                'result' => true,
                'msg'    => 'Transaction Success!',
                'fn'     => 'updateItemQuotation'
            ]);
        }catch(Exemption $e){
            DB::rollback();
            return $e;
        }
    }

    public function deleteQuotation(int $id){
        DB::beginTransaction();
        
        try{
            RequestItemQuotation::where('id', $id)
            ->delete();
            
            DB::commit();

            return response()->json([
                'result' => true,
                'msg' => 'Successfully Deleted!',
                'fn' => 'deleteQuotation'
            ]);
        }catch(Exemption $e){
            DB::rollback();
            return $e;
        }
    }

    public function updateItemQuotationWithCondition(array $condition, array $data){
        DB::beginTransaction();
        try{
            $query = RequestItemQuotation::query();
            foreach ($condition as $key => $value) {
                $query->where($key, $value);
            }
            $query->update($data);

            DB::commit();

            return response()->json([
                'result' => true,
                'msg' => 'Transaction Success!',
                'fn' => 'updateItemQuotationWithCondition'
            ]);
        }catch(Exemption $e){
            DB::rollback();
            return $e;
        }
    }

    public function deleteItem(int $id){
        date_default_timezone_set('Asia/Manila');
        DB::beginTransaction();
        try{
            RequestItem::where('id', $id)
            ->delete();
            DB::commit();

            return response()->json([
                'result' => true,
                'msg'    => 'Successfully Deleted!',
                'fn'     => 'deleteItem'
            ]);

        }catch(Exemption $e){
            DB::rollback();
            return $e;
        }
    }
}