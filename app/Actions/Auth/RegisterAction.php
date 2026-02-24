<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use App\Models\User;
use App\Services\AuthService;

final class RegisterAction
{
    public function __construct(
        private readonly AuthService $authService,
    ) {}

    public function execute(array $data): array
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
        ]);

        $token = $this->authService->createToken($user);

        return [
            'user' => $user,
            'token' => $token,
        ];
    }
}
