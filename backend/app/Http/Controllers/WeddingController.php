<?php

namespace App\Http\Controllers;

use App\Http\Requests\WeddingFormRequest;
use App\Services\WeddingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use OpenApi\Attributes as OA;

// #[OA\Info(
//    version: '1.0.0',
//    description: 'API',
//    title: 'Wedding Planner Laravel API'
// )]
class WeddingController extends Controller
{
    private WeddingService $weddingService;

    public function __construct(WeddingService $weddingService)
    {
        $this->weddingService = $weddingService;
    }

    #[OA\Post(
        path: '/api/weddings/create',
        summary: 'Endpoint testowy',
        responses: [
            new OA\Response(response: 200, description: 'OK'),
        ]
    )]
    public function create(WeddingFormRequest $request): JsonResponse
    {
        return response()->json($this->weddingService->createWedding($request), 201);
    }
}
