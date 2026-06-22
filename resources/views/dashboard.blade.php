<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-8">
                <h3 class="text-2xl font-bold text-white mb-2">Categorias de Processos</h3>
                <p class="text-gray-400">Selecione uma categoria para visualizar os processos.</p>
            </div>

            @if($categories->isEmpty())
                <div class="backdrop-blur-xl bg-white/5 border border-white/10 rounded-xl p-12 text-center">
                    <p class="text-gray-400 text-lg">Nenhuma categoria disponível.</p>
                    <p class="text-gray-500 text-sm mt-2">As categorias aparecerão aqui quando forem cadastradas.</p>
                </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($categories as $category)
                    <a href="{{ route('processes.index', ['category_id' => $category->id]) }}" class="group">
                        <div class="backdrop-blur-xl bg-white/5 border border-white/10 rounded-2xl p-6 glass-card h-full transition-all duration-300">
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-12 h-12 rounded-xl bg-indigo-500/20 border border-indigo-400/20 flex items-center justify-center text-indigo-400 group-hover:bg-indigo-500/30 transition-colors">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        @switch($category->icon)
                                            @case('folder')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                                                @break
                                            @case('currency-dollar')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                @break
                                            @case('scale')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/>
                                                @break
                                            @case('users')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                                                @break
                                            @case('shopping-cart')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
                                                @break
                                            @case('cpu')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                                @break
                                            @default
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        @endswitch
                                    </svg>
                                </div>
                                <span class="backdrop-blur-md bg-indigo-500/20 text-indigo-300 text-sm font-semibold px-3 py-1 rounded-full border border-indigo-400/20">
                                    {{ $category->processes_count }} processos
                                </span>
                            </div>
                            <h4 class="text-xl font-bold text-white group-hover:text-indigo-300 transition-colors mb-2">{{ $category->name }}</h4>
                            <p class="text-gray-400 text-sm">{{ $category->description }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    <style>
        .glass-card {
            transition: all 0.3s ease;
        }
        .glass-card:hover {
            transform: translateY(-4px);
            background: rgba(255,255,255,0.08);
            border-color: rgba(99, 102, 241, 0.3);
            box-shadow: 0 8px 32px rgba(99, 102, 241, 0.15);
        }
    </style>
</x-app-layout>
