<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-100 leading-tight">Simulados</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="backdrop-blur-xl bg-white/5 border border-white/10 rounded-2xl p-6">
                <div class="space-y-4">
                    @forelse ($quizzes as $quiz)
                        <div class="backdrop-blur-lg bg-white/5 border border-white/10 rounded-xl p-4 hover:bg-white/10 transition">
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
