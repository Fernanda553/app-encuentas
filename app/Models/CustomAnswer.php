<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'vote_id',
        'custom_text',
    ];

    public function vote(): BelongsTo
    {
        return $this->belongsTo(Vote::class);
    }
}
