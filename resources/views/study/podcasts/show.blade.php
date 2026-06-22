<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-100 leading-tight">{{ $podcast->title }}</h2>
            <div class="flex items-center gap-2">
                <a href="{{ route('study.podcasts.index') }}" class="text-sm text-gray-400 hover:text-white transition">Voltar</a>
                <form method="POST" action="{{ route('study.podcasts.destroy', $podcast) }}" class="inline" onsubmit="return confirm('Remover este podcast?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-sm text-red-400 hover:text-red-300 transition ml-2">Excluir</button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="backdrop-blur-xl bg-white/5 border border-white/10 rounded-2xl p-6">
                <div class="flex items-center justify-center mb-6">
                    <div class="w-24 h-24 rounded-full bg-gradient-to-br from-green-500/20 to-emerald-500/20 border border-green-500/30 flex items-center justify-center">
                        <svg class="w-12 h-12 text-green-400" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                        </svg>
                    </div>
                </div>

                <div class="text-center mb-4">
                    <p class="text-sm text-gray-500">Duração: {{ gmdate('i:s', $podcast->duration_seconds) }} min</p>

                    @if ($podcast->audio_path)
                        <div class="mt-4 backdrop-blur-lg bg-white/5 border border-white/10 rounded-xl p-4">
                            <audio controls class="w-full" autoplay>
                                <source src="{{ asset('storage/' . $podcast->audio_path) }}" type="audio/mpeg">
                                Seu navegador não suporta o elemento de áudio.
                            </audio>
                            <div class="flex items-center justify-between mt-2 text-xs text-gray-500">
                                <span>{{ $podcast->created_at->format('d/m/Y H:i') }}</span>
                                <a href="{{ asset('storage/' . $podcast->audio_path) }}" download
                                   class="text-indigo-400 hover:text-indigo-300 transition flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    Download MP3
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="backdrop-blur-lg bg-white/5 border border-white/10 rounded-lg p-4 mt-4">
                            <p class="text-gray-400 text-sm">Áudio não disponível no momento. Veja o script abaixo:</p>
                        </div>
                    @endif
                </div>
            </div>

            @if ($podcast->studyMaterial)
                <div class="backdrop-blur-xl bg-white/5 border border-white/10 rounded-2xl p-4">
                    <p class="text-xs text-gray-500">Baseado em:
                        <a href="{{ route('study.materials.show', $podcast->studyMaterial) }}" class="text-indigo-400 hover:text-indigo-300">
                            {{ $podcast->studyMaterial->title }}
                        </a>
                    </p>
                </div>
            @endif

            @if ($podcast->script)
                <div class="backdrop-blur-xl bg-white/5 border border-white/10 rounded-2xl p-6">
                    <h3 class="text-lg font-medium text-gray-200 mb-4">Script do Debate</h3>
                    <div class="text-gray-300 text-sm leading-relaxed whitespace-pre-line font-mono">
                        {{ $podcast->script }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
