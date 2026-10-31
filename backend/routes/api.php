<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\WeddingController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'php' => PHP_VERSION,
        'laravel' => app()->version(),
        'db' => DB::connection()->getDriverName(),
        'time' => now()->toIso8601String(),
    ]);
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user-with-weddings', [AuthController::class, 'authUserWithWeddings']);
    Route::get('/wedding-user', [AuthController::class, 'authUserWedding']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::prefix('/weddings')->group(function () {
        Route::post('/create', [WeddingController::class, 'create']);
        Route::post('/join', [WeddingController::class, 'join']);
        Route::post('/choose', [WeddingController::class, 'choose']);
    });

    Route::prefix('/guests')->group(function () {
        Route::get('/', [GuestController::class, 'index']);
        Route::get('/list', [GuestController::class, 'list']);
        Route::post('/create', [GuestController::class, 'create']);
    });

    Route::get('/addresses/search', [AddressController::class, 'search']);
    Route::get('/addresses/autocomplete', [AddressController::class, 'autocomplete']);
});
