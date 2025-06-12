<x-layouts.app>
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Active Surveys</h1>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($surveys as $survey)
                <a href="{{ route('surveys.show', $survey) }}" class="block p-6 bg-white rounded-lg border border-gray-200 shadow-md hover:bg-gray-100 transition">
                    <h2 class="mb-2 text-2xl font-bold tracking-tight text-gray-900">{{ $survey->title }}</h2>
                    <p class="font-normal text-gray-700">{{ $survey->description }}</p>
                </a>
            @empty
                <p class="text-gray-500 col-span-full">No active surveys at the moment. Please check back later.</p>
            @endforelse
        </div>
    </div>
</x-layouts.app>
