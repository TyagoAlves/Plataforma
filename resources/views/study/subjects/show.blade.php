<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-100 leading-tight">{{ $subject->name }}</h2>
            <div class="flex space-x-2">
                <a href="{{ route('study.materials.create', ['subject_id' => $subject->id]) }}"
                    class="px-4 py-2 bg-indigo-600 rounded-lg text-white text-xs font-semibold uppercase hover:bg-indigo-500 transition">
                    + Material
                </a>
                <a href="{{ route('study.subjects.index') }}" class="px-4 py-2 text-sm text-gray-400 hover:text-white transition">Voltar</a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if ($subject->description)
                <div class="backdrop-blur-xl bg-white/5 border border-white/10 rounded-2xl p-4 mb-6">
                    <p class="text-gray-400">{{ $subject->description }}</p>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @forelse ($subject->studyMaterials as $material)
                    <div class="backdrop-blur-xl bg-white/5 border border-white/10 rounded-xl p-4 hover:bg-white/10 transition">
                        <div class="flex items-start justify-between">
                            <div>
                                <a href="{{ route('study.materials.show', $material) }}" class="text-indigo-300 font-medium hover:text-indigo-200">{{ $material->title }}</a>
                                <p class="text-xs text-gray-500 mt-1">
                                    {{ $material->file_type ? strtoupper($material->file_type) : 'Texto' }} -
                                    {{ $material->status }}
                                </p>
                            </div>
                            <div class="flex space-x-2">
                                <form method="POST" action="{{ route('study.quizzes.generate') }}">
                                    @csrf
                                    <input type="hidden" name="study_material_id" value="{{ $material->id }}">
                                    <button type="submit" class="text-xs text-indigo-400 hover:text-indigo-300" title="Gerar Simulado">Quiz</button>
                                </form>
                                <form method="POST" action="{{ route('study.slides.generate') }}">
                                    @csrf
                                    <input type="hidden" name="study_material_id" value="{{ $material->id }}">
                                    <button type="submit" class="text-xs text-purple-400 hover:text-purple-300" title="Gerar Slides">Slides</button>
                                </form>
                                <form method="POST" action="{{ route('study.podcasts.generate') }}">
                                    @csrf
                                    <input type="hidden" name="study_material_id" value="{{ $material->id }}">
                                    <button type="submit" class="text-xs text-green-400 hover:text-green-300" title="Gerar Podcast">Podcast</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-2 text-center py-12">
                        <p class="text-gray-500">Nenhum material de estudo nesta matéria.</p>
                        <a href="{{ route('study.materials.create', ['subject_id' => $subject->id]) }}"
                            class="inline-block mt-4 text-indigo-400 hover:text-indigo-300">Adicionar primeiro material</a>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
