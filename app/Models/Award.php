<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Award extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'name',
        'display_name',
        'description',
        'category',
        'prize_money_or_award',
        'year',
        'sort_order',
        'is_active',
        'is_voting_allowed',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_voting_allowed' => 'boolean',
            'year' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function winners(): HasMany
    {
        return $this->hasMany(Winner::class);
    }

    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeVotingAllowed(Builder $query): Builder
    {
        return $query->where('is_active', true)->where('is_voting_allowed', true);
    }
}
