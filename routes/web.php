<?php

use App\Livewire\ListSurveys;
use App\Livewire\ShowSurvey;
use Illuminate\Support\Facades\Route;

Route::get('/', ListSurveys::class)->name('surveys.index');
Route::get('/surveys/{survey}', ShowSurvey::class)->name('surveys.show');
