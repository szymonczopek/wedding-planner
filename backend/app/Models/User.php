<?php

namespace App\Models;

use App\Enums\UserRolesEnum;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property UserRolesEnum $role
 * @property-read string $role_label
 *
 * @method static static create(array<string, mixed> $attributes = [])
 * @method static Builder|User where(string|array|\Closure $column, mixed $operator = null, mixed $value = null)
 */
#[Fillable(['id', 'name', 'email', 'password', 'role', 'invite_code'])]
#[Hidden(['id', 'email', 'password', 'role', 'password', 'remember_token', 'created_at', 'updated_at', 'email_verified_at'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, HasUuids, Notifiable;

    protected $keyType = 'string';

    public $incrementing = false;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRolesEnum::class,
        ];
    }

    protected $appends = ['role_label'];

    protected function roleLabel(): Attribute
    {
        return Attribute::get(fn () => $this->role->label());
    }

    public function createdWeddings(): HasMany
    {
        return $this->hasMany(Wedding::class, 'created_by');
    }

    public function weddings(): BelongsToMany
    {
        return $this->belongsToMany(Wedding::class, 'wedding_user', 'user_id', 'wedding_id')->withTimestamps();
    }

    public function weddingUsers(): HasMany
    {
        return $this->hasMany(WeddingUser::class, 'user_id');
    }

    public function createdTables(): HasMany
    {
        return $this->hasMany(Table::class, 'created_by');
    }

    public function createdGuests(): HasMany
    {
        return $this->hasMany(Guest::class, 'created_by');
    }

    public function createdFinances(): HasMany
    {
        return $this->hasMany(Finance::class, 'created_by');
    }

    public function createdCalendars(): HasMany
    {
        return $this->hasMany(Calendar::class, 'created_by');
    }

    public function createdCalendarEvents(): HasMany
    {
        return $this->hasMany(CalendarEvent::class, 'created_by');
    }
}
