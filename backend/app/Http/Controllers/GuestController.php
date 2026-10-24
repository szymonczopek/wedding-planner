<?php

namespace App\Http\Controllers;

use App\Http\Requests\Guest\CreateGuestFormRequest;
use App\Services\Guest\GuestService;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Guest', description: 'Operacje na gościach')]
class GuestController extends Controller
{
    private GuestService $guestService;

    public function __construct(GuestService $guestService)
    {
        $this->guestService = $guestService;
    }

    /**
     * @throws \Throwable
     */
    #[OA\Post(
        path: '/api/guests/create',
        summary: 'Dodaj gościa',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: [
                    'name',
                    'surname',
                    'acquaintance_type',
                    'child',
                    'groom_guest',
                    'invitation_prepared',
                    'invitation_delivered',
                    'confirmed_attendance',
                    'diet',
                    'wedding_id',
                ],
                properties: [
                    new OA\Property(
                        property: 'address_id',
                        description: 'ID istniejącego adresu. Jeśli podano, dane adresowe nie są wymagane.',
                        type: 'string',
                        format: 'uuid',
                        example: '9d1f2c3b-4a5e-6f70-8a9b-0c1d2e3f4a5b',
                    ),
                    new OA\Property(
                        property: 'street',
                        description: 'Wymagane tylko gdy nie podano address_id.',
                        type: 'string',
                        example: 'Kwiatowa',
                    ),
                    new OA\Property(
                        property: 'house_number',
                        description: 'Wymagane tylko gdy nie podano address_id.',
                        type: 'string',
                        example: '12',
                    ),
                    new OA\Property(
                        property: 'local_number',
                        description: 'Wymagane tylko gdy nie podano address_id.',
                        type: 'string',
                        example: '3',
                        nullable: true,
                    ),
                    new OA\Property(
                        property: 'city',
                        description: 'Wymagane tylko gdy nie podano address_id.',
                        type: 'string',
                        example: 'Warszawa',
                    ),
                    new OA\Property(
                        property: 'postal_code',
                        description: 'Wymagane tylko gdy nie podano address_id.',
                        type: 'string',
                        example: '00-001',
                    ),
                    new OA\Property(
                        property: 'country',
                        description: 'Wymagane tylko gdy nie podano address_id.',
                        type: 'string',
                        example: 'Polska',
                    ),
                    new OA\Property(
                        property: 'name',
                        type: 'string',
                        example: 'Anna',
                    ),
                    new OA\Property(
                        property: 'surname',
                        type: 'string',
                        example: 'Kowalska',
                    ),
                    new OA\Property(
                        property: 'acquaintance_type',
                        description: 'Rodzaj znajomości',
                        type: 'string',
                        example: 'close_friend',
                        enum: [
                            'witness',
                            'close_family',
                            'distant_family',
                            'close_friend',
                            'distant_friend',
                            'neighbor',
                            'plus_one',
                        ],
                    ),
                    new OA\Property(
                        property: 'child',
                        type: 'boolean',
                        example: false,
                    ),
                    new OA\Property(
                        property: 'groom_guest',
                        description: 'Gość pana młodego',
                        type: 'boolean',
                        example: true,
                    ),
                    new OA\Property(
                        property: 'invitation_prepared',
                        type: 'boolean',
                        example: false,
                    ),
                    new OA\Property(
                        property: 'invitation_delivered',
                        type: 'boolean',
                        example: false,
                    ),
                    new OA\Property(
                        property: 'confirmed_attendance',
                        type: 'boolean',
                        example: false,
                    ),
                    new OA\Property(
                        property: 'diet',
                        type: 'string',
                        example: 'vegetarian',
                        enum: [
                            'vegetarian',
                            'vegan',
                            'keto',
                            'light',
                            'gluten_free',
                            'lactose_free',
                            'nut_free',
                            'fish_and_seafood_free',
                            'halal',
                            'kosher',
                            'kids_menu',
                        ],
                    ),
                    new OA\Property(
                        property: 'wedding_id',
                        description: 'ID wesela',
                        type: 'string',
                        format: 'uuid',
                        example: '1a2b3c4d-5e6f-7a8b-9c0d-1e2f3a4b5c6d',
                    ),
                    new OA\Property(
                        property: 'main_guest_id',
                        description: 'ID głównego gościa, do którego przypisana jest osoba towarzysząca',
                        type: 'string',
                        format: 'uuid',
                        example: '2b3c4d5e-6f7a-8b9c-0d1e-2f3a4b5c6d7e',
                    ),
                ],
            ),
        ),
        tags: ['Guest'],
        responses: [
            new OA\Response(
                response: 201,
                description: 'Pomyślnie utworzono gościa',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'id', type: 'string', format: 'uuid', example: '3c4d5e6f-7a8b-9c0d-1e2f-3a4b5c6d7e8f'),
                        new OA\Property(property: 'wedding_id', type: 'string', format: 'uuid', example: '1a2b3c4d-5e6f-7a8b-9c0d-1e2f3a4b5c6d'),
                        new OA\Property(property: 'name', type: 'string', example: 'Anna'),
                        new OA\Property(property: 'surname', type: 'string', example: 'Kowalska'),
                        new OA\Property(property: 'acquaintance_type', type: 'string', example: 'close_friend'),
                        new OA\Property(property: 'child', type: 'boolean', example: false),
                        new OA\Property(property: 'groom_guest', type: 'boolean', example: true),
                        new OA\Property(property: 'diet', type: 'string', example: 'vegetarian'),
                        new OA\Property(property: 'table_position_x', type: 'number', format: 'float', example: 100.5),
                        new OA\Property(property: 'table_position_y', type: 'number', format: 'float', example: 200.5),
                    ],
                ),
            ),
            new OA\Response(response: 401, description: 'Brak autoryzacji'),
            new OA\Response(
                response: 422,
                description: 'Błąd walidacji',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Wypełnij to pole'),
                        new OA\Property(
                            property: 'errors',
                            type: 'object',
                            example: ['name' => ['Wypełnij to pole']],
                        ),
                    ],
                ),
            ),
        ],
    )]
    public function create(CreateGuestFormRequest $request): JsonResponse
    {
        return response()->json($this->guestService->createGuest($request), 201);
    }
}
