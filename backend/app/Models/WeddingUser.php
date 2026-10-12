<?php

namespace App\Models;

use App\Enums\UserRolesEnum;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property UserRolesEnum $role
 * @property-read string $role_label
 *
 * @method static static create(array<string, mixed> $attributes = [])
 * @method static Builder|WeddingUser where(string|array|\Closure $column, mixed $operator = null, mixed $value = null)
 */
#[Fillable(['user_id', 'wedding_id', 'role'])]
class WeddingUser extends Pivot
{
    use HasUuids;

    protected $table = 'wedding_user';

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
            'role' => UserRolesEnum::class,
        ];
    }

    protected $appends = ['role_label'];

    protected function roleLabel(): Attribute
    {
        return Attribute::get(fn() => $this->role->label());
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function wedding(): BelongsTo
    {
        return $this->belongsTo(Wedding::class, 'wedding_id');
    }
}
