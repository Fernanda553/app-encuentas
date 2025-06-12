<div class="min-h-screen bg-gradient-to-br from-gray-900 via-slate-900 to-indigo-950 py-12 flex flex-col items-center">
    <div class="max-w-3xl w-full mx-auto px-4">
        @if($error)
            <div class="bg-red-900/80 border-l-4 border-pink-500 p-4 mb-6 rounded-md shadow-sm text-pink-200" role="alert">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-pink-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm">{{ $error }}</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="backdrop-blur-lg bg-white/10 border border-cyan-400/40 rounded-2xl shadow-2xl p-10 relative overflow-hidden">
            <div class="absolute -top-8 -right-8 opacity-10 text-cyan-400 text-9xl pointer-events-none select-none">
                <svg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='currentColor' class='w-40 h-40'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M5 13l4 4L19 7'/></svg>
            </div>
            <div class="text-center mb-10">
                <h2 class="text-3xl md:text-4xl font-extrabold mb-3 bg-gradient-to-r from-cyan-400 via-blue-400 to-fuchsia-500 bg-clip-text text-transparent drop-shadow-lg">
                    {{ $survey->title }}
                </h2>
                <p class="text-lg text-slate-200">{{ $survey->description }}</p>
            </div>

            @if(!$hasVoted && $survey->isActive())
                <form wire:submit="vote" class="space-y-10">
                    @foreach($questions as $question)
                        <div class="bg-slate-900/60 rounded-xl p-6 shadow group border border-cyan-400/10">
                            <h3 class="text-xl font-semibold mb-4 text-cyan-200 flex items-center">
                                {{ $question['text'] }}
                                @if($question['is_required'])
                                    <span class="text-pink-400 ml-2">*</span>
                                @endif
                            </h3>

                            @if($question['type'] === 'single')
                                <div class="space-y-3">
                                    @foreach($question['answers'] as $answer)
                                        <label class="flex items-center p-3 bg-slate-800/60 rounded-lg border border-cyan-400/10 hover:border-cyan-400 transition-colors duration-200 cursor-pointer">
                                            <input type="radio" 
                                                   name="selectedAnswers[{{ $question['id'] }}]" 
                                                   wire:model="selectedAnswers.{{ $question['id'] }}"
                                                   value="{{ $answer['id'] }}"
                                                   class="h-4 w-4 text-cyan-400 focus:ring-cyan-500 bg-slate-900 border-slate-700">
                                            <span class="ml-3 text-slate-100">{{ $answer['text'] }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            @else
                                <div class="space-y-3">
                                    @foreach($question['answers'] as $answer)
                                        <label class="flex items-center p-3 bg-slate-800/60 rounded-lg border border-cyan-400/10 hover:border-cyan-400 transition-colors duration-200 cursor-pointer">
                                            <input type="checkbox" 
                                                   name="selectedAnswers[{{ $question['id'] }}][]" 
                                                   wire:model="selectedAnswers.{{ $question['id'] }}"
                                                   value="{{ $answer['id'] }}"
                                                   class="h-4 w-4 text-cyan-400 focus:ring-cyan-500 bg-slate-900 border-slate-700">
                                            <span class="ml-3 text-slate-100">{{ $answer['text'] }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach

                    <div class="flex justify-center">
                        <button type="submit" 
                                class="inline-flex items-center px-8 py-3 border border-cyan-400/30 text-base font-bold rounded-lg shadow-lg text-white bg-gradient-to-r from-cyan-400 via-blue-500 to-fuchsia-500 hover:from-fuchsia-500 hover:to-cyan-400 focus:outline-none focus:ring-2 focus:ring-cyan-400/60 transition-all duration-200 animate-neon-glow">
                            <svg class="w-5 h-5 mr-2 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Enviar Voto
                        </button>
                    </div>
                </form>
            @endif

            @if($showResults || $hasVoted)
                <div class="mt-12">
                    <h3 class="text-2xl font-bold mb-6 text-center bg-gradient-to-r from-cyan-400 via-blue-400 to-fuchsia-500 bg-clip-text text-transparent drop-shadow-lg">Resultados</h3>
                    <div class="space-y-6">
                        @foreach($questions as $question)
                            <div class="bg-slate-900/60 rounded-xl p-6 shadow border border-cyan-400/10">
                                <h4 class="text-lg font-semibold mb-4 text-cyan-200">{{ $question['text'] }}</h4>
                                <div class="space-y-4">
                                    @foreach($question['answers'] as $answer)
                                        <div>
                                            <div class="flex justify-between items-center mb-2">
                                                <span class="text-slate-100">{{ $answer['text'] }}</span>
                                                <span class="text-sm font-medium text-cyan-300">
                                                    {{ $answer['votes'] }} votos ({{ $answer['percentage'] }}%)
                                                </span>
                                            </div>
                                            <div class="w-full bg-slate-800 rounded-full h-3">
                                                <div class="bg-gradient-to-r from-cyan-400 via-blue-500 to-fuchsia-500 h-3 rounded-full transition-all duration-500 ease-out" 
                                                     style="width: {{ $answer['percentage'] }}%">
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
    <style>
        .animate-neon-glow {
            box-shadow: 0 0 16px 2px #22d3ee, 0 0 32px 4px #6366f1, 0 0 48px 8px #d946ef;
            transition: box-shadow 0.3s;
        }
        .animate-neon-glow:hover {
            box-shadow: 0 0 32px 8px #22d3ee, 0 0 64px 16px #6366f1, 0 0 96px 24px #d946ef;
        }
    </style>
</div>
