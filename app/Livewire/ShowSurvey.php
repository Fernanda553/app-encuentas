<?php

namespace App\Livewire;

use App\Models\Survey;
use App\Models\CustomAnswer;
use App\Events\VoteCasted;
use App\Jobs\ProcessVote;
use App\Jobs\ProcessVoteWithCustomAnswer;
use Livewire\Component;
use Illuminate\Support\Facades\Cache;

class ShowSurvey extends Component
{
    public Survey $survey;
    public array $selectedAnswers = [];
    public array $customAnswers = [];
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
                        'is_other' => $answer->is_other,
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

        // Validar respuestas "Otro" que requieren texto personalizado
        if (!$this->validateOtherAnswers()) {
            return;
        }

        $sessionId = session()->getId();
        $ipAddress = request()->ip();

        foreach ($this->selectedAnswers as $questionId => $answers) {
            if (empty($answers)) {
                continue;
            }

            // Para preguntas de opción única (radio buttons)
            if (!is_array($answers)) {
                $answerId = $answers;
                $customText = $this->customAnswers[$questionId] ?? null;
                
                if ($customText) {
                    ProcessVoteWithCustomAnswer::dispatch($answerId, $ipAddress, $sessionId, $customText);
                } else {
                    ProcessVote::dispatch($answerId, $ipAddress, $sessionId);
                }
                continue;
            }

            // Para preguntas de opción múltiple (checkboxes)
            foreach ($answers as $answerId => $isSelected) {
                if ($isSelected) {
                    $customText = $this->customAnswers[$questionId][$answerId] ?? null;
                    
                    if ($customText) {
                        ProcessVoteWithCustomAnswer::dispatch($answerId, $ipAddress, $sessionId, $customText);
                    } else {
                        ProcessVote::dispatch($answerId, $ipAddress, $sessionId);
                    }
                }
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

    private function validateOtherAnswers(): bool
    {
        foreach ($this->selectedAnswers as $questionId => $answers) {
            if (empty($answers)) {
                continue;
            }

            $question = collect($this->questions)->firstWhere('id', $questionId);
            if (!$question) {
                continue;
            }

            // Para preguntas de opción única (radio buttons)
            if (!is_array($answers)) {
                $answerId = $answers;
                $answer = collect($question['answers'])->firstWhere('id', $answerId);
                
                if ($answer && $answer['is_other']) {
                    $customText = $this->customAnswers[$questionId] ?? '';
                    if (empty(trim($customText))) {
                        $this->error = 'Por favor, especifique su respuesta para la opción "' . $answer['text'] . '".';
                        return false;
                    }
                    if (strlen(trim($customText)) < 3) {
                        $this->error = 'Su respuesta personalizada debe tener al menos 3 caracteres.';
                        return false;
                    }
                }
                continue;
            }

            // Para preguntas de opción múltiple (checkboxes)
            foreach ($answers as $answerId => $isSelected) {
                if (!$isSelected) {
                    continue;
                }

                $answer = collect($question['answers'])->firstWhere('id', $answerId);
                if ($answer && $answer['is_other']) {
                    $customText = $this->customAnswers[$questionId][$answerId] ?? '';
                    if (empty(trim($customText))) {
                        $this->error = 'Por favor, especifique su respuesta para la opción "' . $answer['text'] . '".';
                        return false;
                    }
                    if (strlen(trim($customText)) < 3) {
                        $this->error = 'Su respuesta personalizada debe tener al menos 3 caracteres.';
                        return false;
                    }
                }
            }
        }

        return true;
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