<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-100 leading-tight">{{ $quiz->title }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            @if ($quiz->completed)
                <div class="backdrop-blur-xl bg-white/5 border border-white/10 rounded-2xl p-6 text-center">
                    <p class="text-gray-400">Este simulado já foi respondido.</p>
                    <a href="{{ route('study.quizzes.results', $quiz) }}" class="inline-block mt-4 text-indigo-400 hover:text-indigo-300">Ver resultados</a>
                </div>
            @else
                <form method="POST" action="{{ route('study.quizzes.answer', $quiz) }}">
                    @csrf
                    <div class="space-y-6">
                        @foreach ($quiz->questions as $index => $question)
                            <div class="backdrop-blur-xl bg-white/5 border border-white/10 rounded-2xl p-6">
                                <h3 class="text-gray-200 font-medium mb-4">Questão {{ $index + 1 }}</h3>
                                <p class="text-gray-300 mb-4">{{ $question->question }}</p>
                                <div class="space-y-2">
                                    @foreach ($question->options as $option)
                                        <label class="flex items-center p-3 rounded-lg border border-white/10 hover:bg-white/5 cursor-pointer transition">
                                            <input type="radio" name="answers[{{ $question->id }}]" value="{{ $option }}" required
                                                class="text-indigo-600 focus:ring-indigo-500 border-gray-600 bg-gray-800">
                                            <span class="ml-3 text-gray-300 text-sm">{{ $option }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-6 text-right">
                        <x-primary-button>Enviar Respostas</x-primary-button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</x-app-layout>
