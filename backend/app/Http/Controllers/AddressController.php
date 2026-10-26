<?php

namespace App\Http\Controllers;

use App\Http\Requests\Address\SearchAddressesFormRequest;
use App\Services\Address\AddressService;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Address', description: 'Operacje na adresach')]
class AddressController extends Controller
{
    private AddressService $addressService;

    public function __construct(AddressService $addressService)
    {
        $this->addressService = $addressService;
    }

    #[OA\Get(
        path: '/api/addresses/search',
        summary: 'Wyszukaj adresy wesela',
        security: [['sanctum' => []]],
        tags: ['Address'],
        parameters: [
            new OA\Parameter(
                name: 'wedding_id',
                description: 'ID wesela',
                in: 'query',
                required: true,
                schema: new OA\Schema(type: 'string', format: 'uuid'),
            ),
            new OA\Parameter(
                name: 'q',
                description: 'Miejscowość lub ulica i numer',
                in: 'query',
                required: true,
                schema: new OA\Schema(type: 'string'),
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Lista pasujących adresów',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'id', type: 'string', format: 'uuid'),
                            new OA\Property(property: 'street', type: 'string', example: 'Kwiatowa'),
                            new OA\Property(property: 'house_number', type: 'string', example: '12'),
                            new OA\Property(property: 'local_number', type: 'string', nullable: true, example: '3'),
                            new OA\Property(property: 'city', type: 'string', example: 'Warszawa'),
                            new OA\Property(property: 'postal_code', type: 'string', example: '00-001'),
                            new OA\Property(property: 'country', type: 'string', example: 'Polska'),
                        ],
                    ),
                ),
            ),
            new OA\Response(response: 401, description: 'Brak autoryzacji'),
            new OA\Response(response: 403, description: 'Brak dostępu do wesela'),
            new OA\Response(response: 422, description: 'Błąd walidacji'),
        ],
    )]
    public function search(SearchAddressesFormRequest $request): JsonResponse
    {
        $data = $request->validated();

        return response()->json(
            $this->addressService->searchForWedding($data['wedding_id'], $data['q']),
        );
    }
}
