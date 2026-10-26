<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class NullableBoolean implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): ?bool
    {
        return $value === null ? null : (bool) $value;
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): ?bool
    {
        return $value === null ? null : (bool) $value;
    }
}
