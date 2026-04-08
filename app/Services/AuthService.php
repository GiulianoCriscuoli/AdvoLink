<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Laravel\Socialite\Facades\Socialite;
use Exception;
class AuthService
{
    public function register(array $data): User
    {
        $repositoryUser = new UserRepository(new User());

        $data['password'] = Hash::make($data['password']);
        $user = $repositoryUser->create($data);

        return $user;
    }

    public function login(array $data): User
    {
        $user = new UserRepository(new User());
        $user = $user->findByEmail($data['email']);

        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw new Exception('Credenciais inválidas');
        }

        return $user;
    }

    protected function findByEmail(string $email): ?User
    {
        $user = new UserRepository(new User());
        return $user->findByEmail($email);
    }

    public function generateToken(User $user): string
    {
        return $user->createToken('auth_token')->plainTextToken;
    }

    public function redirectToSocialite()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleSocialiteCallback(): array
    {
        $googleUser = Socialite::driver('google')->user();

        $data = [
            'name' => $googleUser->getName(),
            'email' => $googleUser->getEmail(),
            'google_id' => $googleUser->getId(),
            'avatar' => $googleUser->getAvatar(),
        ];

        $user = new UserRepository(new User());
        $user = $user->updateOrCreate($data);

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token
        ];
    }
}
