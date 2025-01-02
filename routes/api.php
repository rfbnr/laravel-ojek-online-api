<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PengemudiController;
use App\Http\Controllers\Api\MetodePembayaranController;
use App\Http\Controllers\Api\OrderController;

// auth user
Route::post('login', [AuthController::class, 'login']);
Route::post('/user/register', [AuthController::class, 'register']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);

// auth driver
Route::post('driver-login', [PengemudiController::class, 'login']);
Route::post('driver-register', [PengemudiController::class, 'register']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);

// payment method
Route::apiResource('payments', MetodePembayaranController::class);

Route::middleware('auth:sanctum')->group(function () {
    // Logout have to be in auth middleware
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('driver-logout', [PengemudiController::class, 'logout']);

    Route::get('account-info', [AuthController::class, 'getAccountInfo']);

    // Get user data
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // change status driver
    Route::post('activate-driver/{id}', [PengemudiController::class, 'activateDriver']);

    // orders
    Route::post('create-order', [OrderController::class, 'createOrder']);
    Route::post('accept-order/{id}', [OrderController::class, 'acceptOrder']);
    Route::post('pickup-order/{id}', [OrderController::class, 'pickupOrder']);
    Route::post('complete-order/{id}', [OrderController::class, 'completeOrder']);
    Route::post('rate-order/{id}', [OrderController::class, 'rateOrder']);
    Route::get('history-orders', [OrderController::class, 'orderHistory']);
});
