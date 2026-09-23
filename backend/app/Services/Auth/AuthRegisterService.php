<?php

namespace App\Services\Auth;

use App\DTO\User\RegisterUserDTO;
use App\Http\Requests\RegisterFormRequest;
use App\Models\User;

class AuthRegisterService
{
    /**
     * @return array{user: User, token: string}
     */
    public function registerUser(RegisterFormRequest $request): array
    {
        $registerUserDTO = RegisterUserDTO::fromRequest($request->validated());

        /** @var User $user */
        $user = User::create($registerUserDTO->toArray());

        return [
            'user' => $user,
            'token' => $user->createToken('auth-token')->plainTextToken,
        ];
    }
}
