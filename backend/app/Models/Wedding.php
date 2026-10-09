<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['name', 'date', 'created_by'])]
class Wedding extends Model
{
    use HasUuids, SoftDeletes;

    protected $keyType = 'string';

    public $incrementing = false;

    protected function casts(): array
    {
        return [
            'date' => 'datetime',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'wedding_user', 'wedding_id', 'user_id')->using(WeddingUser::class)->withTimestamps();
    }

    public function weddingUsers(): HasMany
    {
        return $this->hasMany(WeddingUser::class, 'wedding_id');
    }

    public function tables(): HasMany
    {
        return $this->hasMany(Table::class, 'wedding_id');
    }

    public function finances(): HasMany
    {
        return $this->hasMany(Finance::class, 'wedding_id');
    }

    public function calendars(): HasMany
    {
        return $this->hasMany(Calendar::class, 'wedding_id');
    }
}
