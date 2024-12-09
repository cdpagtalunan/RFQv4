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
            'active_flag' => 1,
        );
        return $this->SupplierRepository->getSupplierWithCondition($condition);
    }
}
