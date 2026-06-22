<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-100 leading-tight">Resultados: {{ $quiz->title }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="backdrop-blur-xl bg-white/5 border border-white/10 rounded-2xl p-6 text-center">
                <div class="text-5xl font-bold {{ $quiz->correct_answers >= $quiz->total_questions / 2 ? 'text-green-400' : 'text-yellow-400' }}">
                    {{ $quiz->correct_answers }}/{{ $quiz->total_questions }}
                </div>
                <p class="text-gray-400 mt-2">
                    {{ $quiz->total_questions > 0 ? round(($quiz->correct_answers / $quiz->total_questions) * 100) : 0 }}% de acerto
                </p>
            </div>

            @foreach ($quiz->questions as $index => $question)
                <div class="backdrop-blur-xl bg-white/5 border border-white/10 rounded-2xl p-6
                    {{ $question->answered_correctly ? 'border-green-500/30' : 'border-red-500/30' }}">
                    <h3 class="text-gray-200 font-medium mb-2">Questão {{ $index + 1 }}</h3>
                    <p class="text-gray-300 mb-3">{{ $question->question }}</p>
                    <div class="text-sm space-y-1">
                        <p>Sua resposta: <span class="{{ $question->answered_correctly ? 'text-green-400' : 'text-red-400' }}">{{ $question->user_answer ?? 'N/A' }}</span></p>
                        @unless ($question->answered_correctly)
                            <p>Resposta correta: <span class="text-green-400">{{ $question->correct_answer }}</span></p>
                        @endunless
                    </div>
                </div>
            @endforeach

            <div class="text-center">
                <a href="{{ route('study.quizzes.index') }}" class="text-indigo-400 hover:text-indigo-300">Voltar aos Simulados</a>
            </div>
        </div>
    </div>
</x-app-layout>
