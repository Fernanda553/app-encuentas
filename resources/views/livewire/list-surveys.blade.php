<div class="min-h-screen bg-gradient-to-br from-gray-900 via-slate-900 to-indigo-950 py-12 flex flex-col items-center">
    <div class="container mx-auto px-4 py-8 w-full">
        <h1 class="text-4xl md:text-5xl font-extrabold mb-12 text-center bg-gradient-to-r from-cyan-400 via-blue-500 to-fuchsia-500 bg-clip-text text-transparent drop-shadow-lg tracking-tight">
            Encuestas activas
        </h1>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
            @forelse($surveys as $survey)
                <a href="{{ route('surveys.show', $survey) }}" class="block group transform transition duration-300 hover:scale-105">
                    <div class="backdrop-blur-lg bg-white/10 border border-cyan-400/40 rounded-2xl shadow-2xl group-hover:shadow-cyan-500/30 transition-all duration-300 p-8 relative overflow-hidden">
                        <div class="absolute -top-4 -right-4 opacity-20 text-cyan-400 text-8xl pointer-events-none select-none">
                            <svg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='currentColor' class='w-24 h-24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M5 13l4 4L19 7'/></svg>
                        </div>
                        <h2 class="text-2xl font-bold mb-2 bg-gradient-to-r from-cyan-400 via-blue-400 to-fuchsia-500 bg-clip-text text-transparent group-hover:from-fuchsia-500 group-hover:to-cyan-400 transition-colors drop-shadow">
                            {{ $survey->title }}
                        </h2>
                        <p class="text-slate-200 mb-6 font-normal">{{ $survey->description }}</p>
                        <div class="flex justify-between items-center mt-4">
                            <span class="text-xs text-cyan-300 bg-cyan-900/40 px-3 py-1 rounded-full font-medium shadow-sm">{{ $survey->questions_count }} preguntas</span>
                            <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-gradient-to-r from-cyan-400 via-blue-500 to-fuchsia-500 text-white shadow-lg hover:from-fuchsia-500 hover:to-cyan-400 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-cyan-400/60 border border-cyan-400/30 group-hover:shadow-cyan-400/40 animate-neon-glow">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                Votar ahora
                            </span>
                        </div>
                    </div>
                </a>
            @empty
                <div class="col-span-full">
                    <div class="text-center py-20">
                        <h3 class="text-2xl font-semibold text-cyan-300 mb-2">No hay encuestas activas</h3>
                        <p class="text-cyan-400/80">Actualmente no hay encuestas activas disponibles.</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
    <style>
        .animate-neon-glow {
            box-shadow: 0 0 16px 2px #22d3ee, 0 0 32px 4px #6366f1, 0 0 48px 8px #d946ef;
            transition: box-shadow 0.3s;
        }
        .group:hover .animate-neon-glow {
            box-shadow: 0 0 32px 8px #22d3ee, 0 0 64px 16px #6366f1, 0 0 96px 24px #d946ef;
        }
    </style>
</div>
