<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

#[Hidden(['id', 'created_at', 'updated_at'])]
#[Fillable(['name', 'date', 'created_by', 'invite_code'])]
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

    protected static function booted()
    {
        static::creating(function (Wedding $wedding) {
            if (Auth::check() && empty($wedding->created_by)) {
                $wedding->created_by = Auth::id();
            }
        });
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'wedding_user', 'wedding_id', 'user_id')->using(WeddingUser::class)->withPivot(['id', 'role', 'is_active'])->withTimestamps();
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
