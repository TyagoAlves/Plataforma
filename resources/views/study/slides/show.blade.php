<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-100 leading-tight">{{ $slide->title }}</h2>
            <a href="{{ route('study.slides.index') }}" class="text-sm text-gray-400 hover:text-white transition">Voltar</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div x-data="{ current: 0 }" class="space-y-4">
                <div class="flex justify-between items-center text-sm text-gray-400">
                    <span>Slide <span x-text="current + 1"></span> de {{ count($slide->slides) }}</span>
                </div>

                <template x-for="(slide, index) in {{ json_encode($slide->slides) }}" :key="index">
                    <div x-show="current === index"
                        class="backdrop-blur-xl bg-white/5 border border-white/10 rounded-2xl p-12 min-h-[400px] flex flex-col items-center justify-center text-center transition-opacity duration-300">
                        <h3 class="text-2xl font-bold text-indigo-300 mb-6" x-text="slide.title"></h3>
                        <p class="text-gray-300 text-lg leading-relaxed whitespace-pre-line max-w-2xl" x-text="slide.content"></p>
                    </div>
                </template>

                <div class="flex justify-between items-center">
                    <button @click="current = Math.max(0, current - 1)" :disabled="current === 0"
                        class="px-4 py-2 rounded-lg border border-white/10 text-gray-300 hover:bg-white/5 disabled:opacity-30 disabled:cursor-not-allowed transition">
                        ← Anterior
                    </button>

                    <div class="flex space-x-2">
                        <template x-for="(_, i) in {{ json_encode($slide->slides) }}" :key="i">
                            <button @click="current = i"
                                class="w-2.5 h-2.5 rounded-full transition"
                                :class="current === i ? 'bg-indigo-400' : 'bg-gray-600 hover:bg-gray-500'">
                            </button>
                        </template>
                    </div>

                    <button @click="current = Math.min({{ count($slide->slides) - 1 }}, current + 1)" :disabled="current === {{ count($slide->slides) - 1 }}"
                        class="px-4 py-2 rounded-lg border border-white/10 text-gray-300 hover:bg-white/5 disabled:opacity-30 disabled:cursor-not-allowed transition">
                        Próximo →
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
