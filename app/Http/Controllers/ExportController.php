<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\RFQItem;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Solid\Interfaces\RequestRepositoryInterface;

class ExportController extends Controller
{
  protected $requestRepository;

  public function __construct( RequestRepositoryInterface $requestRepository) {
    $this->requestRepository = $requestRepository;
  }

  public function export(Request $request, $month){
    // return $_SESSION;
    $conditions = array(
      'like:created_at' => $month,
      'deleted_at'      => NULL
    );
    $relations = array(
      'request_details',
      'request_details.assigned_to_details',
      'request_details.created_by_details'
    );
    
    $rfqs;
    if($_SESSION['rfq_access'] == 1){ // Logistics
      $conditions['request_details.status'] = ['>', 0];
    }
    else{
      $conditions['request_details.status'] = ['>', 0];
      $conditions['request_details.created_by'] = $_SESSION['rapidx_user_id'];
    }
    $rfqs = $this->requestRepository->getRequestItemWithConditionAndRelation($conditions, $relations);
    $collection = collect($rfqs)->where('request_details.status', '<>', 5)->flatten(1);
    // return $collection;
    return Excel::download(new RFQItem($collection), "List of Request Quotation-$month.xlsx");
  }
}
