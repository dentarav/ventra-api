<?php

use App\Http\Controllers\AssetController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        
        Route::apiResource('assets', AssetController::class);
        Route::post('transactions/borrow', [TransactionController::class, 'borrow']);
        Route::put('profile', [AuthController::class, 'updateProfile']);

        Route::middleware('admin')->group(function () {
            Route::apiResource('categories', CategoryController::class);
            Route::apiResource('locations', LocationController::class);
            Route::get('assets/report', [AssetController::class, 'report']);
        });
    });
});