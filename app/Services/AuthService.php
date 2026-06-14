<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function __construct(private readonly UserRepositoryInterface $repo) {}

    public function register(array $data): array
    {
        $user  = $this->repo->create($data);
        $token = $user->createToken('api-token')->plainTextToken;

        return ['token' => $token, 'user' => $user];
    }

    public function login(string $email, string $password): array
    {
        $user = $this->repo->findByEmail($email);

        if (! $user || ! Hash::check($password, $user->password)) {
            throw ValidationException::withMessages(['email' => ['Invalid credentials.']]);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return ['token' => $token, 'user' => $user];
    }

    public function logout(User $user): void
    {
        $user->currentAccessToken()->delete();
    }
}
