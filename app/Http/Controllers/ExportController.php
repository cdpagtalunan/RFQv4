<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\Sheets\Recon;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Solid\Interfaces\RequestRepositoryInterface;

class ExportController extends Controller
{
  protected $requestRepository;

  public function __construct( RequestRepositoryInterface $requestRepository) {
    $this->requestRepository = $requestRepository;
  }

  public function export(Request $request){
    // return $request->all();
    return Excel::download(new Recon(), 'Reconciliation.xlsx');

  }
}
