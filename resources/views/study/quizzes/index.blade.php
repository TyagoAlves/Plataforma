<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-100 leading-tight">Simulados</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="backdrop-blur-xl bg-white/5 border border-white/10 rounded-2xl p-6">
                <div class="space-y-4">
                    @forelse ($quizzes as $quiz)
                        <div class="backdrop-blur-lg bg-white/5 border border-white/10 rounded-xl p-4 hover:bg-white/10 transition group relative">
                            <a href="{{ route('study.quizzes.show', $quiz) }}" class="flex items-center justify-between">
                                <div>
                                    <h4 class="font-medium text-indigo-300">{{ $quiz->title }}</h4>
                                    <p class="text-xs text-gray-500 mt-1">{{ $quiz->questions_count }} questões - {{ $quiz->created_at->diffForHumans() }}</p>
                                </div>
                                <div class="text-right">
                                    @if ($quiz->completed)
                                        <span class="text-sm font-medium {{ $quiz->correct_answers >= $quiz->total_questions / 2 ? 'text-green-400' : 'text-yellow-400' }}">
                                            {{ $quiz->correct_answers }}/{{ $quiz->total_questions }}
                                        </span>
                                    @else
                                        <span class="text-sm text-gray-400">Pendente</span>
                                    @endif
                                </div>
                            </a>
                            <form method="POST" action="{{ route('study.quizzes.destroy', $quiz) }}" class="absolute top-2 right-2" onsubmit="return confirm('Excluir este simulado?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 rounded-lg text-gray-500 hover:text-red-400 hover:bg-red-900/20 transition opacity-0 group-hover:opacity-100">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-8">Nenhum simulado ainda.</p>
                    @endforelse
                </div>

                <div class="mt-6">
                    {{ $quizzes->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
