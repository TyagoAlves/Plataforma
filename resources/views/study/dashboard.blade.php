<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-100 leading-tight">
            {{ __('Painel de Estudos') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <div class="backdrop-blur-xl bg-white/5 border border-white/10 rounded-2xl p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-medium text-gray-200">Matérias</h3>
                    <a href="{{ route('study.subjects.index') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:ring-offset-2 focus:ring-offset-gray-900 transition ease-in-out duration-150">
                        Gerenciar
                    </a>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @forelse ($subjects as $subject)
                        <div class="backdrop-blur-lg bg-white/5 border border-white/10 rounded-xl p-4 hover:bg-white/10 transition">
                            <a href="{{ route('study.subjects.show', $subject) }}" class="block">
                                <h4 class="font-medium text-indigo-300">{{ $subject->name }}</h4>
                                <p class="text-sm text-gray-400 mt-1">{{ $subject->study_materials_count }} material(is)</p>
                            </a>
                        </div>
                    @empty
                        <p class="text-gray-500 col-span-3 text-center py-4">Nenhuma matéria cadastrada ainda.</p>
                    @endforelse
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="backdrop-blur-xl bg-white/5 border border-white/10 rounded-2xl p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-200">Simulados</h3>
                        <a href="{{ route('study.quizzes.index') }}" class="text-sm text-indigo-400 hover:text-indigo-300">Ver todos</a>
                    </div>
                    @forelse ($recentQuizzes as $quiz)
                        <div class="text-sm py-2 border-b border-white/5 last:border-0">
                            <a href="{{ route('study.quizzes.show', $quiz) }}" class="text-gray-300 hover:text-white">{{ $quiz->title }}</a>
                            <span class="text-gray-500 ml-2">{{ $quiz->completed ? $quiz->correct_answers.'/'.$quiz->total_questions : 'Pendente' }}</span>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm">Nenhum simulado ainda.</p>
                    @endforelse
                </div>

                <div class="backdrop-blur-xl bg-white/5 border border-white/10 rounded-2xl p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-200">Slides</h3>
                        <a href="{{ route('study.slides.index') }}" class="text-sm text-indigo-400 hover:text-indigo-300">Ver todos</a>
                    </div>
                    @forelse ($recentSlides as $slide)
                        <div class="text-sm py-2 border-b border-white/5 last:border-0">
                            <a href="{{ route('study.slides.show', $slide) }}" class="text-gray-300 hover:text-white">{{ $slide->title }}</a>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm">Nenhum slide ainda.</p>
                    @endforelse
                </div>

                <div class="backdrop-blur-xl bg-white/5 border border-white/10 rounded-2xl p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-200">Podcasts</h3>
                        <a href="{{ route('study.podcasts.index') }}" class="text-sm text-indigo-400 hover:text-indigo-300">Ver todos</a>
                    </div>
                    @forelse ($recentPodcasts as $podcast)
                        <div class="text-sm py-2 border-b border-white/5 last:border-0">
                            <a href="{{ route('study.podcasts.show', $podcast) }}" class="text-gray-300 hover:text-white">{{ $podcast->title }}</a>
                            <span class="text-gray-500 ml-2">{{ gmdate('i:s', $podcast->duration_seconds) }}</span>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm">Nenhum podcast ainda.</p>
                    @endforelse
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="backdrop-blur-xl bg-white/5 border border-white/10 rounded-2xl p-6 text-center">
                    <a href="{{ route('study.materials.create') }}" class="inline-flex items-center px-6 py-3 bg-indigo-600 border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-widest hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:ring-offset-2 focus:ring-offset-gray-900 transition ease-in-out duration-150">
                        + Novo Material de Estudo
                    </a>
                    <p class="text-gray-500 text-sm mt-2">Faça upload de PDFs, textos ou digite sua matéria</p>
                </div>

                <div class="backdrop-blur-xl bg-gradient-to-br from-indigo-500/10 to-purple-500/10 border border-indigo-500/20 rounded-2xl p-6 text-center group hover:bg-white/5 transition">
                    <a href="{{ route('study.opencode.index') }}" class="block">
                        <p class="text-3xl mb-2">🤖</p>
                        <p class="text-lg font-medium text-indigo-300 group-hover:text-indigo-200 transition">OpenCode Assistente</p>
                        <p class="text-gray-500 text-sm mt-1">Peça ajuda com código, gere quizzes, execute scripts</p>
                        <span class="inline-block mt-3 text-xs text-indigo-400/60 group-hover:text-indigo-400 transition">Abir →</span>
                    </a>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
