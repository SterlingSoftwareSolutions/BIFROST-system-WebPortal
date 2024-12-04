<?php

use App\Http\Controllers\AccessController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientManagementController;
use App\Http\Controllers\CommunicationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FinancialController;
use App\Http\Controllers\MobileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;



use App\Http\Controllers\MailController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\StatisticsController;
use App\Http\Controllers\UserAchievementsController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\UserGoalController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\UserSettingController;
use App\Http\Controllers\WorkoutLibraryController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect('login');
});
// Display the login form
Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
// login
Route::post('login', [AuthController::class, 'login'])->name('login');
// logout
Route::post('logout', [AuthController::class, 'logout'])->name('logout');
// Display the forget password form
Route::get('forget/password', function () {
    return view('auth.forget-password');
})->name('forget.password');


// reset pin nummber
Route::post('send-forgot-password-email', [MailController::class, 'sendForgotPasswordEmail']);
Route::get('/resetpassword/{token}', [MailController::class, 'resetpassword_index'])->name('resetpassword');
Route::post('frogot-password/new-pin', [MailController::class, 'insert_new_pin'])->name('frogot-password');

// admin login
Route::middleware(['admin'])->group(function () {
    Route::get('/admin-dashboard', function () {
        return redirect('/admin/dashboard');
    })->name('admin.dashboard');
});

// user login
Route::middleware(['auth'])->group(function () {
    Route::get('/user-dashboard', function () {
        // return view user.user.dashboard
        return redirect('/user/dashboard');
    })->name('user.dashboard');
});

// Route to handle unauthorized access
Route::get('/unauthorized', function () {
    return "You are not authorized to access this page.";
});


//admin dashboard route
Route::middleware(['admin'])->group(function () {

    // dashboard
    Route::get('/admin/dashboard', [DashboardController::class, 'viewdashboard'])->name('admindashboard');
    // access page Routes
    Route::get('/admin/access', [AccessController::class, 'viewaccess'])->name('admindaaccess');
    // Change user name and email
    Route::post('/updatedata', [AccessController::class, 'setData']);
    // delete user
    Route::post('/deletedata', [AccessController::class, 'deleteData']);
    // reset pin
    Route::post('/resetpin', [AccessController::class, 'resetPin']);
    // chane access type
    Route::post('/updateaccesstype', [AccessController::class, 'updateAccess']);
    // chane access page
    Route::post('/updateaccesspage', [AccessController::class, 'updateAccessPage']);
    //  next button
    Route::get('/users/{user}', [AccessController::class, 'nextShow'])->name('user.show');
    // previous button
    Route::get('/previous-show/{id}', [AccessController::class, 'previousShow'])->name('previous.show');



    // financial
    Route::get('/admin/financial', [FinancialController::class, 'viewfinancial'])->name('adminfinancial');

    // client management page
    Route::get('/admin/clientmanagement', [ClientManagementController::class, 'viewclientmanagement'])->name('viewadminclientmanagement');
    //fetch data
    Route::get('/save/newclient/{action?}/{id?}', [ClientManagementController::class, 'newProfileclientShow'])->name('addnewclientedit');

    Route::get('/view/newclient/{id?}', [ClientManagementController::class, 'clientview'])->name('clientview');
    //add new client
    Route::post('/save/newclient/add', [ClientManagementController::class, 'addnewclient'])->name('newProfileclientsave');
    //editclient
    Route::post('/save/newclient/edit/{id}', [ClientManagementController::class, 'updatenewclient'])->name('updatenewclient');
    // update profile
    Route::post('/clients/update', [ClientManagementController::class, 'updatenewclient'])->name(('updatenewclient'));
    //delete profile
    Route::delete('/profile/{id}', [ClientManagementController::class, 'deleteProfile'])->name('deleteProfile');
    //edit
    Route::get('/edit/{id}', [ClientManagementController::class, 'editclient'])->name('editclient');
    // Route::get('/edit/{id}', [ClientManagementController::class, 'editclient'])->name('editclient'); function missing


    // store
    Route::post('/clients', [SessionController::class, 'store'])->name('clients.store');
    // Get Category
    Route::post('/getCategory', [SessionController::class, 'getCategory'])->name('getCategory');

    // get data
    Route::post('/class-manager', [SessionController::class, 'getdata']);
    // // get workout
    Route::post('/get-workout', [SessionController::class, 'getworkout']);

    // store warmup
    Route::Post('/store-warmup', [SessionController::class, 'storewarmup']);
    // update warmup
    Route::Post('/update-warmup', [SessionController::class, 'updatewarmup']);
    // delete
    Route::delete('/delete-warmups', [SessionController::class, 'deleteAllBySelectDateWarmups'])->name('warmups.deleteAllBySelectDate');
    // get warmup
    Route::Post('/get-wormup', [SessionController::class, 'getwarmup']);

    // store weightlifting
    Route::post('/store-weightlifting', [SessionController::class, 'storeweightlifting']);
    // get Weightlifting
    Route::post('/get-Weightlifting', [SessionController::class, 'getWeightlifting']);
    // update Weightlifting
    Route::post('/update-Weightlifting', [SessionController::class, 'updateWeightlifting'])->name('updateWeightlifting');
    // delete
    Route::delete('/delete-Weightlifting', [SessionController::class, 'deleteAllBySelectDateWeightlifting'])->name('Weightlifting.deleteAllBySelectDate');

    // store conditioning
    Route::post('/store-conditioning', [SessionController::class, 'storeconditioning']);
    // get conditioning
    Route::post('/getConditioning', [SessionController::class, 'getConditioning']);
    // update conditioning
    Route::post('/update-conditioning', [SessionController::class, 'updateConditioning']);
    // delete conditioning
    Route::post('/delete-conditioning', [SessionController::class, 'deleteConditioning']);


    //stroe strenght
    Route::post('/save-strength', [SessionController::class, 'strengthstore'])->name('save-strength');
    Route::post('/get-strengthdata', [SessionController::class, 'getstrength'])->name('get-strength');
    Route::post('/update-strenthdata', [SessionController::class, 'updatestrength'])->name("updatestrength");
    // Route::get('/update-strenthdata',[SessionController::class,'updatestrength' ])->name("updatestrength");
    Route::delete('/delete-strengthdata', [SessionController::class, 'deleteAllByDelectDataStrenght'])->name('deletestrength');

    //store test
    //getmembers
    Route::post('/get-members', [SessionController::class, 'getmember'])->name('getmembers');
    // storetest
    Route::post('/save-test', [SessionController::class, 'storeTest'])->name('storeTest');
    //gettestdata
    Route::post('/get-testdata', [SessionController::class, 'gettest'])->name('get-testdata');
    //testupdate
    Route::post('/update-test', [SessionController::class, 'updatest'])->name('update-test');
    //testdelete
    Route::delete('/delete-test', [SessionController::class, 'deletealldatatest'])->name('delete-test');



    // store
    Route::post('/client-update', [SessionController::class, 'update'])->name('clients.update');

    //workout librabry
    Route::get('/admin/workoutlibrary', [WorkoutLibraryController::class, 'viewworkoutlibrary'])->name('viewworkoutlibrary');
    Route::post('/save-workout-library', [WorkoutLibraryController::class, 'listworkoutlibrary'])->name('save.workoutlibrary');
    Route::delete('/workout-library/{id}', [WorkoutLibraryController::class, 'delete'])->name('workout-library.delete');

    // session
    Route::get('/admin/session', [SessionController::class, 'viewsession'])->name('viewsession');
    // communication
    Route::get('/admin/communication', [CommunicationController::class, 'viewcommunication'])->name('viewviewcommunication');
    //statistic
    Route::get('/admin/statistics', [StatisticsController::class, 'viewstatistics'])->name('viewviewstatistics');
});

