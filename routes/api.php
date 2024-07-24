<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\OCRController;
use App\Http\Controllers\Api\PasswordController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\WhatsAppController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

$version = "v1/";
$url = $version;

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


/**
 * AUTHENTICATION
 */

Route::group([
    'prefix' => $url . 'auth',
    'middleware' => 'api',
], function ($router) {
    $router->post('/login', [AuthController::class, 'loginUser'])->name('login');
});

/**
 * PROFILE
 */
Route::group([
    'prefix' => $url . 'user',
    'middleware' => 'jwt.verify'
], function ($router) {
    $router->get('/self', [ProfileController::class, 'getUser']);
    $router->put('/update', [ProfileController::class, 'updateUser']);
    $router->put('/change-password', [PasswordController::class, 'changePassword']);
});

Route::group([
    'prefix' => $url,
    // 'middleware' => 'jwt.verify',
], function ($router) {
    $router->post('/webhook', [WhatsAppController::class, 'handleWebhook']);
});

Route::group([
    'prefix' => $url,
    // 'middleware' => 'jwt.verify',
], function ($router) {
    $router->post('/ocr/upload', [OCRController::class, 'upload']);
});
