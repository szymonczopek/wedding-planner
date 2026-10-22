<?php

namespace App\Services\User;

use App\Models\User;
use Illuminate\Http\Request;

class UserService
{
    public function userWithSelectedWedding(Request $request): User
    {
        return $request->user()
            ->loadCount('weddings')
            ->load([
                'weddings' => fn ($q) => $q->wherePivot('selected', true),
            ]);
    }
}
