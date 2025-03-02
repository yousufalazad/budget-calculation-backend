<?php

use App\Http\Controllers\TransactionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MonthController;
use App\Http\Controllers\RecurringTypeController;
use App\Http\Controllers\AuthController;

// Public Routes (No Authentication Required)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected Routes (Require Authentication)
Route::middleware('auth:sanctum')->group(function () {
    // User Information
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Month Routes
    Route::prefix('month')->group(function () {
        Route::get('/', [MonthController::class, 'index']);
        Route::post('/', [MonthController::class, 'store']);
        Route::put('/{id}', [MonthController::class, 'update']);
        Route::delete('/{id}', [MonthController::class, 'destroy']);
    });

    // Recurring Type Routes
    Route::prefix('recurring-type')->group(function () {
        Route::get('/', [RecurringTypeController::class, 'index']);
        Route::post('/', [RecurringTypeController::class, 'store']);
        Route::put('/{id}', [RecurringTypeController::class, 'update']);
        Route::delete('/{id}', [RecurringTypeController::class, 'destroy']);
    });

    Route::prefix('transactions')->group(function () {
        Route::get('/', [TransactionController::class, 'index']);
        Route::post('/', [TransactionController::class, 'store']);
        Route::put('/{id}', [TransactionController::class, 'update']);
        Route::delete('/{id}', [TransactionController::class, 'destroy']);
    });

    // Logout Route
    Route::post('/logout', [AuthController::class, 'logout']);
});
