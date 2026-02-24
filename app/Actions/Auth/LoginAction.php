<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use App\Services\AuthService;
use Illuminate\Validation\ValidationException;

final readonly class LoginAction
{
    public function __construct(
        private AuthService $authService,
    ) {}

    public function execute(array $credentials): array
    {
        $user = $this->authService->attemptLogin($credentials);

        if (! $user) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $this->authService->createToken($user);

        return [
            'user' => $user,
            'token' => $token,
        ];
    }
}
