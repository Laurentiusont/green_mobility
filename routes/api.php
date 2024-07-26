<?php

use App\Http\Controllers\MerchantLocationController;
use App\Http\Controllers\MerchantMasterController;
use App\Http\Controllers\OtpController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\JarInputController;
use App\Http\Controllers\ParkingLotController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserFormController;
use Illuminate\Support\Facades\Route;

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

$version = "v1/";
$url = $version;

Route::group([
    'prefix' => $url . 'auth',
    'middleware' => 'api',
], function ($router) {
    $router->post('/register', [AuthController::class, 'register'])->name('register');
    $router->post('/login', [AuthController::class, 'login'])->name('login');
    $router->post('/login-google', [AuthController::class, 'loginGoogle'])->name('login-google');
    $router->post('/verify-google', [AuthController::class, 'verifyGoogle']);
});

/**
 * FORGOT PASSWORD
 */
Route::group([
    'prefix' => $url,
    'middleware' => 'api',
], function ($router) {
    $router->post('forgot-password/generate-otp', [OtpController::class, 'generateOtp']);
    $router->post('forgot-password/validate-otp', [OtpController::class, 'validateOtp']);
    $router->post('/check-otp', [OtpController::class, 'checkOtp']);
    $router->post('/send-otp', [OtpController::class, 'verificationOtp']);
    $router->post('/reset-password', [PasswordController::class, 'resetPassword']);
});

Route::group([
    'prefix' => $url . 'auth',
    'middleware' => 'jwt.verify',
], function ($router) {
    $router->post('/logout', [AuthController::class, 'logout']);
});

/**
 * PROFILE
 */
Route::group([
    'prefix' => $url . 'user',
    'middleware' => 'jwt.verify'
], function ($router) {
    $router->get('/self', [UserController::class, 'index']);
    // $router->put('/update', [ProfileController::class, 'updateUser']);
    $router->put('/change-password', [PasswordController::class, 'changePassword']);
    // $router->put('/update-fcm-token', [FcmController::class, 'updateFcmToken']);
    $router->get('/', [UserController::class, 'showData']);
    $router->get('/{guid}', [UserController::class, 'getData']);
    $router->put('/', [UserController::class, 'updateData']);
    $router->delete('/{guid}', [UserController::class, 'deleteData']);
    $router->post('/', [UserController::class, 'insertData']);
    $router->post('/sync-google', [AuthController::class, 'syncGoogle']);
});

/**
 * FORM
 */
Route::group([
    'prefix' => $url,
    'middleware' => 'jwt.verify'
], function ($router) {
    $router->get('/form', [JarInputController::class, 'index']);
    $router->get('/download/{filename}', [JarInputController::class, 'download']);
    $router->get('/form/datatable', [JarInputController::class, 'getAllDataTable']);
    $router->get('/form/{guid}', [JarInputController::class, 'getData']);
    $router->post('/form', [JarInputController::class, 'insertData']);
    $router->delete('/form/{guid}', [JarInputController::class, 'deleteData']);
});

/**
 * MERCHANT MASTER CONTROLLER
 */
Route::group([
    'prefix' => $url,
    'middleware' => 'jwt.verify'
], function ($router) {
    $router->get('/merchantmaster', [MerchantMasterController::class, 'index']);
    $router->get('/merchantmaster/datatable', [MerchantMasterController::class, 'getAllDataTable']);
    $router->get('/merchantmaster/{guid}', [MerchantMasterController::class, 'getData']);
    $router->post('/merchantmaster', [MerchantMasterController::class, 'insertData']);
    $router->put('/merchantmaster', [MerchantMasterController::class, 'updateData']);
    $router->delete('/merchantmaster/{guid}', [MerchantMasterController::class, 'deleteData']);
});

/**
 * MERCHANT LOCATION CONTROLLER
 */
Route::group([
    'prefix' => $url,
    'middleware' => 'jwt.verify'
], function ($router) {
    $router->get('/merchantlocation', [MerchantLocationController::class, 'index']);
    $router->get('/merchantlocation/datatable', [MerchantLocationController::class, 'getAllDataTable']);
    $router->get('/merchantlocation/{guid}', [MerchantLocationController::class, 'getData']);
    $router->post('/merchantlocation', [MerchantLocationController::class, 'insertData']);
    $router->put('/merchantlocation', [MerchantLocationController::class, 'updateData']);
    $router->delete('/merchantlocation/{guid}', [MerchantLocationController::class, 'deleteData']);
});

/**
 * PARKING LOT CONTROLLER
 */
Route::group([
    'prefix' => $url,
    'middleware' => 'jwt.verify'
], function ($router) {
    $router->get('/parkinglot', [ParkingLotController::class, 'index']);
    $router->get('/parkinglot/datatable', [ParkingLotController::class, 'getAllDataTable']);
    $router->get('/parkinglot/{guid}', [ParkingLotController::class, 'getData']);
    $router->post('/parkinglot', [ParkingLotController::class, 'insertData']);
    $router->put('/parkinglot', [ParkingLotController::class, 'updateData']);
    $router->delete('/parkinglot/{guid}', [ParkingLotController::class, 'deleteData']);
});

