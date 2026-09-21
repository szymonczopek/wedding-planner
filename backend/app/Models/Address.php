<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['street', 'house_number', 'local_number', 'city', 'postal_code', 'country'])]
class Address extends Model
{
    use HasUuids;

    protected $keyType = 'string';

    public $incrementing = false;

    public $timestamps = false;

    public function guests(): HasMany
    {
        return $this->hasMany(Guest::class, 'address_id');
    }
}
