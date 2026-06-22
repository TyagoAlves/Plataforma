<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-100 leading-tight">Slides Explicativos</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="backdrop-blur-xl bg-white/5 border border-white/10 rounded-2xl p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @forelse ($slides as $slide)
                        <div class="backdrop-blur-lg bg-white/5 border border-white/10 rounded-xl p-4 hover:bg-white/10 transition">
                            <a href="{{ route('study.slides.show', $slide) }}" class="block">
                                <h4 class="font-medium text-purple-300">{{ $slide->title }}</h4>
                                <p class="text-xs text-gray-500 mt-1">{{ count($slide->slides) }} slides - {{ $slide->created_at->diffForHumans() }}</p>
                            </a>
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
