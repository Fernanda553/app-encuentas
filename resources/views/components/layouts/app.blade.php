<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>{{ $title ?? 'App Encuestas' }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="bg-gray-50 font-sans antialiased">
        <nav class="bg-gradient-to-r from-gray-900 via-slate-900 to-indigo-950 shadow-lg border-b-2 border-cyan-400/40">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-20 items-center">
                    <div class="flex items-center">
                        <a href="{{ route('surveys.index') }}" class="flex items-center gap-2 text-2xl md:text-3xl font-extrabold text-pink-400 drop-shadow-lg tracking-tight">
                            <svg class="w-7 h-7 text-pink-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 17v2a2 2 0 002 2h14a2 2 0 002-2v-2M8 17V9m4 8V5m4 12v-3"/></svg>
                            App Encuestas
                        </a>
                    </div>
                    <!-- Desktop button -->
                    <div class="hidden md:flex">
                        <a href="/admin" class="ml-auto inline-flex items-center gap-2 px-5 py-2 rounded-full text-base font-semibold bg-gradient-to-r from-cyan-200 via-blue-200 to-blue-400 text-blue-900 shadow-lg hover:from-blue-300 hover:to-cyan-200 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-cyan-200/60 border border-cyan-200/30">
                            <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                            Crear encuesta
                        </a>
                    </div>
                    <!-- Mobile menu -->
                    <div class="md:hidden flex items-center">
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" class="p-2 rounded-md text-cyan-200 hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-cyan-400/60">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                            </button>
                            <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-slate-900 border border-cyan-200/30 rounded-lg shadow-lg py-2 z-50">
                                <a href="/admin" class="flex items-center gap-2 px-4 py-2 text-base text-blue-900 font-semibold hover:bg-cyan-100 rounded transition">
                                    <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                    Crear encuesta
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <main>
            {{ $slot }}
        </main>
        @livewireScripts
    </body>
</html>