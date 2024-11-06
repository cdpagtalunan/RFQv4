<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Solid\Interfaces\UomRepositoryInterface;

class UomController extends Controller
{
    protected $UomRepository;
    
    public function __construct( UomRepositoryInterface $UomRepository) {
      $this->UomRepository = $UomRepository;
    }
    public function get_uom(Request $request){
        $condition = array(
            'deleted_at' => null,
        );
        return $this->UomRepository->getUomWithCondition($condition);
    }
}
