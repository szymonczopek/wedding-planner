<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginFormRequest;
use App\Http\Requests\Auth\RegisterFormRequest;
use App\Services\Auth\AuthLoginService;
use App\Services\Auth\AuthRegisterService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    description: 'API',
    title: 'Wedding Planner Laravel API'
)]
class AuthController extends Controller
{
    private AuthRegisterService $registerService;

    private AuthLoginService $loginService;

    public function __construct(AuthRegisterService $registerService, AuthLoginService $loginService)
    {
        $this->registerService = $registerService;
        $this->loginService = $loginService;
    }

    #[OA\Post(
        path: '/api/register',
        summary: 'Endpoint testowy',
        responses: [
            new OA\Response(response: 200, description: 'OK'),
        ]
    )]
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

    public function authUser(Request $request): JsonResponse
    {
        return response()->json($request->user()->load('weddings'));
    }
}
