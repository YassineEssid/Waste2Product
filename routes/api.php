<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Api\WasteItemController;

// Public API routes
Route::post('/register', [AuthController::class, 'apiRegister']);
Route::post('/login', [AuthController::class, 'apiLogin']);

// Protected API routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'apiLogout']);
    Route::get('/me', [AuthController::class, 'me']);
    
    // Waste Items API
    Route::get('/waste-items/nearby/{lat}/{lng}', [WasteItemController::class, 'nearby']);
});