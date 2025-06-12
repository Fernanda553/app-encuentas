<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Survey extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'is_active',
        'start_date',
        'end_date',
        'max_votes',
        'total_votes',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'max_votes' => 'integer',
        'total_votes' => 'integer',
    ];

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class)->orderBy('order');
    }

    public function isActive(): bool
    {
        return $this->is_active && 
               now()->between($this->start_date, $this->end_date) &&
               (!$this->max_votes || $this->total_votes < $this->max_votes);
    }

    public function hasReachedMaxVotes(): bool
    {
        return $this->max_votes && $this->total_votes >= $this->max_votes;
    }

    public function incrementVoteCount(): void
    {
        $this->increment('total_votes');
    }

    public function decrementVoteCount(): void
    {
        $this->decrement('total_votes');
    }
} 