<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Models\Broadcast;

class Movie extends Model
{
    use HasUuids, HasFactory;

    protected $fillable = [
        'title',
        'rating',
        'age_restriction',
        'description',
        'released_at',
    ];

    protected $casts = [
        'released_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the movies attached broadcasts.
     *
     * @return HasMany
     */
    public function broadcasts(): HasMany
    {
        return $this->hasMany(Broadcast::class);
    }
}
