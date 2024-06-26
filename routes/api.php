<?php

use App\Http\Controllers\MerchantController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\UserController;
use App\Models\User;
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
Route::prefix('v1')->group(function () {
    Route::post('register', [UserController::class, 'store']);
    Route::post('login', [UserController::class, 'login']);
    // Route::post('sendMessage/{mode}', [MerchantController::class, 'sendSMS'])->middleware('auth:sanctum');
    Route::middleware(['auth:sanctum', 'throttle:sendSMS'])->post('sendMessage/{mode}', [MerchantController::class, 'sendSMS']);
    Route::get('generateClient',[UserController::class, 'generateClient']);
    Route::get('generateSecret',[UserController::class, 'generateSecret']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('projects', [ProjectController::class, 'index']);
        Route::post('projects', [ProjectController::class, 'store']);
        Route::get('projects/{id}', [ProjectController::class, 'show']);
        Route::put('projects/{id}', [ProjectController::class, 'update']);
        Route::delete('projects/{id}', [ProjectController::class, 'destroy']);
    });
    Route::post('login/user', [UserController::class, 'login']);
    Route::post('login/merchant', [MerchantController::class, 'merchantLogin']);
//Route for admin
    Route::group(['middleware' => ['auth:sanctum']], function () {
        Route::get('/user', [MerchantController::class, 'index']);
        Route::post('/user/add-merchant', [MerchantController::class, 'store']);
        Route::get('/user/{merchant_no}', [MerchantController::class, 'show']);
        Route::put('/user/edit-merchant/{merchant_no}', [MerchantController::class, 'edit']);
        Route::delete('/user/delete-merchant/{merchant_no}', [MerchantController::class, 'destroy']);
    });
    // End route for admin


    Route::group(['middleware' => ['auth:merchant-api']], function () {
        Route::put('/merchant/update', [MerchantController::class, 'updateByMerchant']);
        Route::middleware(['throttle:sendSMS'])->post('sendMessage/{mode}', [MerchantController::class, 'sendSMS']);
    });
});

