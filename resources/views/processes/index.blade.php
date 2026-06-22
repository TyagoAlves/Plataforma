<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-white leading-tight">
                @if(request('category_id'))
                    Processos: {{ \App\Models\ProcessCategory::find(request('category_id'))?->name }}
                @else
                    {{ __('Todos os Processos') }}
                @endif
            </h2>
            <span class="text-gray-400 text-sm">{{ $processes->total() }} processos encontrados</span>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="backdrop-blur-xl bg-white/5 border border-white/10 rounded-2xl p-6 mb-8 glass-panel">
                <form method="GET" action="{{ route('processes.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                    @if(request('category_id'))
                        <input type="hidden" name="category_id" value="{{ request('category_id') }}">
                    @endif

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Data Início</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full rounded-xl border border-white/10 bg-white/5 backdrop-blur-md text-white px-4 py-2.5 focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Data Fim</label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full rounded-xl border border-white/10 bg-white/5 backdrop-blur-md text-white px-4 py-2.5 focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Tipo</label>
                        <select name="type" class="w-full rounded-xl border border-white/10 bg-white/5 backdrop-blur-md text-white px-4 py-2.5 focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Todos</option>
                            @foreach($types as $type)
                                <option value="{{ $type }}" @selected(request('type') == $type)>{{ ucfirst($type) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Palavra-chave</label>
                        <input type="text" name="keyword" value="{{ request('keyword') }}" placeholder="Buscar..." class="w-full rounded-xl border border-white/10 bg-white/5 backdrop-blur-md text-white px-4 py-2.5 focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div class="flex items-end gap-2">
                        <button type="submit" class="w-full backdrop-blur-lg bg-indigo-600/80 hover:bg-indigo-600 text-white font-semibold rounded-xl px-4 py-2.5 transition-all border border-indigo-400/30">
                            Filtrar
                        </button>
                        <a href="{{ route('processes.index') }}" class="w-full backdrop-blur-lg bg-white/10 hover:bg-white/20 text-white font-semibold rounded-xl px-4 py-2.5 transition-all border border-white/20 text-center">
                            Limpar
                        </a>
                    </div>
                </form>
            </div>

            <div class="space-y-4">
                @forelse($processes as $process)
                    <a href="{{ route('processes.show', $process) }}" class="block group">
                        <div class="backdrop-blur-xl bg-white/5 border border-white/10 rounded-xl p-6 glass-card transition-all duration-300">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-2">
                                        <span class="text-xs font-mono font-bold text-indigo-400 bg-indigo-500/10 px-2 py-1 rounded-lg border border-indigo-400/20">
                                            {{ $process->number }}
                                        </span>
                                        <span class="text-xs text-gray-500">{{ $process->date->format('d/m/Y') }}</span>
                                        <span class="text-xs px-2 py-0.5 rounded-full
                                            @if($process->status == 'pending') bg-yellow-500/20 text-yellow-300 border border-yellow-400/20
                                            @elseif($process->status == 'in_analysis') bg-blue-500/20 text-blue-300 border border-blue-400/20
                                            @else bg-emerald-500/20 text-emerald-300 border border-emerald-400/20 @endif">
                                            {{ __($process->status) }}
                                        </span>
                                        <span class="text-xs text-gray-400 bg-white/5 px-2 py-0.5 rounded-full border border-white/10">
                                            {{ ucfirst($process->type) }}
                                        </span>
                                    </div>
                                    <h4 class="text-lg font-semibold text-white group-hover:text-indigo-300 transition-colors">{{ $process->title }}</h4>
                                    <p class="text-gray-400 text-sm mt-1 line-clamp-2">{{ $process->category->name }}</p>
                                </div>
                                <svg class="w-5 h-5 text-gray-500 group-hover:text-indigo-400 transition-colors mt-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="backdrop-blur-xl bg-white/5 border border-white/10 rounded-xl p-12 text-center">
                        <p class="text-gray-400 text-lg">Nenhum processo encontrado.</p>
                    </div>
                @endforelse
            </div>

            @if($processes->hasPages())
                <div class="mt-8">
                    {{ $processes->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>

    <style>
        .glass-panel {
            background: linear-gradient(135deg, rgba(255,255,255,0.08) 0%, rgba(255,255,255,0.03) 100%);
        }
        .glass-card {
            transition: all 0.3s ease;
        }
        .glass-card:hover {
            transform: translateY(-2px);
            background: rgba(255,255,255,0.08);
            border-color: rgba(99, 102, 241, 0.3);
            box-shadow: 0 8px 32px rgba(99, 102, 241, 0.1);
        }
        input, select {
            color-scheme: dark;
        }
    </style>
</x-app-layout>
