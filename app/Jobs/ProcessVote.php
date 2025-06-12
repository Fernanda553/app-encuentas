<?php

namespace App\Jobs;

use App\Events\VoteCasted;
use App\Models\Answer;
use App\Models\Survey;
use App\Models\Vote;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Throwable;

class ProcessVote implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $surveyId,
        public array $answerIds,
        public ?string $ipAddress,
        public string $sessionId
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            DB::transaction(function () {
                // 1. Bloquear la encuesta para evitar race conditions
                $survey = Survey::lockForUpdate()->findOrFail($this->surveyId);

                // 2. Re-verificar si la encuesta está activa
                if (!$survey->is_active) {
                    return; // Salir si ya no está activa
                }

                // 3. Re-verificar si este usuario ya votó
                $alreadyVoted = Vote::where('survey_id', $this->surveyId)
                    ->where('session_id', $this->sessionId)
                    ->when($this->ipAddress, fn ($q) => $q->where('ip_address', $this->ipAddress))
                    ->exists();

                if ($alreadyVoted) {
                    return; // Salir si ya existe un voto
                }

                // 4. Incrementar contadores atómicamente
                Answer::whereIn('id', $this->answerIds)->increment('votes_count');

                // 5. Registrar el voto para prevenir duplicados futuros
                Vote::create([
                    'survey_id' => $this->surveyId,
                    'ip_address' => $this->ipAddress,
                    'session_id' => $this->sessionId,
                ]);

                // 6. Verificar si se alcanzó el límite de votos
                if ($survey->max_votes && $survey->votes()->count() >= $survey->max_votes) {
                    $survey->update(['is_active' => false]);
                }

                // 7. Emitir evento para actualización en tiempo real
                $updatedSurvey = $survey->fresh(['questions.answers']);
                VoteCasted::dispatch($updatedSurvey);

            });
        } catch (Throwable $e) {
            // Manejar error, por ejemplo, loguearlo
            report($e);
        }
    }
}
