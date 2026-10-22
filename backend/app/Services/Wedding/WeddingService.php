<?php

namespace App\Services\Wedding;

use App\DTO\Wedding\CreateWeddingDTO;
use App\Http\Requests\Wedding\ChooseWeddingFormRequest;
use App\Http\Requests\Wedding\CreateWeddingFormRequest;
use App\Http\Requests\Wedding\JoinWeddingFormRequest;
use App\Models\Wedding;
use App\Models\WeddingUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WeddingService
{
    /**
     * @throws \Throwable
     */
    public function createWedding(CreateWeddingFormRequest $request): Wedding
    {
        return DB::transaction(function () use ($request) {
            $this->unselectWeddings();

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

    public function joinToWedding(JoinWeddingFormRequest $request): bool
    {
        $wedding = Wedding::where('invite_code', $request->validated()['invite_code'])->first();

        return DB::transaction(function () use ($wedding, $request) {
            $this->unselectWeddings();

            auth()->user()->weddings()->attach($wedding->id, [
                'selected' => true,
                'role' => $request->validated()['role'],
            ]);

            return true;
        });
    }

    public function unselectWeddings(): void
    {
        WeddingUser::where('user_id', Auth::id())
            ->where('selected', true)
            ->update(['selected' => false]);
    }

    public function selectWeddings(): void
    {
        WeddingUser::where('user_id', Auth::id())
            ->update(['selected' => true]);
    }

    public function chooseWedding(ChooseWeddingFormRequest $request): bool
    {
        return Wedding::findOrFail($request->validated()['wedding_id'])->update(['selected' => true]);
    }
}
