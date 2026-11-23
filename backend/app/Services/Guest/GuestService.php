<?php

namespace App\Services\Guest;

use App\DTO\Address\CreateAddressDTO;
use App\DTO\Guest\CreateGuestDTO;
use App\Http\Requests\Guest\CreateGuestFormRequest;
use App\Http\Requests\Guest\ListGuestsFormRequest;
use App\Http\Requests\Guest\UpdateGuestFormRequest;
use App\Models\Address;
use App\Models\Guest;
use App\Models\Wedding;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class GuestService
{
    /**
     * @return Collection<int, Guest>
     */
    public function getGuestsForWedding(): Collection
    {
        return Guest::where('wedding_id', Wedding::selectedWedding())
            ->orderBy('surname')
            ->orderBy('name')
            ->get();
    }

    public function createGuest(CreateGuestFormRequest $request): Guest
    {
        $data = $request->validated();

        return DB::transaction(function () use ($data) {
            $createGuestDTO = CreateGuestDTO::fromRequest($data);

            $guest = Guest::create($createGuestDTO->toArray());

            if (isset($data['address_id'])) {
                $guest->address()->associate($data['address_id']);
            } elseif (isset($data['address'])) {
                $address = Address::create(CreateAddressDTO::fromRequest($data['address'])->toArray());
                $guest->address()->associate($address);
            }

            $guest->save();

            return $guest;
        });
    }

    public function updateGuest(Guest $guest, UpdateGuestFormRequest $request): Guest
    {
        abort_unless($guest->wedding_id === Wedding::selectedWedding(), 403);

        $data = $request->validated();

        return DB::transaction(function () use ($guest, $data) {
            $guest->fill(array_intersect_key($data, array_flip([
                'name',
                'surname',
                'acquaintance_type',
                'child',
                'groom_guest',
                'diet',
                'main_guest_id',
            ])));
            $guest->save();

            $this->updateGuestAddress($data, $guest);

            return $guest->refresh();
        });
    }

    public function updateGuestAddress(array $addressData, Guest $guest): void
    {
        $addressMode = $addressData['address_mode'] ?? null;
        $hasAddress = array_key_exists('address', $addressData);

        if ($addressMode === 'new' && $hasAddress) {
            $oldAddress = $guest->address;

            $newAddress = Address::create(CreateAddressDTO::fromRequest($addressData['address'])->toArray());
            $guest->address()->associate($newAddress);
            $guest->save();

            if($oldAddress->guests()->doesntExist()) $oldAddress->delete();

            return;
        }

        if ($addressMode === 'edit' && $hasAddress) {
            $this->updateOrCreateGuestAddress($guest, $addressData['address']);

            return;
        }

        if (array_key_exists('address_id', $addressData)) {
            $oldAddress = $guest->address;

            $guest->address()->associate($addressData['address_id']);
            $guest->save();

            if($oldAddress->guests()->doesntExist()) $oldAddress->delete();

            return;
        }

        if ($hasAddress) {
            $this->updateOrCreateGuestAddress($guest, $addressData['address']);
        }
    }

    /**
     * @param  array<string, mixed>  $addressData
     */
    private function updateOrCreateGuestAddress(Guest $guest, array $addressData): void
    {
        $address = $guest->address;

        if ($address) {
            $address->update($addressData);

            return;
        }

        $address = Address::create(CreateAddressDTO::fromRequest($addressData)->toArray());
        $guest->address()->associate($address);
        $guest->save();
    }

    /**
     * @return array{data: Collection<int, Address>, total: int}
     */
    public function addressTable(ListGuestsFormRequest $request): array
    {
        $first = $request->integer('first', 0);
        $rowsPerPage = $request->integer('rowsPerPage', 15);
        $weddingId = Wedding::selectedWedding();

        $query = Address::query()
            ->whereHas('guests', fn ($q) => $q
                ->where('wedding_id', $weddingId)
                ->whereNull('main_guest_id'));

        $total = (clone $query)->count();

        $data = $query
            ->with(['guests' => fn ($q) => $q
                ->where('wedding_id', $weddingId)
                ->whereNull('main_guest_id')
                ->with('companion')
                ->oldest()])
            ->oldest()
            ->skip($first)
            ->take($rowsPerPage)
            ->get();

        return ['data' => $data, 'total' => $total];
    }

    /**
     * @return array{data: Collection<int, Guest>, total: int}
     */
    public function guestsTable(ListGuestsFormRequest $request): array
    {
        $first = $request->integer('first', 0);
        $rowsPerPage = $request->integer('rowsPerPage', 15);
        $weddingId = Wedding::selectedWedding();

        $query = Guest::query()
            ->where('wedding_id', $weddingId)
            ->whereNull('main_guest_id');

        $total = (clone $query)->count();

        $data = $query
            ->with(['address', 'companion'])
            ->oldest()
            ->skip($first)
            ->take($rowsPerPage)
            ->get();

        return ['data' => $data, 'total' => $total];
    }
}
