<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('processes.index', ['category_id' => $process->process_category_id]) }}" class="text-gray-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 12H5m7-7l-7 7 7 7"/></svg>
            </a>
            <h2 class="font-semibold text-xl text-white leading-tight">
                Processo {{ $process->number }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
                <div class="backdrop-blur-xl bg-emerald-500/20 border border-emerald-400/30 rounded-xl p-4 text-emerald-300">
                    {{ session('success') }}
                </div>
            @endif

            <div class="backdrop-blur-xl bg-white/5 border border-white/10 rounded-2xl p-8 glass-panel">
                <div class="flex items-center gap-3 mb-6">
                    <span class="text-sm font-mono font-bold text-indigo-400 bg-indigo-500/10 px-3 py-1.5 rounded-lg border border-indigo-400/20">
                        {{ $process->number }}
                    </span>
                    <span class="text-sm text-gray-400">{{ $process->date->format('d/m/Y') }}</span>
                    <span class="text-sm px-3 py-1 rounded-full
                        @if($process->status == 'pending') bg-yellow-500/20 text-yellow-300 border border-yellow-400/20
                        @elseif($process->status == 'in_analysis') bg-blue-500/20 text-blue-300 border border-blue-400/20
                        @else bg-emerald-500/20 text-emerald-300 border border-emerald-400/20 @endif">
                        {{ __($process->status) }}
                    </span>
                    <span class="text-sm text-gray-400 bg-white/5 px-3 py-1 rounded-full border border-white/10">
                        {{ ucfirst($process->type) }}
                    </span>
                    <span class="text-sm text-gray-400 bg-white/5 px-3 py-1 rounded-full border border-white/10">
                        {{ $process->category->name }}
                    </span>
                </div>

                <h3 class="text-2xl font-bold text-white mb-4">{{ $process->title }}</h3>

                <div class="backdrop-blur-md bg-white/5 border border-white/10 rounded-xl p-6 mb-6">
                    <h4 class="text-sm font-semibold text-indigo-400 uppercase tracking-wider mb-3">Conteúdo do Processo</h4>
                    <p class="text-gray-300 leading-relaxed">{{ $process->content }}</p>
                </div>
            </div>

            @if($process->response)
                <div class="backdrop-blur-xl bg-white/5 border border-white/10 rounded-2xl p-8 glass-panel">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                        <div>
                            <h4 class="text-lg font-semibold text-white">Sugestão de Resposta (IA)</h4>
                            <p class="text-xs text-gray-400">A IA analisou o processo e gerou uma sugestão. Edite conforme necessário.</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('processes.response', $process) }}">
                        @csrf
                        <textarea name="final_response" rows="8" class="w-full rounded-xl border border-white/10 bg-white/5 backdrop-blur-md text-white px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500 mb-4" placeholder="A sugestão da IA aparecerá aqui...">{{ old('final_response', $process->response->final_response ?? $process->response->ai_suggestion) }}</textarea>

                        <div class="flex items-center justify-between">
                            <p class="text-sm text-gray-400">
                                @if($process->response->status == 'submitted')
                                    <span class="text-emerald-400">Resposta já enviada</span>
                                @else
                                    <span class="text-amber-400">Aguardando envio</span>
                                @endif
                            </p>
                            <button type="submit" class="inline-flex items-center px-6 py-3 backdrop-blur-lg bg-indigo-600/80 hover:bg-indigo-600 text-white font-semibold rounded-xl transition-all border border-indigo-400/30 shadow-lg hover:shadow-indigo-500/25">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                Enviar Resposta
                            </button>
                        </div>
                    </form>
                </div>
            @endif
        </div>
    </div>

    <style>
        .glass-panel {
            background: linear-gradient(135deg, rgba(255,255,255,0.08) 0%, rgba(255,255,255,0.03) 100%);
        }
        textarea {
            color-scheme: dark;
        }
    </style>
</x-app-layout>
