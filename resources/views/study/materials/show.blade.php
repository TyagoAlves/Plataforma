<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-100 leading-tight">{{ $material->title }}</h2>
            <a href="{{ route('study.dashboard') }}" class="text-sm text-gray-400 hover:text-white transition">Voltar</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="backdrop-blur-xl bg-white/5 border border-white/10 rounded-2xl p-6">
                <div class="flex items-center space-x-4 mb-4">
                    <span class="px-3 py-1 rounded-full text-xs font-medium bg-indigo-600/30 text-indigo-300">
                        {{ $material->file_type ? strtoupper($material->file_type) : 'Texto' }}
                    </span>
                    <span class="text-sm text-gray-500">{{ $material->status }}</span>
                    @if ($material->subject)
                        <span class="text-sm text-gray-400">{{ $material->subject->name }}</span>
                    @endif
                </div>

                @if ($material->content)
                    <div class="prose prose-invert max-w-none text-gray-300 text-sm leading-relaxed">
                        {{ nl2br(e($material->content)) }}
                    </div>
                @endif
            </div>

            <div class="grid grid-cols-3 gap-4">
                <form method="POST" action="{{ route('study.quizzes.generate') }}">
                    @csrf
                    <input type="hidden" name="study_material_id" value="{{ $material->id }}">
                    <button type="submit"
                        class="w-full backdrop-blur-xl bg-white/5 border border-white/10 rounded-xl p-4 hover:bg-indigo-600/20 transition text-center group">
                        <div class="text-2xl mb-2 text-indigo-400 group-hover:text-indigo-300">📝</div>
                        <div class="text-sm font-medium text-gray-200">Gerar Simulado</div>
                        <div class="text-xs text-gray-500 mt-1">Questões com feedback</div>
                    </button>
                </form>

                <form method="POST" action="{{ route('study.slides.generate') }}">
                    @csrf
                    <input type="hidden" name="study_material_id" value="{{ $material->id }}">
                    <button type="submit"
                        class="w-full backdrop-blur-xl bg-white/5 border border-white/10 rounded-xl p-4 hover:bg-purple-600/20 transition text-center group">
                        <div class="text-2xl mb-2 text-purple-400 group-hover:text-purple-300">📊</div>
                        <div class="text-sm font-medium text-gray-200">Gerar Slides</div>
                        <div class="text-xs text-gray-500 mt-1">Resumo visual</div>
                    </button>
                </form>

                <form method="POST" action="{{ route('study.podcasts.generate') }}">
                    @csrf
                    <input type="hidden" name="study_material_id" value="{{ $material->id }}">
                    <button type="submit"
                        class="w-full backdrop-blur-xl bg-white/5 border border-white/10 rounded-xl p-4 hover:bg-green-600/20 transition text-center group">
                        <div class="text-2xl mb-2 text-green-400 group-hover:text-green-300">🎧</div>
                        <div class="text-sm font-medium text-gray-200">Gerar Podcast</div>
                        <div class="text-xs text-gray-500 mt-1">Áudio de debate</div>
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
