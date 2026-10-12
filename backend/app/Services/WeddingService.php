<?php

namespace App\Services;

use App\DTO\Wedding\CreateWeddingDTO;
use App\Http\Requests\WeddingFormRequest;
use App\Models\Wedding;
use Illuminate\Support\Facades\DB;

class WeddingService
{
    /**
     * @param WeddingFormRequest $request
     * @return Wedding
     * @throws \Throwable
     */
    public function createWedding(WeddingFormRequest $request): Wedding
    {
        return DB::transaction(function () use ($request) {
            $weddingDTO = CreateWeddingDTO::fromRequest($request->validated());

            $wedding = Wedding::create([
                'name' => $weddingDTO->name,
                'date' => $weddingDTO->date ?? null,
                'invite_code' => $weddingDTO->invite_code,
            ]);

            auth()->user()->weddings()->save($wedding, [
                'role' => $weddingDTO->role->value,
                'is_active' => $weddingDTO->is_active,
            ]);

            return $wedding;
        });
    }
}