//user dashboard route
Route::middleware(['user'])->group(function () {
    // dashboard
    Route::get('/user/dashboard', [UserDashboardController::class, 'viewdashboard'])->name('userdashboard');
    // Find exercise
    Route::POST('/find-data',[UserDashboardController::class,'getData'])->name('find-data');
    Route::post('/users/search', [UserDashboardController::class, 'search'])->name('users.search');


    // profile
    Route::get('/user/profile', [UserProfileController::class, 'viewprofile'])->name('userprofile');
    // monthly image Store
    Route::post('/monthly-images', [UserProfileController::class, 'store'])->name('monthly_images.store');
    // get Prev Image and Next Image
    Route::post('/prevdata', [UserProfileController::class, 'handlePrevData'])->name('prevdata');
    Route::post('/nextvdata', [UserProfileController::class, 'handleNextData'])->name('nextvdata');
    // Add new Profile
    Route::get('/user/new-profile', [UserProfileController::class, 'viewnewprofile'])->name('usernewprofile');
    Route::post('/profiles', [UserProfileController::class, 'newProfileShow'])->name('profiles.store');
    //achievements
    Route::get('/user/achivements', [UserAchievementsController::class, 'viewachievement'])->name('userachievements');
    //goal
    Route::get('/user/goal', [UserGoalController::class, 'viewgoal'])->name('usergoal');
    Route::post('/user/save-goal', [UserGoalController::class, 'saveGoal'])->name('save.goal');
    //setting
    Route::get('/user/setting', [UserSettingController::class, 'viewsetting'])->name('usersetting');
});


//mobile route
Route::get('/mobile/login', [MobileController::class, 'login'])->name('mobile.login');
Route::get('/mobile/forgetpin', [MobileController::class, 'forgetpin'])->name('mobile.pin');

Route::get('/mobile/trainingday', [MobileController::class, 'trainingday'])->name('mobile.trainingday');
Route::post('/select-day',  [MobileController::class, 'selectday']);

Route::get('/mobile/readinessscore', [MobileController::class, 'readinessscore'])->name('mobile.readinessscore');
Route::post('/mobile/store-readiness', [MobileController::class, 'storescore'])->name('mobile.storescore');

// get workout data
Route::post('/mobile/class-manager', [ClientManagementController::class, 'getdata'])->name('mobile.class-manager');
Route::get('/mobile/workout', [MobileController::class, 'workout'])->name('mobile.workout');
Route::post('/warmup-daily',  [MobileController::class, 'storewarmupdaily']);
Route::put('/warmup-daily/{id}', [MobileController::class, 'storewarmupdaily'])->name('warmup.daily.update');

Route::post('/save-strength-workout',  [MobileController::class, 'storestrengthdaily']);

Route::get('/mobile/workouttimer', [MobileController::class, 'workouttimer'])->name('mobile.workouttimer');
Route::get('/mobile/histroyview', [MobileController::class, 'histroyview'])->name('mobile.histroyview');

// Display the forget password form
Route::get('forgot/password', function () {
    return view('mobile.auth.forgetpin');
})->name('mobileforget.password');

Route::post('send-forgot-password-email', [MailController::class, 'sendForgotPasswordEmail']);
Route::get('/resetpassword/{token}', [MailController::class, 'resetpassword_index'])->name('resetpassword');
Route::post('frogot-password/new-pin', [MailController::class, 'insert_new_pin'])->name('frogot-password');
