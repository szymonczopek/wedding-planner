<?php

namespace App\Services;

use App\DTO\Wedding\CreateWeddingDTO;
use App\Models\Wedding;

class WeddingService
{
    /**
     * @param string $name
     * @return Wedding
     */
    public function createWedding(string $createdBy, string $name): Wedding
    {
        $registerUserDTO = CreateWeddingDTO::fromRequest([
            'name' => $name,
            'created_by' => $createdBy,
        ]);

        return Wedding::create($registerUserDTO->toArray());
    }
}
