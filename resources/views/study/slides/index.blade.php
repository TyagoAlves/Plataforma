<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-100 leading-tight">Slides Explicativos</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="backdrop-blur-xl bg-white/5 border border-white/10 rounded-2xl p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @forelse ($slides as $slide)
                        <div class="backdrop-blur-lg bg-white/5 border border-white/10 rounded-xl p-4 hover:bg-white/10 transition group relative">
                            <a href="{{ route('study.slides.show', $slide) }}" class="block">
                                <h4 class="font-medium text-purple-300">{{ $slide->title }}</h4>
                                <p class="text-xs text-gray-500 mt-1">{{ count($slide->slides) }} slides - {{ $slide->created_at->diffForHumans() }}</p>
                            </a>
                            <form method="POST" action="{{ route('study.slides.destroy', $slide) }}" class="absolute top-2 right-2" onsubmit="return confirm('Excluir estes slides?')">
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
                        <p class="text-gray-500 col-span-3 text-center py-8">Nenhum slide gerado ainda.</p>
                    @endforelse
                </div>
                <div class="mt-6">{{ $slides->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
