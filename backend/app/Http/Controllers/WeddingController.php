<?php

namespace App\Http\Controllers;

use App\Http\Requests\Wedding\ChooseWeddingFormRequest;
use App\Http\Requests\Wedding\CreateWeddingFormRequest;
use App\Http\Requests\Wedding\JoinWeddingFormRequest;
use App\Services\Wedding\WeddingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Wedding', description: 'Operacje na weselach')]
class WeddingController extends Controller
{
    private WeddingService $weddingService;

    public function __construct(WeddingService $weddingService)
    {
        $this->weddingService = $weddingService;
    }

    /**
     * @throws \Throwable
     */
    #[OA\Post(
        path: '/api/weddings/create',
        summary: 'Endpoint testowy',
        tags: ['Wedding'],
        responses: [
            new OA\Response(response: 200, description: 'OK'),
        ]
    )]
    public function create(CreateWeddingFormRequest $request): JsonResponse
    {
        return response()->json($this->weddingService->createWedding($request), 201);
    }

    #[OA\Post(
        path: '/api/weddings/join',
        summary: 'Dołącz do wesela za pomocą kodu wesela',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['invite_code'],
                properties: [
                    new OA\Property(
                        property: 'invite_code',
                        description: 'Kod wesela',
                        type: 'string',
                        example: 'ABC123',
                    ),
                    new OA\Property(
                        property: 'role',
                        description: 'Rola',
                        type: 'string',
                        example: 'wedding_planner',
                    ),
                ],
            ),
        ),
        tags: ['Wedding'],
        responses: [
            new OA\Response(
                response: 201,
                description: 'Pomyślnie dołączono do wesela',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'name', type: 'string', example: 'Wesele Anny i Jana'),
                        new OA\Property(property: 'date', type: 'string', format: 'date-time', nullable: true, example: '2026-06-20T15:00:00.000000Z'),
                        new OA\Property(property: 'invite_code', type: 'string', example: 'ABC123'),
                        new OA\Property(property: 'selected', type: 'boolean', example: true),
                    ],
                ),
            ),
            new OA\Response(response: 401, description: 'Brak autoryzacji'),
            new OA\Response(
                response: 422,
                description: 'Błąd walidacji',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'invite_code',
                            type: 'array',
                            items: new OA\Items(type: 'string', example: 'Nie istnieje taki kod wesela'),
                        ),
                    ],
                ),
            ),
        ],
    )]
    public function join(JoinWeddingFormRequest $request): JsonResponse
    {
        return response()->json($this->weddingService->joinToWedding($request), 201);
    }

    public function choose(ChooseWeddingFormRequest $request): JsonResponse
    {
        return response()->json($this->weddingService->chooseWedding($request), 201);
    }
}
