<?php

namespace App\Livewire;

use App\Models\Survey;
use Livewire\Component;
use Livewire\WithPagination;

class ListSurveys extends Component
{
    public function render()
    {
        $surveys = Survey::where('is_active', true)->latest()->get();
        return view('livewire.list-surveys', ['surveys' => $surveys]);
    }
}
