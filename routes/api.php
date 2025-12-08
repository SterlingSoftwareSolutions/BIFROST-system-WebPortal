<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MobileController;
use App\Http\Controllers\UserMobileController;
use App\Http\Controllers\UserProfileController;

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
Route::post('mobilelogout', [AuthController::class, 'mobilelogout'])->name('mobilelogout');

Route::group(['middleware' => ['auth:sanctum']], function () {

    Route::get('/getrainingdaysnclasses', [MobileController::class, 'getrainingdaysnclasses']);
    //class reserve
    Route::post('reserve', [MobileController::class, 'reserve'])->name('class.reserve');
    Route::post('/cancel', [MobileController::class, 'cancel'])->name('class.cancel');

    Route::post('storescore', [MobileController::class, 'storescoremobile']);
    Route::post('getscore', [MobileController::class, 'getscore']);

    Route::post('getworkout', [MobileController::class, 'getworkout']);
    Route::post('updateweight', [MobileController::class, 'updateWeight']);
    Route::post('insertweight', [MobileController::class, 'insertWeight']);

    //workout complete
    Route::post('/warmup-daily',  [MobileController::class, 'storewarmupdaily']);
    Route::post('/save-strength-workout',  [MobileController::class, 'storestrengthdaily']);
    Route::post('/save-weightligting-workout',  [MobileController::class, 'storeweightliftingdaily']);
    Route::post('/save-conditioning-workout',  [MobileController::class, 'storeconditioningdaily']);

    //profile
    Route::get('profile', [UserMobileController::class, 'viewprofile'])->name('userprofile');
    Route::post('profileimage', [UserMobileController::class, 'profileImageStore']);

    Route::post('monthlyimages-store', [UserMobileController::class, 'store'])->name('monthly_images.store');
    Route::post('nextvdata', [UserProfileController::class, 'handleNextData'])->name('nextvdata');
    Route::get('memberdetails', [UserMobileController::class, 'getMemberProfile'])->name('memberdetails');
    Route::post('update-profile', [UserMobileController::class, 'updateMemberProfile'])->name('updateMemberProfile');

    //achievements
    Route::get('getexercises', [UserMobileController::class, 'getStrengthWorkouts'])->name('getexercise');
    Route::post('getacheivementgraph', [UserMobileController::class, 'getStrengthProgress'])->name('get.strength.details');
    Route::get('getworkouthistory', [UserMobileController::class, 'getMemberWorkoutDetails'])->name('getworkouthistory');
});
