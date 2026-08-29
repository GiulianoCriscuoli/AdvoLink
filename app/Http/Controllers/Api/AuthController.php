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

    public function __construct(
        private AuthService $authService
    ) {}

    public function register(RegisterRequest $request)
    {
        $data = $request->validated();

        $user  = $this->authService->register($data);
        $token = $this->authService->generateToken($user);

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

        $user = $this->authService->login($data);
        $token = $this->authService->generateToken($user);

        return response()->json([
            'message' => 'Login bem-sucedido',
            'user' => $user,
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
        return $this->authService->redirectToSocialite();
    }

    public function handleSocialiteCallback()
    {
        $response = $this->authService->handleSocialiteCallback();

        return redirect(config('app.frontend_url') . "/auth/callback?token={$response['token']}");
    }
}
