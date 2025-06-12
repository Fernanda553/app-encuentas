<?php

namespace App\Livewire;

use App\Models\Survey;
use App\Events\VoteCasted;
use App\Jobs\ProcessVote;
use Livewire\Component;
use Illuminate\Support\Facades\Cache;

class ShowSurvey extends Component
{
    public Survey $survey;
    public array $selectedAnswers = [];
    public bool $showResults = false;
    public bool $hasVoted = false;
    public ?string $error = null;
    public array $questions = [];

    public function mount(Survey $survey)
    {
        $this->survey = $survey;
        $this->checkVoteStatus();
        $this->loadResults();
        
        // Si la ruta es survey.results, mostrar resultados automáticamente
        if (request()->route()->getName() === 'survey.results') {
            $this->showResults = true;
        }
    }

    public function checkVoteStatus()
    {
        $sessionId = session()->getId();
        $this->hasVoted = Cache::has('voted_' . $this->survey->id . '_' . request()->ip() . '_' . $sessionId);
    }

    public function loadResults()
    {
        $this->questions = $this->survey->questions->map(function ($question) {
            $totalVotes = $question->getTotalVotes();
            return [
                'id' => $question->id,
                'text' => $question->text,
                'type' => $question->type,
                'is_required' => $question->is_required,
                'answers' => $question->answers->map(function ($answer) use ($totalVotes) {
                    return [
                        'id' => $answer->id,
                        'text' => $answer->text,
                        'votes' => $answer->vote_count,
                        'percentage' => $totalVotes > 0 ? round(($answer->vote_count / $totalVotes) * 100) : 0,
                    ];
                })->toArray(),
            ];
        })->toArray();
    }

    public function vote()
    {
        if (!$this->survey->isActive()) {
            $this->error = 'Esta encuesta no está activa.';
            return;
        }

        if ($this->hasVoted) {
            $this->error = 'Ya has votado en esta encuesta.';
            return;
        }

        $sessionId = session()->getId();
        $ipAddress = request()->ip();

        foreach ($this->selectedAnswers as $questionId => $answerIds) {
            if (empty($answerIds)) {
                continue;
            }

            if (!is_array($answerIds)) {
                $answerIds = [$answerIds];
            }

            foreach ($answerIds as $answerId) {
                ProcessVote::dispatch($answerId, $ipAddress, $sessionId);
            }
        }

        $this->hasVoted = true;
        $this->showResults = true;
        $this->loadResults();
    }

    public function toggleResults()
    {
        $this->showResults = !$this->showResults;
    }

    public function handleVoteCasted($data)
    {
        $this->loadResults();
    }

    public function render()
    {
        return view('livewire.show-survey', [
            'survey' => $this->survey,
            'questions' => $this->questions,
            'showResults' => $this->showResults,
            'hasVoted' => $this->hasVoted,
            'error' => $this->error,
        ]);
    }
} 