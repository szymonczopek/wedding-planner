<?php

namespace App\Services\Wedding;

use App\DTO\Wedding\CreateWeddingDTO;
use App\Http\Requests\Wedding\ChooseWeddingFormRequest;
use App\Http\Requests\Wedding\CreateWeddingFormRequest;
use App\Models\Wedding;
use Illuminate\Support\Facades\DB;

class WeddingService
{
    /**
     * @throws \Throwable
     */
    public function createWedding(CreateWeddingFormRequest $request): Wedding
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
                'selected' => $weddingDTO->selected,
            ]);

            return $wedding;
        });
    }

//    public function chooseWedding(chooseWeddingFormRequest $request)
//    {
//
//    }
}
