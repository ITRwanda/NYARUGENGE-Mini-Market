<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthService
{
    /**
     * Register a new user.
     */
    public function register(array $data): array
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => Hash::make($data['password']),
            'role' => $data['role'] ?? 'vendor',
        ]);

        $token = $user->createToken(
            'market-hygiene-api'
        )->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
            'token_type' => 'Bearer',
        ];
    }

    /**
     * Authenticate user.
     */
    public function login(array $credentials): array
    {
        if (!Auth::attempt([
            'email' => $credentials['email'],
            'password' => $credentials['password'],
        ])) {
            throw ValidationException::withMessages([
                'email' => [
                    'The provided credentials are incorrect.'
                ],
            ]);
        }

        $user = User::where(
            'email',
            $credentials['email']
        )->firstOrFail();

        $token = $user->createToken(
            'market-hygiene-api'
        )->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
            'token_type' => 'Bearer',
        ];
    }

    /**
     * Logout user by revoking current token.
     */
    public function logout(User $user): void
    {
        $user->currentAccessToken()?->delete();
    }

    /**
     * Change user's password.
     */
    public function changePassword(
        User $user,
        array $data
    ): void {
        $user->update([
            'password' => Hash::make(
                $data['password']
            ),
        ]);

        /**
         * Revoke all existing tokens so the user
         * must authenticate again.
         */
        $user->tokens()->delete();
    }
}