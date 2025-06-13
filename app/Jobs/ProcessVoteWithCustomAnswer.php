<?php

namespace App\Jobs;

use App\Models\Answer;
use App\Models\Vote;
use App\Models\CustomAnswer;
use App\Events\VoteCasted;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProcessVoteWithCustomAnswer implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $answerId,
        public string $ipAddress,
        public string $sessionId,
        public ?string $customText = null
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        DB::transaction(function () {
            $answer = Answer::lockForUpdate()->findOrFail($this->answerId);
            $survey = $answer->question->survey;

            // Check if the survey is active
            if (!$survey->isActive()) {
                return;
            }

            // Check if user has already voted
            if (Vote::hasVoted($this->answerId, $this->ipAddress, $this->sessionId)) {
                return;
            }

            // Create the vote
            $vote = Vote::create([
                'answer_id' => $this->answerId,
                'ip_address' => $this->ipAddress,
                'session_id' => $this->sessionId,
            ]);

            // If this is an "other" answer and has custom text, save it
            if ($answer->is_other && $this->customText) {
                CustomAnswer::create([
                    'vote_id' => $vote->id,
                    'custom_text' => $this->customText,
                ]);
            }

            // Update vote counts
            $answer->incrementVoteCount();
            $survey->incrementVoteCount();

            // Cache the vote to prevent duplicate votes
            Cache::put(
                'voted_' . $survey->id . '_' . $this->ipAddress . '_' . $this->sessionId,
                true,
                $survey->end_date
            );

            // Broadcast the event with updated results
            broadcast(new VoteCasted($survey->id, [
                'answer_id' => $answer->id,
                'vote_count' => $answer->vote_count,
                'percentage' => $answer->getPercentage(),
                'total_votes' => $survey->total_votes,
            ]))->toOthers();
        });
    }

    public function failed(Throwable $exception)
    {
        Log::error('Vote with custom answer processing failed', [
            'answer_id' => $this->answerId,
            'ip_address' => $this->ipAddress,
            'custom_text' => $this->customText,
            'error' => $exception->getMessage()
        ]);
    }
}
