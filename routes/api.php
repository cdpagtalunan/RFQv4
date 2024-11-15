<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UomController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CommonController;
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
        // Route::get('generate_control_no', 'generate_control_no');
    });

    Route::controller(UomController::class)->group(function(){
        Route::get('get_uom', 'get_uom');
    });

    Route::controller(TransactionController::class)->group(function(){
        Route::get('dt_get_log_request', 'dt_get_log_request');
        Route::get('dt_get_items_supplier', 'dt_get_items_supplier');
        Route::get('get_purchasing_staff', 'get_purchasing_staff');
        Route::post('assign_request', 'assign_request');
        Route::post('save_quotation', 'save_quotation');
        Route::get('dt_get_supplier_quotation', 'dt_get_supplier_quotation');
        
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

    
});



