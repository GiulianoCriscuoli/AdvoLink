<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

class SanctumCookieAuth
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->cookie('auth_token');

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Token não informado.'
            ], 401);
        }

        $accessToken = PersonalAccessToken::findToken($token);

        if (!$accessToken) {
            return response()->json([
                'success' => false,
                'message' => 'Token inválido ou expirado.'
            ], 401);
        }

        auth()->setUser($accessToken->tokenable);

        $accessToken->tokenable->withAccessToken($accessToken);

        return $next($request);
    }
}
