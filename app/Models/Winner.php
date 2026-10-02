<?php

namespace App\Models;

use App\Enums\WinnerRank;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Winner extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'award_id',
        'participant_id',
        'year',
        'rank_order',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'rank_order' => WinnerRank::class,
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function award(): BelongsTo
    {
        return $this->belongsTo(Award::class);
    }

    public function participant(): BelongsTo
    {
        return $this->belongsTo(Participant::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }
}
