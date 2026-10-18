<?php

namespace App\Services\Auth;

use App\Http\Requests\Auth\LoginFormRequest;
use App\Models\User;
use App\Services\Wedding\WeddingService;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\Contracts\HasAbilities;
use Laravel\Sanctum\PersonalAccessToken;

class AuthLoginService
{
    private WeddingService $weddingService;

    public function __construct(WeddingService $weddingService)
    {
        $this->weddingService = $weddingService;
    }

    /**
     * @return array{user: User|Authenticatable, token: string}
     */
    public function loginUser(LoginFormRequest $request): array
    {
        if (! Auth::attempt($request->validated())) {
            throw ValidationException::withMessages([
                'email' => ['Niepoprawne dane logowania'],
            ]);
        }

        /** @var User $user */
        $user = Auth::user();

        return [
            'user' => $user->load('weddings'),
            'token' => $user->createToken('auth-token')->plainTextToken,
        ];
    }

    public function userLogout(Request $request): bool
    {
        $user = $request->user();

        if ($user === null) {
            return false;
        }

        $this->weddingService->unselectWeddings();

        /** @var HasAbilities|null $accessToken */
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

        /** @var Guard|StatefulGuard $guard */
        $guard = Auth::guard('web');

        if ($guard instanceof StatefulGuard) {
            $guard->logout();
        }

        return true;
    }
}
