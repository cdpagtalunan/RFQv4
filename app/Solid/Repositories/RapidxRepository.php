<?php
namespace App\Solid\Repositories;

use App\Models\YourModel;
use App\Models\RapidxUser;

/**
 * Import Interfaces
 */
use Illuminate\Support\Facades\DB;

/**
 * Import Models
 */
use Illuminate\Support\Facades\Auth;
use App\Solid\Interfaces\RapidxRepositoryInterface;

class RapidxRepository implements RapidxRepositoryInterface
{
    public function getRapidxUserWithCondition(array $condition){
        $query = RapidxUser::query();
        foreach ($condition as $key => $value) {
            $query->where($key, $value);
        }
        return $query->get();
    }
}