<?php
namespace App\Solid\Repositories;

use App\Models\YourModel;
use App\Models\UserAccess;

/**
 * Import Interfaces
 */
use Illuminate\Support\Facades\DB;

/**
 * Import Models
 */
use Illuminate\Support\Facades\Auth;
use App\Solid\Interfaces\UserAccessRepositoryInterface;

class UserAccessRepository implements UserAccessRepositoryInterface
{
    public function getUAccessWithCondition(array $condition){
        // return $condition;
        $query =  UserAccess::query();
        foreach($condition AS $key => $value){
            $query->where($key, $value);
        }
        return $query->get();
    }

    public function getAllUserAccess(){
        return UserAccess::all();
    }

    public function insert(array $data){
        DB::beginTransaction();
        try{
            UserAccess::insert($data);

            DB::commit();
            return response()->json([
                'result' => true,
                'msg' => 'Successfully Saved!'
            ]);
        }catch(Exemption $e){
            DB::rollback();
            return $e;
        }
    }

    public function getUserWithRelationAndCondition(array $condition, array $relation){

        $query =  UserAccess::query();
        $query->with($relation);
        foreach($condition AS $key => $value){
            $query->where($key, $value);
        }
        return $query->get();
    }

    public function update(int $id, array $data){
        DB::beginTransaction();
        try{
            UserAccess::where('id', $id)
            ->update($data);

            DB::commit();

            return response()->json([
                'result' => true,
                'msg' => "Successfully Saved!"
            ]);
        }
        catch(Exemption $e){
            DB::rollback();
            return $e;
        }
    }
}