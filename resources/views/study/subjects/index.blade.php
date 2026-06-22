<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-100 leading-tight">Minhas Matérias</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="backdrop-blur-xl bg-white/5 border border-white/10 rounded-2xl p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-medium text-gray-200">Matérias Cadastradas</h3>
                    <button x-data @click="$dispatch('open-modal', 'create-subject')"
                        class="px-4 py-2 bg-indigo-600 rounded-lg text-white text-xs font-semibold uppercase hover:bg-indigo-500 transition">
                        + Nova Matéria
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @forelse ($subjects as $subject)
                        <div class="backdrop-blur-lg bg-white/5 border border-white/10 rounded-xl p-4 hover:bg-white/10 transition group">
                            <a href="{{ route('study.subjects.show', $subject) }}">
                                <h4 class="font-medium text-indigo-300">{{ $subject->name }}</h4>
                                @if ($subject->description)
                                    <p class="text-sm text-gray-400 mt-1">{{ Str::limit($subject->description, 80) }}</p>
                                @endif
                                <p class="text-xs text-gray-500 mt-2">{{ $subject->study_materials_count }} material(is)</p>
                            </a>
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
