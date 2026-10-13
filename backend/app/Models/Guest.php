<?php

namespace App\Models;

use App\Enums\AcquaintanceTypesEnum;
use App\Enums\DietsEnum;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

#[Fillable(['wedding_id', 'table_id', 'created_by', 'address_id', 'main_guest_id', 'name', 'surname', 'acquaintance_type', 'child', 'groom_guest', 'invitation_prepared', 'invitation_delivered', 'confirmed_attendance', 'diet', 'table_position_x', 'table_position_y'])]
#[Hidden(['created_by'])]
class Guest extends Model
{
    use HasUuids, SoftDeletes;

    protected $keyType = 'string';

    public $incrementing = false;

    protected function casts(): array
    {
        return [
            'acquaintance_type' => AcquaintanceTypesEnum::class,
            'diet' => DietsEnum::class,
            'child' => 'boolean',
            'groom_guest' => 'boolean',
            'invitation_prepared' => 'boolean',
            'invitation_delivered' => 'boolean',
            'confirmed_attendance' => 'boolean',
            'table_position_x' => 'float',
            'table_position_y' => 'float',
        ];
    }

    protected static function booted()
    {
        static::creating(function (Guest $guest) {
            if (Auth::check() && empty($guest->created_by)) {
                $guest->created_by = Auth::id();
            }
        });
    }

    public function wedding(): BelongsTo
    {
        return $this->belongsTo(Wedding::class, 'wedding_id');
    }

    public function table(): BelongsTo
    {
        return $this->belongsTo(Table::class, 'table_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'address_id');
    }

    public function mainGuest(): BelongsTo
    {
        return $this->belongsTo(Guest::class, 'main_guest_id');
    }

    public function companions(): HasMany
    {
        return $this->hasMany(Guest::class, 'main_guest_id');
    }
}
