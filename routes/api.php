<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;

Route::post('/cadastrar', [AuthController::class, 'register'])->middleware('throttle:5,1');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');

Route::get('/auth/google', [AuthController::class, 'redirectToSocialite']);
Route::get('/auth/google/callback', [AuthController::class, 'handleSocialiteCallback']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/me', [UserController::class, 'me']);
    Route::patch('/username', [UserController::class, 'updateUsername']);
    Route::post('/logout', [AuthController::class, 'logout']);
});
