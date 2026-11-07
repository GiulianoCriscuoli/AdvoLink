<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class AuthService
{
    public function __construct(
        private UserRepository $userRepository
    ) {}

    public function register(array $data): User
    {
        if ($this->findByEmail($data['email'])) {
            throw ValidationException::withMessages([
                'email' => 'O email já está em uso.',
            ]);
        }

        $data['password'] = Hash::make($data['password']);

        return $this->userRepository->create($data);
    }

    public function login(array $data): User
    {
        $user = $this->userRepository->findByEmail($data['email']);

        if (!$user || !Hash::check($data['password'], $user->password)) {
            abort(401, 'Credenciais inválidas ou login não encontrado! Tente novamente.');
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
            abort(401, 'Erro no login com Google');
        }

        if (!$googleUser->getEmail() ||
            !($googleUser->user['email_verified'] ?? false)) {
            abort(401, 'O Google não forneceu um email válido ou o email não foi verificado.');
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
