<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UomController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CommonController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TransactionController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
// Route::get('get_request_details_by_id', [RequestController::class, 'get_request_details_by_id'])->name('api.get_request_details');
Route::get('get_request_details', [TransactionController::class,'get_request_details'])->name('api.get_request_details');

Route::middleware('checkSessionExist')->group(function(){
    Route::controller(UserController::class)->group(function () {
        Route::get('get_users','get_users');
        Route::get('get_rapidx_user_list', 'get_rapidx_user_list');
        Route::post('save_user', 'save_user');
        Route::post('update_status', 'update_status');
    });

    Route::controller(CategoryController::class)->group(function (){
        Route::get('dt_get_category', 'dt_get_category');
        Route::post('save_category', 'save_category');
        Route::post('update_cat_status', 'update_cat_status');
        Route::get('get_category', 'get_category');
    });

    Route::controller(RequestController::class)->group(function(){
        Route::get('dt_get_request_item', 'dt_get_request_item');
        Route::get('dt_get_request', 'dt_get_request');
        Route::post('save_req_details', 'save_req_details');
        Route::post('save_item', 'save_item');
        Route::post('done_request', 'done_request');
        Route::get('get_request_details_by_id', 'get_request_details_by_id');
        Route::post('remove_item', 'remove_item');
        Route::post('cancel_request', 'cancel_request');
        // Route::get('generate_control_no', 'generate_control_no');
    });

    Route::controller(UomController::class)->group(function(){
        Route::get('get_uom', 'get_uom');
        Route::get('dt_get_uom', 'dt_get_uom');
        Route::post('save_uom', 'save_uom');
        Route::post('change_uom_status', 'change_uom_status');
    });

    Route::controller(TransactionController::class)->group(function(){
        Route::get('dt_get_log_request', 'dt_get_log_request');
        Route::get('dt_get_items_supplier', 'dt_get_items_supplier');
        Route::get('get_purchasing_staff', 'get_purchasing_staff');
        Route::post('assign_request', 'assign_request');
        Route::post('save_quotation', 'save_quotation');
        Route::get('dt_get_supplier_quotation', 'dt_get_supplier_quotation');
        Route::post('delete_quotation', 'delete_quotation');
        Route::post('proceed_approval', 'proceed_approval');
        Route::get('dt_get_quotation_summary', 'dt_get_quotation_summary');
        Route::post('select_winning_quotation', 'select_winning_quotation');
        Route::post('disapprove_quotation', 'disapprove_quotation');
        Route::post('serve_quotation', 'serve_quotation');
        Route::get('get_quotations', 'get_quotations');
        // Route::get('get_request_details', 'get_request_details')->name('api.get_request_details');
    });

    Route::controller(CurrencyController::class)->group(function(){
        Route::post('save_currency', 'save_currency');
        Route::get('dt_get_currency', 'dt_get_currency');
        Route::post('update_status', 'update_status');
        Route::get('get_currency', 'get_currency');
    });

    Route::controller(SupplierController::class)->group(function(){
        Route::get('get_supplier', 'get_supplier');
    });

    Route::controller(CommonController::class)->group(function(){
        Route::get('get_stat', 'get_stat');
        // Route::get('download_attachments/{doc}', 'download_attachments');
    });

    Route::controller(ExportController::class)->group(function(){
        Route::get('export', 'export');
    });
    
});



