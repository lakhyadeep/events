<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vote extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'participant_id',
        'award_id',
        'mobile_number',
        'ip_address',
        'user_agent',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'event_id' => 'integer',
            'participant_id' => 'integer',
            'award_id' => 'integer',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function participant(): BelongsTo
    {
        return $this->belongsTo(Participant::class);
    }

    public function award(): BelongsTo
    {
        return $this->belongsTo(Award::class);
    }

    /**
     * Scope for a specific mobile number.
     *
     * @param  Builder<Vote>  $query
     * @return Builder<Vote>
     */
    public function scopeForMobile(Builder $query, string $mobile): Builder
    {
        $clean = preg_replace('/[^0-9]/', '', $mobile) ?? '';

        return $query->where('mobile_number', $clean);
    }
}
