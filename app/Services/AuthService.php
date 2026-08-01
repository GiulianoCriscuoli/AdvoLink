<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Laravel\Socialite\Facades\Socialite;
use Exception;
use Throwable;

class AuthService
{
    public function __construct(
        private UserRepository $userRepository
    ) {}

    public function register(array $data): User
    {
        if ($this->findByEmail($data['email'])) {
            throw new Exception('Email já registrado');
        }

        $data['password'] = Hash::make($data['password']);

        return $this->userRepository->create($data);
    }

    public function login(array $data): User
    {
        $user = $this->userRepository->findByEmail($data['email']);

        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw new Exception('Credenciais inválidas ou login não encontrado! Tente novamente.');
        }

        return $user;
    }

    protected function findByEmail(string $email): ?User
    {
        return $this->userRepository->findByEmail($email);
    }

    public function generateToken(User $user): string
    {
        return $user->createToken('auth_token')->plainTextToken;
    }

    public function redirectToSocialite()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function handleSocialiteCallback(): array
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
        } catch (Throwable $e) {
            throw new Exception('Erro no login com Google');
        }

        if (!$googleUser->getEmail() ||
            !($googleUser->user['email_verified'] ?? false)) {
            throw new Exception('O Google não forneceu um email válido ou o email não foi verificado.');
        }

        $data = [
            'name' => $googleUser->getName(),
            'email' => $googleUser->getEmail(),
            'google_id' => $googleUser->getId(),
            'avatar' => $googleUser->getAvatar(),
        ];

        $user = $this->userRepository->updateOrCreate(['email' => $data['email']], $data);
        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token
        ];
    }
}
