<?php
namespace App\Solid\Repositories;

use App\Models\Category;
use Illuminate\Support\Facades\DB;

/**
 * Import Interfaces
 */
use Illuminate\Support\Facades\Auth;
use App\Solid\Interfaces\CategoryRepositoryInterface;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function getAllCategory(){
        return Category::all();
    }

    public function insert(array $data){
        DB::beginTransaction();
        try{
            Category::insert($data);

            DB::commit();
            return response()->json([
                'result' => true,
                'msg'    => "Successfully Saved!"
            ]);
        }
        catch(Exemption $e){
            DB::rollback();
            return $e;
        }
    }

    public function update(int $id, array $data){
        DB::beginTransaction();
        try{
            Category::where('id', $id)
            ->update($data);

            DB::commit();

            return response()->json([
                'result' => true,
                'msg'    => 'Successfully Saved!'
            ]);
        }
        catch(Exemption $e){
            DB::rollback();
            return $e;
        }
    }

    public function getCategoryWithCondition(array $condition){
        $query = Category::query();

        foreach ($condition as $key => $value) {
            $query->where($key, $value);
        }
        return $query->get();
    }
}