<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

#[Fillable(['wedding_id', 'created_by', 'name', 'amount', 'category', 'contact', 'payment_deadline', 'payment_status', 'note'])]
class Finance extends Model
{
    use HasUuids, SoftDeletes;

    protected $keyType = 'string';

    public $incrementing = false;

    protected function casts(): array
    {
        return [
            'amount' => 'float',
            'payment_deadline' => 'datetime',
        ];
    }

    protected static function booted()
    {
        static::creating(function (Finance $finance) {
            if (Auth::check() && empty($finance->created_by)) {
                $finance->created_by = Auth::id();
            }
        });
    }

    public function wedding(): BelongsTo
    {
        return $this->belongsTo(Wedding::class, 'wedding_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
