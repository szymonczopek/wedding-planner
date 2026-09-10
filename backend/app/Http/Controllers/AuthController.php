<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginFormRequest;
use App\Http\Requests\RegisterFormRequest;
use App\Services\Auth\AuthLoginService;
use App\Services\Auth\AuthRegisterService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class AuthController extends Controller
{
    private AuthRegisterService $registerService;

    private AuthLoginService $loginService;

    public function __construct(AuthRegisterService $registerService, AuthLoginService $loginService)
    {
        $this->registerService = $registerService;
        $this->loginService = $loginService;
    }

    public function register(RegisterFormRequest $request): JsonResponse
    {
        return response()->json($this->registerService->registerUser($request), 201);
    }

    public function login(LoginFormRequest $request): JsonResponse
    {
        return response()->json($this->loginService->loginUser($request));
    }

    public function logout(Request $request): JsonResponse
    {
        return response()->json(['success' => $this->loginService->userLogout($request)]);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json($request->user());
    }
}
