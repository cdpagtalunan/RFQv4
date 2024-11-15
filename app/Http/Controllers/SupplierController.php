<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Solid\Interfaces\SupplierRepositoryInterface;

class SupplierController extends Controller
{
    protected $SupplierRepository;
    
    public function __construct( SupplierRepositoryInterface $SupplierRepository) {
        $this->SupplierRepository = $SupplierRepository;
    }

    public function get_supplier(Request $request){
        $condition = array(
            'deleted_at' => null,
        );
        return $this->SupplierRepository->getSupplierWithCondition($condition);
    }
}
