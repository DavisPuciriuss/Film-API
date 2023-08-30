<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Movie extends Model
{
    use HasUuids, HasFactory;

    /**
     * fillable
     *
     * @var array<string>
     */
    protected $fillable = [
        'title',
        'rating',
        'age_restriction',
        'description',
        'released_at',
    ];

    /**
     * casts
     *
     * @var array<string,string>
     */
    protected $casts = [
        'released_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the movies attached broadcasts.
     */
    public function broadcasts(): HasMany
    {
        return $this->hasMany(Broadcast::class);
    }

    /**
     * Return only movies after now.
     */
    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('released_at', '>', now());
    }

    /**
     * Return only movies with rating above the given rating.
     */
    public function scopeRatingAbove(Builder $query, float $rating): Builder
    {
        return $query->where('rating', '>', $rating);
    }

    /**
     * Return movies ordered by closest upcoming broadcast.
     */
    public function scopeOrderByClosestBroadcast(Builder $query): Builder
    {
        return $query->select('movies.*')
            ->leftJoin('broadcasts', 'movies.id', '=', 'broadcasts.movie_id')
            ->orderByRaw('MIN(ABS(TIMESTAMPDIFF(SECOND, NOW(), broadcasts.broadcasted_at)))')
            ->orderBy('rating', 'desc')
            ->groupBy('movies.id');
    }
}
