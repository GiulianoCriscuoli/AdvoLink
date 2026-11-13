<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateUsernameRequest;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(
        private UserRepository $userRepository
    ) {}

    public function me(Request $request)
    {
        return response()->json([
            'user' => $request->user(),
            'needs_username' => $request->user()->username === null,
        ]);
    }

    public function updateUsername(UpdateUsernameRequest $request)
    {
        $user = $this->userRepository->update($request->user()->id, $request->validated());

        return response()->json([
            'message' => 'Nome de usuário atualizado com sucesso',
            'user' => $user,
        ]);
    }
}
