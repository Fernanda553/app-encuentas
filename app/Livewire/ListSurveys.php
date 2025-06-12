<?php

namespace App\Livewire;

use App\Models\Survey;
use Livewire\Component;

class ListSurveys extends Component
{
    public function render()
    {
        return view('livewire.list-surveys', [
            'surveys' => Survey::with('questions.answers')->get()
        ]);
    }
} 