<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CommonController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::view('/mailer/mail','welcome')->name('mail');
Route::get('get_pending_requests',[CommonController::class, 'get_pending_requests']);

Route::get('check_user', function (Request $request) {
    session_start();
    if($_SESSION){
        return true;
    }
    else{
        return false;
    }
});


Route::middleware('checkSessionExist')->group(function(){
    Route::get('download/{file}', [CommonController::class, 'download']);

    /*
        ! THIS ROUTE WILL BE ACCESS FIRST AND WILL CHECK IF USER HAS ACCESS ON THIS MODULE
        ! IF USER DONT HAVE ACCESS ON THE SYSTEM, EDI WALA
    */
        Route::get('check_access', [CommonController::class, 'check_access']);


    Route::get('/{any}', function () {
        return view('welcome');
    })->where('any', '.*');
});
