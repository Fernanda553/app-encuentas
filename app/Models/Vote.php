<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vote extends Model
{
    use HasFactory;

    protected $fillable = [
        'answer_id',
        'ip_address',
        'session_id',
    ];

    public function answer(): BelongsTo
    {
        return $this->belongsTo(Answer::class);
    }

    public function question()
    {
        return $this->answer->question();
    }

    public function survey()
    {
        return $this->question->survey();
    }

    public static function hasVoted($answerId, $ipAddress, $sessionId): bool
    {
        return static::where('answer_id', $answerId)
            ->where('ip_address', $ipAddress)
            ->where('session_id', $sessionId)
            ->exists();
    }
} 