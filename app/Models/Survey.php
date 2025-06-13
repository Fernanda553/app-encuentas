<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Validation\ValidationException;

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

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($survey) {
            // Validar longitud mínima del título
            if ($survey->title && strlen($survey->title) < 3) {
                throw ValidationException::withMessages([
                    'title' => 'El título debe tener al menos 3 caracteres.'
                ]);
            }

            // Validar longitud mínima de la descripción (si existe)
            if ($survey->description && strlen($survey->description) < 3) {
                throw ValidationException::withMessages([
                    'description' => 'La descripción debe tener al menos 3 caracteres.'
                ]);
            }

            // Validar que la fecha de fin no sea menor que la fecha de inicio
            if ($survey->start_date && $survey->end_date) {
                $startDate = $survey->start_date->format('Y-m-d');
                $endDate = $survey->end_date->format('Y-m-d');
                if ($endDate < $startDate) {
                    throw ValidationException::withMessages([
                        'end_date' => 'La fecha de fin no puede ser menor que la fecha de inicio.'
                    ]);
                }
            }

            // Validar que la fecha de inicio no sea anterior a hoy (solo para nuevos registros)
            if (!$survey->exists && $survey->start_date) {
                $today = now()->format('Y-m-d');
                $startDate = $survey->start_date->format('Y-m-d');
                if ($startDate < $today) {
                    throw ValidationException::withMessages([
                        'start_date' => 'La fecha de inicio no puede ser anterior a la fecha actual.'
                    ]);
                }
            }

            // Validar que la fecha de fin no sea anterior a hoy
            if ($survey->end_date) {
                $today = now()->format('Y-m-d');
                $endDate = $survey->end_date->format('Y-m-d');
                if ($endDate < $today) {
                    throw ValidationException::withMessages([
                        'end_date' => 'La fecha de fin no puede ser anterior a la fecha actual.'
                    ]);
                }
            }
        });
    }

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