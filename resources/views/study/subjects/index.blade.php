<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-100 leading-tight">Minhas Matérias</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="backdrop-blur-xl bg-white/5 border border-white/10 rounded-2xl p-6">
                @if (session('error'))
                    <div class="mb-4 px-4 py-3 rounded-lg bg-red-900/20 border border-red-500/30 text-red-300 text-sm">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-medium text-gray-200">Matérias Cadastradas</h3>
                    <button x-data @click="$dispatch('open-modal', 'create-subject')"
                        class="px-4 py-2 bg-indigo-600 rounded-lg text-white text-xs font-semibold uppercase hover:bg-indigo-500 transition">
                        + Nova Matéria
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @forelse ($subjects as $subject)
                        <div class="backdrop-blur-lg bg-white/5 border border-white/10 rounded-xl p-4 hover:bg-white/10 transition group relative">
                            <a href="{{ route('study.subjects.show', $subject) }}">
                                <h4 class="font-medium text-indigo-300">{{ $subject->name }}</h4>
                                @if ($subject->description)
                                    <p class="text-sm text-gray-400 mt-1">{{ Str::limit($subject->description, 80) }}</p>
                                @endif
                                <p class="text-xs text-gray-500 mt-2">{{ $subject->study_materials_count }} material(is)</p>
                            </a>
                            @if ($subject->study_materials_count === 0)
                                <form method="POST" action="{{ route('study.subjects.destroy', $subject) }}" class="absolute top-2 right-2" onsubmit="return confirm('Excluir esta matéria?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded-lg text-gray-500 hover:text-red-400 hover:bg-red-900/20 transition opacity-0 group-hover:opacity-100">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            @endif
                        </div>
                    @empty
                        <p class="text-gray-500 col-span-3 text-center py-8">Nenhuma matéria cadastrada. Crie sua primeira matéria!</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div x-data="{ show: false }" x-show="show" x-on:open-modal.window="show = true" x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center" style="display: none;">
        <div class="fixed inset-0 bg-black/50" @click="show = false"></div>
        <div class="relative backdrop-blur-xl bg-gray-900/90 border border-white/10 rounded-2xl p-6 w-full max-w-md mx-4">
            <h3 class="text-lg font-medium text-gray-200 mb-4">Nova Matéria</h3>
            <form method="POST" action="{{ route('study.subjects.store') }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <x-input-label for="name" value="Nome da Matéria" />
                        <x-text-input id="name" name="name" class="block mt-1 w-full" required />
                    </div>
                    <div>
                        <x-input-label for="description" value="Descrição (opcional)" />
                        <textarea id="description" name="description" rows="3"
                            class="block mt-1 w-full rounded-lg border-gray-700 bg-gray-800 text-gray-200 focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                    </div>
                </div>
                <div class="flex justify-end mt-6 space-x-3">
                    <button type="button" @click="show = false"
                        class="px-4 py-2 text-sm text-gray-400 hover:text-white transition">Cancelar</button>
                    <x-primary-button>Criar</x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
