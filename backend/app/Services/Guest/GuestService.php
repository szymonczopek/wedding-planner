<?php

namespace App\Services\Guest;

use App\DTO\Address\CreateAddressDTO;
use App\DTO\Guest\CreateGuestDTO;
use App\Http\Requests\Guest\CreateGuestFormRequest;
use App\Models\Address;
use App\Models\Guest;
use Illuminate\Support\Facades\DB;

class GuestService
{
    public function createGuest(CreateGuestFormRequest $request): Guest
    {
        $data = $request->validated();

        return DB::transaction(function () use ($data) {
            $createGuestDTO = CreateGuestDTO::fromRequest($data);

            $guest = Guest::create($createGuestDTO->toArray());

            if (isset($data['address_id'])) {
                $guest->address()->associate($data['address_id']);
            } else {
                $address = Address::create(CreateAddressDTO::fromRequest($data)->toArray());
                $guest->address()->associate($address);
            }

            $guest->save();

            return $guest;
        });
    }
}
