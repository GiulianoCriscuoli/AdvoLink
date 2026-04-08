<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Illuminate\Http\Request;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\LoginRequest;



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
            'token' => $token
        ], 201);
    }

    public function login(LoginRequest $request)
    {
        $data = $request->validated();

        $authService = new AuthService();

        $user = $authService->login($data);
        $token = $authService->generateToken($user);

        return response()->json([
            'message' => 'Login bem-sucedido',
            'token' => $token
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout realizado com sucesso'
        ]);
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

        $redirect = redirect(env('APP_URL') . "/auth/callback?token={$response['token']}");

        return response()->json([
            'message' => 'Login com Google realizado com sucesso',
            'user' => $response['user'],
            'token' => $response['token'],
            'redirect' => $redirect->getTargetUrl()
        ], 200);
    }
}
