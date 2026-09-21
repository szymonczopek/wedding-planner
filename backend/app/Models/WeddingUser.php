<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'wedding_id'])]
class WeddingUser extends Model
{
    use HasUuids;

    protected $table = 'wedding_user';

    protected $keyType = 'string';

    public $incrementing = false;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function wedding(): BelongsTo
    {
        return $this->belongsTo(Wedding::class, 'wedding_id');
    }
}
