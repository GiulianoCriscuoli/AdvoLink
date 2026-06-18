<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

Route::post('/cadastrar', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/auth/google', [AuthController::class, 'redirectToSocialite']);
Route::get('/auth/google/callback', [AuthController::class, 'handleSocialiteCallback']);

Route::middleware(['cookie.auth'])->group(function () {

    Route::get('/me', function () {

        if (!auth()->check()) {
            return response()->json([
                'message' => 'Não autenticado'
            ], 401);
        }

        return auth()->user();
    });

    Route::post('/logout', [AuthController::class, 'logout']);
});
