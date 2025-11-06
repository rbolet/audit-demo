<?php

use App\Http\Controllers\Api\HealthCheckController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Health check endpoint
Route::get('/health', HealthCheckController::class);

// Example authenticated route (for future use)
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
