<?php

namespace App\Models;

use Database\Factories\UserActivityFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserActivity extends Model
{
    /** @use HasFactory<UserActivityFactory> */
    use HasFactory;

    protected $connection = 'analytics';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Associated Event relation.
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Scope for a specific event.
     */
    public function scopeForEvent(Builder $query, ?int $eventId): Builder
    {
        if ($eventId) {
            return $query->where('event_id', $eventId);
        }

        return $query;
    }

    /**
     * Scope for a specific action type.
     */
    public function scopeAction(Builder $query, string $actionType): Builder
    {
        return $query->where('action_type', $actionType);
    }

    /**
     * Scope within a date range.
     */
    public function scopeDateBetween(Builder $query, $startDate, $endDate): Builder
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }
}
