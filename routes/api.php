<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;

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
        Route::get('get_category', 'get_category');
        Route::post('save_category', 'save_category');
        Route::post('update_status', 'update_status');
    });
});



