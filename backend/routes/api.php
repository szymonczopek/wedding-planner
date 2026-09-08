<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/users', function () {
    return DB::table('users')->get();
})/* ->middleware('auth:sanctum') */;

Route::get('/health', function () {
    return response()->json([
        'status' => 'okkkk',
        'php' => PHP_VERSION,
        'laravel' => app()->version(),
        'db' => DB::connection()->getDriverName(),
        'time' => now()->toIso8601String(),
    ]);
});
