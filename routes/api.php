<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MobileController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::post('mobilelogin', [AuthController::class, 'mobilelogin'])->name('mobilelogin');

Route::group(['middleware' => ['auth:sanctum']], function () {

    Route::get('/getrainingdaysnclasses', [MobileController::class, 'getrainingdaysnclasses']);
    //class reserve
    Route::post('reserve', [MobileController::class, 'reserve'])->name('class.reserve');
    Route::post('/cancel', [MobileController::class, 'cancel'])->name('class.cancel');

});
