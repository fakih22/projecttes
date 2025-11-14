<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Controller API
use App\Http\Controllers\Api\CategoryApiController;
use App\Http\Controllers\Api\DonatApiController;
use App\Http\Controllers\Api\TransactionController as ApiTransactionController;
use App\Http\Controllers\MidtransController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;

// ===============================
// MIDTRANS API
// ===============================

Route::post('/midtrans/notification', [ApiTransactionController::class, 'notification']);

// ===============================
// DATA API
// ===============================
Route::get('/categories', [CategoryApiController::class, 'index']);
Route::get('/donats', [DonatApiController::class, 'index']);
Route::get('/donats/{id}', [DonatApiController::class, 'show']);

// ===============================
// TRANSACTION API
// ===============================




// ===============================
// USER AUTH (opsional)
// ===============================
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/transactions', [ApiTransactionController::class, 'store']);
    Route::get('/transactions', [ApiTransactionController::class, 'index']);
});

Route::post('/update-profile', [CustomerController::class, 'updateProfile']);
Route::middleware('auth:sanctum')->post('/update-profile', [CustomerController::class, 'updateProfile']);


