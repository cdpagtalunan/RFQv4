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
}