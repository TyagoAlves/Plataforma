<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-100 leading-tight">Novo Material de Estudo</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="backdrop-blur-xl bg-white/5 border border-white/10 rounded-2xl p-8">
                <form method="POST" action="{{ route('study.materials.store') }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <div>
                        <x-input-label for="title" value="Título do Material" />
                        <x-text-input id="title" name="title" class="block mt-1 w-full" required />
                        <x-input-error :messages="$errors->get('title')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="subject_id" value="Matéria (opcional)" />
                        <select id="subject_id" name="subject_id"
                            class="block mt-1 w-full rounded-lg border-gray-700 bg-gray-800 text-gray-200 focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Sem matéria</option>
                            @foreach ($subjects as $subject)
                                <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                                    {{ $subject->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <x-input-label for="file" value="Upload de Arquivo (PDF, TXT, EPUB)" />
                        <input type="file" id="file" name="file" accept=".pdf,.txt,.epub"
                            class="block mt-1 w-full text-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-600 file:text-white hover:file:bg-indigo-500" />
                        <x-input-error :messages="$errors->get('file')" class="mt-2" />
                    </div>

                    <div class="text-center text-gray-500 text-sm">— ou —</div>

                    <div>
                        <x-input-label for="content" value="Digite o conteúdo da matéria" />
                        <textarea id="content" name="content" rows="10"
                            class="block mt-1 w-full rounded-lg border-gray-700 bg-gray-800 text-gray-200 focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Cole ou digite aqui o conteúdo da sua matéria para processamento por IA..."></textarea>
                        <x-input-error :messages="$errors->get('content')" class="mt-2" />
                    </div>

                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('study.dashboard') }}" class="px-4 py-2 text-sm text-gray-400 hover:text-white transition">Cancelar</a>
                        <x-primary-button>Enviar Material</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
