<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\WeddingController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/health', function () {
    return response()->json([
        'status' => 'okkkk',
        'php' => PHP_VERSION,
        'laravel' => app()->version(),
        'db' => DB::connection()->getDriverName(),
        'time' => now()->toIso8601String(),
    ]);
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'authUser']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::prefix('/weddings')->group(function () {
        Route::post('/create', [WeddingController::class, 'create']);
    });
});
