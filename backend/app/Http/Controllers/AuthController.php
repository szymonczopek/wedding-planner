<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginFormRequest;
use App\Http\Requests\Auth\RegisterFormRequest;
use App\Services\Auth\AuthLoginService;
use App\Services\Auth\AuthRegisterService;
use App\Services\User\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    description: 'API',
    title: 'Wedding Planner Laravel API'
)]
#[OA\SecurityScheme(
    securityScheme: 'sanctum',
    type: 'http',
    description: 'Token Sanctum w nagłówku Authorization: Bearer {token}',
    bearerFormat: 'JWT',
    scheme: 'bearer'
)]
#[OA\Tag(name: 'Auth', description: 'Rejestracja i logowanie')]
class AuthController extends Controller
{
    private AuthRegisterService $registerService;

    private AuthLoginService $loginService;

    private UserService $userService;

    public function __construct(AuthRegisterService $registerService, AuthLoginService $loginService, UserService $userService)
    {
        $this->registerService = $registerService;
        $this->loginService = $loginService;
        $this->userService = $userService;
    }

    #[OA\Post(
        path: '/api/register',
        summary: 'Endpoint testowy',
        tags: ['Auth'],
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

    public function authUserWithWeddings(Request $request): JsonResponse
    {
        return response()->json($request->user()->load('weddings'));
    }

    public function authUserWedding(Request $request): JsonResponse
    {
        return response()->json($this->userService->userWithSelectedWedding($request), 200);
    }
}
