<?php

namespace App\Services\Address;

use App\Models\Address;
use Illuminate\Database\Eloquent\Collection;

class AddressService
{
    /**
     * @return Collection<int, Address>
     */
    public function searchForWedding(string $weddingId, string $query): Collection
    {
        $terms = preg_split('/\s+/', trim($query), -1, PREG_SPLIT_NO_EMPTY) ?: [];

        return Address::query()
            ->whereHas('guests', fn ($query) => $query->where('wedding_id', $weddingId))
            ->when($terms !== [], function ($query) use ($terms) {
                foreach ($terms as $term) {
                    $like = '%'.$term.'%';

                    $query->where(function ($query) use ($like) {
                        $query->whereLike('city', $like)
                            ->orWhereLike('street', $like)
                            ->orWhereLike('house_number', $like)
                            ->orWhereLike('local_number', $like)
                            ->orWhereLike('postal_code', $like);
                    });
                }
            })
            ->orderBy('city')
            ->orderBy('street')
            ->orderBy('house_number')
            ->limit(20)
            ->get([
                'id',
                'street',
                'house_number',
                'local_number',
                'city',
                'district',
                'postal_code',
                'country',
            ]);
    }
}
