<?php

namespace App\Services\Auth;

use App\Http\Requests\LoginFormRequest;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;

class AuthLoginService
{
    /**
     * @return array{user: User|Authenticatable, token: string}
     */
    public function loginUser(LoginFormRequest $request): array
    {
        if (! Auth::attempt($request->validated())) {
            throw ValidationException::withMessages([
                'email' => ['Incorrect login data.'],
            ]);
        }

        /** @var User $user */
        $user = Auth::user();

        return [
            'user' => $user,
            'token' => $user->createToken('auth-token')->plainTextToken,
        ];
    }

    public function userLogout(Request $request): bool
    {
        $user = $request->user();

        if ($user === null) {
            return false;
        }

        $accessToken = $user->currentAccessToken();

        if ($accessToken instanceof PersonalAccessToken) {
            $accessToken->delete();

            return true;
        }

        $bearerToken = $request->bearerToken();

        if ($bearerToken !== null && str_contains($bearerToken, '|')) {
            [$id, $secret] = explode('|', $bearerToken, 2);

            $user->tokens()
                ->where('id', $id)
                ->where('token', hash('sha256', $secret))
                ->delete();

            return true;
        }

        $guard = Auth::guard('web');

        if ($guard instanceof StatefulGuard) {
            $guard->logout();
        }

        return true;
    }
}
