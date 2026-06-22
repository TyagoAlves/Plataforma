<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-100 leading-tight">Podcasts Didáticos</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="backdrop-blur-xl bg-white/5 border border-white/10 rounded-2xl p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @forelse ($podcasts as $podcast)
                        <div class="backdrop-blur-lg bg-white/5 border border-white/10 rounded-xl p-4 hover:bg-white/10 transition group">
                            <a href="{{ route('study.podcasts.show', $podcast) }}" class="block">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <h4 class="font-medium text-green-300">{{ $podcast->title }}</h4>
                                        <p class="text-xs text-gray-500 mt-1">
                                            {{ gmdate('i:s', $podcast->duration_seconds) }} min
                                            - {{ $podcast->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                    @if ($podcast->audio_path)
                                        <span class="text-green-400 text-xs flex items-center gap-1 shrink-0 ml-2">
                                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M9.383 3.076A1 1 0 0110 4v12a1 1 0 01-1.707.707L4.586 13H2a1 1 0 01-1-1V8a1 1 0 011-1h2.586l3.707-3.707a1 1 0 011.09-.217zM14.657 2.929a1 1 0 011.414 0A9.972 9.972 0 0119 10a9.972 9.972 0 01-2.929 7.071 1 1 0 01-1.414-1.414A7.971 7.971 0 0017 10c0-2.21-.894-4.208-2.343-5.657a1 1 0 010-1.414zm-2.829 2.828a1 1 0 011.415 0A5.983 5.983 0 0115 10a5.984 5.984 0 01-1.757 4.243 1 1 0 01-1.415-1.415A3.984 3.984 0 0013 10a3.983 3.983 0 00-1.172-2.828 1 1 0 010-1.415z" clip-rule="evenodd"/>
                                            </svg>
                                            Áudio
                                        </span>
                                    @else
                                        <span class="text-yellow-400 text-xs flex items-center gap-1 shrink-0 ml-2">
                                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M9.383 3.076A1 1 0 0110 4v12a1 1 0 01-1.707.707L4.586 13H2a1 1 0 01-1-1V8a1 1 0 011-1h2.586l3.707-3.707a1 1 0 011.09-.217zM14.657 2.929a1 1 0 011.414 0A9.972 9.972 0 0119 10a9.972 9.972 0 01-2.929 7.071 1 1 0 01-1.414-1.414A7.971 7.971 0 0017 10c0-2.21-.894-4.208-2.343-5.657a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                            </svg>
                                            Script
                                        </span>
                                    @endif
                                </div>
                            </a>
                        </div>
                    @empty
                        <p class="text-gray-500 col-span-3 text-center py-8">Nenhum podcast gerado ainda.</p>
                    @endforelse
                </div>
                <div class="mt-6">{{ $podcasts->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
