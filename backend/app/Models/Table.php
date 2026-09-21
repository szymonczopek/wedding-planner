<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['wedding_id', 'created_by', 'name', 'capacity', 'position_x', 'position_y', 'width', 'height', 'radius'])]
class Table extends Model
{
    use HasUuids, SoftDeletes;

    protected $keyType = 'string';

    public $incrementing = false;

    protected function casts(): array
    {
        return [
            'capacity' => 'integer',
            'position_x' => 'float',
            'position_y' => 'float',
            'width' => 'float',
            'height' => 'float',
            'radius' => 'float',
        ];
    }

    public function wedding(): BelongsTo
    {
        return $this->belongsTo(Wedding::class, 'wedding_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function guests(): HasMany
    {
        return $this->hasMany(Guest::class, 'table_id');
    }
}
