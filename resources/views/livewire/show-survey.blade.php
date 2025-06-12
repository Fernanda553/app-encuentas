<div class="max-w-4xl mx-auto p-6">
    @if($error)
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ $error }}</span>
        </div>
    @endif

    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <div class="p-6">
            <h2 class="text-2xl font-bold mb-2">{{ $survey->title }}</h2>
            <p class="text-gray-600 mb-6">{{ $survey->description }}</p>

            @if(!$hasVoted && $survey->isActive())
                <form wire:submit="vote">
                    @foreach($questions as $question)
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold mb-3">
                                {{ $question['text'] }}
                                @if($question['is_required'])
                                    <span class="text-red-500">*</span>
                                @endif
                            </h3>

                            @if($question['type'] === 'single')
                                <div class="space-y-2">
                                    @foreach($question['answers'] as $answer)
                                        <label class="flex items-center space-x-3">
                                            <input type="radio" 
                                                   name="selectedAnswers[{{ $question['id'] }}]" 
                                                   wire:model="selectedAnswers.{{ $question['id'] }}"
                                                   value="{{ $answer['id'] }}"
                                                   class="form-radio h-4 w-4 text-blue-600">
                                            <span>{{ $answer['text'] }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            @else
                                <div class="space-y-2">
                                    @foreach($question['answers'] as $answer)
                                        <label class="flex items-center space-x-3">
                                            <input type="checkbox" 
                                                   name="selectedAnswers[{{ $question['id'] }}][]" 
                                                   wire:model="selectedAnswers.{{ $question['id'] }}"
                                                   value="{{ $answer['id'] }}"
                                                   class="form-checkbox h-4 w-4 text-blue-600">
                                            <span>{{ $answer['text'] }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach

                    <div class="mt-6">
                        <button type="submit" 
                                class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            Votar
                        </button>
                    </div>
                </form>
            @endif

            @if($showResults || $hasVoted)
                <div class="mt-8">
                    <h3 class="text-xl font-semibold mb-4">Resultados</h3>
                    <div class="space-y-6">
                        @foreach($questions as $question)
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h4 class="font-medium mb-3">{{ $question['text'] }}</h4>
                                <div class="space-y-3">
                                    @foreach($question['answers'] as $answer)
                                        <div>
                                            <div class="flex justify-between mb-1">
                                                <span>{{ $answer['text'] }}</span>
                                                <span class="text-sm text-gray-600">
                                                    {{ $answer['votes'] }} votos ({{ $answer['percentage'] }}%)
                                                </span>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-2">
                                                <div class="bg-blue-600 h-2 rounded-full" 
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
</div>
