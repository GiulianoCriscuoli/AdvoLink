<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Illuminate\Http\Request;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\LoginRequest;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $data = $request->validated();

        $authService = new AuthService();

        $user  = $authService->register($data);
        $token = $authService->generateToken($user);

        return response()->json([
            'message' => 'Usuário registrado com sucesso',
            'user' => $user,
        ], 201)->cookie(
            'auth_token',
            $token,
            60 * 24 * 7,
            '/',
            null,
            false,
            true,
            false,
            'Lax'
        );
    }

    public function login(LoginRequest $request)
    {
        $data = $request->validated();

        $authService = new AuthService();

        $user = $authService->login($data);
        $token = $authService->generateToken($user);

        return response()->json([
            'message' => 'Login bem-sucedido',
            ])->cookie(
                'auth_token',
                $token,
                60 * 24 * 7,
                '/',
                null,
                false,
                true,
                false,
                'Lax'
            );
    }
    public function logout(Request $request)
    {
        $token = $request->cookie('auth_token');

        if ($token) {
            $accessToken = PersonalAccessToken::findToken($token);

            if ($accessToken) {
                $accessToken->delete();
            }
        }

        return response()->json([
            'message' => 'Logout realizado'
        ])->withoutCookie('auth_token');
    }

    public function redirectToSocialite()
    {
        $authService = new AuthService();
        return $authService->redirectToSocialite();
    }

    public function handleSocialiteCallback()
    {
        $authService = new AuthService();
        $response = $authService->handleSocialiteCallback();

        return redirect(env('APP_URL') . "/auth/callback?token={$response['token']}");
    }
}
