<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Answer extends Model
{
    use HasFactory;

    protected $fillable = [
        'text',
        'question_id',
        'order',
        'vote_count',
        'is_other',
    ];

    protected $casts = [
        'order' => 'integer',
        'vote_count' => 'integer',
        'is_other' => 'boolean',
    ];

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }

    public function incrementVoteCount(): void
    {
        $this->increment('vote_count');
    }

    public function decrementVoteCount(): void
    {
        $this->decrement('vote_count');
    }

    public function getPercentage(): float
    {
        $total = $this->question->getTotalVotes();
        return $total > 0 ? round(($this->vote_count / $total) * 100, 2) : 0;
    }
} 