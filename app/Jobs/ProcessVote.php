<?php

namespace App\Jobs;

use App\Models\Answer;
use App\Models\Vote;
use App\Events\VoteCasted;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProcessVote implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $maxExceptions = 3;
    public $timeout = 30;
    public $retryAfter = 60;

    protected $answerId;
    protected $ipAddress;
    protected $sessionId;

    public function __construct($answerId, $ipAddress, $sessionId)
    {
        $this->answerId = $answerId;
        $this->ipAddress = $ipAddress;
        $this->sessionId = $sessionId;
    }

    public function handle()
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
        Log::error('Vote processing failed', [
            'answer_id' => $this->answerId,
            'ip_address' => $this->ipAddress,
            'error' => $exception->getMessage()
        ]);
    }
} 