<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'text',
        'survey_id',
        'is_required',
        'allow_multiple_answers',
        'order',
        'type',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'allow_multiple_answers' => 'boolean',
        'order' => 'integer',
    ];

    public function survey(): BelongsTo
    {
        return $this->belongsTo(Survey::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class)->orderBy('order');
    }

    public function isMultipleChoice(): bool
    {
        return $this->type === 'multiple' || $this->allow_multiple_answers;
    }

    public function getTotalVotes(): int
    {
        return $this->answers()->sum('vote_count');
    }
} 