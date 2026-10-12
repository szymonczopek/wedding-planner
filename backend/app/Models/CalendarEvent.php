<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

#[Fillable(['calendar_id', 'created_by', 'date'])]
class CalendarEvent extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'calendar_event';

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
        static::creating(function (CalendarEvent $calendarEvent) {
            if (Auth::check() && empty($calendarEvent->created_by)) {
                $calendarEvent->created_by = Auth::id();
            }
        });
    }

    public function calendar(): BelongsTo
    {
        return $this->belongsTo(Calendar::class, 'calendar_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
