<div>
        <div class="container mx-auto px-4 py-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-6">Active Surveys</h1>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($surveys as $survey)
                    <a href="{{ route('surveys.show', $survey) }}" class="block">
                        <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300 p-6">
                            <h2 class="text-xl font-semibold text-gray-800 mb-2">{{ $survey->title }}</h2>
                            <p class="text-gray-600 mb-4">{{ $survey->description }}</p>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-500">{{ $survey->questions_count }} questions</span>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800">
                                    Vote Now
                                </span>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="col-span-full">
                        <div class="text-center py-12">
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No Active Surveys</h3>
                            <p class="text-gray-500">There are currently no active surveys available.</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>

</div>
