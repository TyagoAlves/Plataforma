<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-100 leading-tight">OpenCode - Ambiente de Desenvolvimento</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="backdrop-blur-xl bg-white/5 border border-white/10 rounded-2xl overflow-hidden" x-data="opencode()" x-init="init()">
                <div class="flex h-[80vh]">
                    <div class="w-56 border-r border-white/10 overflow-y-auto p-2 space-y-1 shrink-0">
                        <template x-for="item in files" :key="item.path">
                            <div>
                                <div @click="item.type === 'directory' ? toggleDir(item) : openFile(item)"
                                    class="flex items-center px-2 py-1.5 rounded-lg text-sm cursor-pointer"
                                    :class="selectedPath === item.path ? 'bg-indigo-600/30 text-indigo-200' : 'text-gray-400 hover:bg-white/5 hover:text-gray-200'">
                                    <span class="mr-1.5" x-text="item.type === 'directory' ? (item.expanded ? '📂' : '📁') : '📄'"></span>
                                    <span class="truncate" x-text="item.name"></span>
                                </div>
                                <div x-show="item.expanded" x-cloak class="ml-3 space-y-1">
                                    <template x-for="child in item.children" :key="child.path">
                                        <div @click="child.type === 'file' ? openFile(child) : null"
                                            class="flex items-center px-2 py-1.5 rounded-lg text-sm cursor-pointer"
                                            :class="selectedPath === child.path ? 'bg-indigo-600/30 text-indigo-200' : 'text-gray-400 hover:bg-white/5 hover:text-gray-200'">
                                            <span class="mr-1.5" x-text="child.type === 'directory' ? '📁' : '📄'"></span>
                                            <span class="truncate" x-text="child.name"></span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div class="flex-1 flex flex-col border-r border-white/10" x-show="activeTab === 'code'">
                        <div class="border-b border-white/10 px-4 py-2 text-sm text-gray-500 flex items-center justify-between">
                            <span x-text="currentFile || 'Selecione um arquivo'"></span>
                            <button @click="saveFile()" x-show="currentFile" class="px-3 py-1 bg-indigo-600 rounded text-xs text-white hover:bg-indigo-500 transition">
                                Salvar
                            </button>
                        </div>
                        <textarea x-show="currentFile" x-model="fileContent"
                            class="flex-1 bg-gray-950 text-gray-200 font-mono text-sm p-4 border-0 resize-none focus:outline-none focus:ring-0"
                            spellcheck="false"></textarea>
                        <div x-show="!currentFile" class="flex-1 flex items-center justify-center text-gray-500">
                            <div class="text-center">
                                <p class="text-lg mb-2">📁 OpenCode</p>
                                <p class="text-sm">Selecione um arquivo para editar</p>
                            </div>
                        </div>
                        <div x-show="saveMessage" x-text="saveMessage" class="px-4 py-2 text-sm text-green-400 border-t border-white/10"></div>
                    </div>

                    <div class="flex-1 flex flex-col bg-gradient-to-b from-gray-950 via-gray-900 to-gray-950 relative" x-show="activeTab === 'chat'">
                        <div class="flex-1 overflow-y-auto p-4 space-y-4" x-ref="chatBox">
                            <template x-for="(msg, i) in chatMessages" :key="i">
                                <div class="flex" :class="msg.role === 'user' ? 'justify-end' : 'justify-start'">
                                    <div class="max-w-[80%] rounded-2xl px-4 py-3 text-sm"
                                        :class="msg.role === 'user'
                                            ? 'bg-indigo-600/20 border border-indigo-500/30 text-gray-100'
                                            : 'bg-white/5 border border-white/10 text-gray-300'">
                                        <p class="text-xs font-medium mb-1" :class="msg.role === 'user' ? 'text-indigo-300 text-right' : 'text-emerald-300'"
                                            x-text="msg.role === 'user' ? 'Você' : 'IA'"></p>
                                        <p class="whitespace-pre-wrap leading-relaxed" x-text="msg.content"></p>
                                    </div>
                                </div>
                            </template>
                            <div x-show="chatLoading" class="flex justify-start">
                                <div class="bg-white/5 border border-white/10 rounded-2xl px-4 py-3 text-sm text-gray-400">
                                    <span class="inline-flex gap-1">
                                        <span class="w-2 h-2 bg-indigo-400 rounded-full animate-bounce" style="animation-delay: 0ms"></span>
                                        <span class="w-2 h-2 bg-indigo-400 rounded-full animate-bounce" style="animation-delay: 150ms"></span>
                                        <span class="w-2 h-2 bg-indigo-400 rounded-full animate-bounce" style="animation-delay: 300ms"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="p-4 border-t border-white/10">
                            <form @submit.prevent="sendChat" class="flex gap-2">
                                <input type="text" x-model="chatInput" placeholder="Peça ao sistema..."
                                    class="flex-1 bg-gray-800/50 border border-white/10 rounded-xl px-4 py-3 text-sm text-gray-200 placeholder-gray-500 focus:outline-none focus:border-indigo-500/50 focus:ring-1 focus:ring-indigo-500/30 transition"
                                    :disabled="chatLoading">
                                <button type="submit" :disabled="!chatInput.trim() || chatLoading"
                                    class="px-5 py-3 bg-indigo-600 rounded-xl text-white text-sm font-medium hover:bg-indigo-500 disabled:opacity-40 disabled:cursor-not-allowed transition">
                                    Enviar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="border-t border-white/10 flex">
                    <button @click="activeTab = 'code'" class="flex-1 py-2 text-sm font-medium transition"
                        :class="activeTab === 'code' ? 'text-indigo-300 bg-white/5' : 'text-gray-500 hover:text-gray-300'">
                        Editor
                    </button>
                    <button @click="activeTab = 'chat'" class="flex-1 py-2 text-sm font-medium transition"
                        :class="activeTab === 'chat' ? 'text-indigo-300 bg-white/5' : 'text-gray-500 hover:text-gray-300'">
                        Chat IA
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function opencode() {
            return {
                activeTab: 'code',
                files: [],
                selectedPath: null,
                currentFile: null,
                fileContent: '',
                saveMessage: '',
                chatInput: '',
                chatMessages: [],
                chatLoading: false,

                async init() {
                    const res = await fetch('{{ route("study.opencode.browse") }}?path={{ base_path() }}');
                    const data = await res.json();
                    if (data.type === 'dir') {
                        this.files = data.files.map(f => ({ ...f, expanded: false, children: [] }));
                    }
                },

                async toggleDir(item) {
                    item.expanded = !item.expanded;
                    if (item.expanded && item.children.length === 0) {
                        const res = await fetch(`{{ route("study.opencode.browse") }}?path=${encodeURIComponent(item.path)}`);
                        const data = await res.json();
                        if (data.type === 'dir') {
                            item.children = data.files;
                        }
                    }
                },

                async openFile(item) {
                    this.selectedPath = item.path;
                    this.currentFile = item.name;
                    const res = await fetch(`{{ route("study.opencode.browse") }}?path=${encodeURIComponent(item.path)}`);
                    const data = await res.json();
                    if (data.type === 'file') {
                        this.fileContent = data.content;
                    }
                },

                async saveFile() {
                    const res = await fetch('{{ route("study.opencode.save") }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ path: this.selectedPath, content: this.fileContent })
                    });
                    const data = await res.json();
                    this.saveMessage = data.success ? '✅ Arquivo salvo!' : '❌ Erro ao salvar';
                    setTimeout(() => this.saveMessage = '', 2000);
                },

                async sendChat() {
                    if (!this.chatInput.trim() || this.chatLoading) return;
                    const msg = this.chatInput.trim();
                    this.chatMessages.push({ role: 'user', content: msg });
                    this.chatInput = '';
                    this.chatLoading = true;
                    this.activeTab = 'chat';

                    try {
                        const res = await fetch('{{ route("study.opencode.chat") }}', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body: JSON.stringify({ message: msg, context: this.currentFile ? this.fileContent.substring(0, 2000) : '' })
                        });
                        const data = await res.json();
                        this.chatMessages.push({ role: 'assistant', content: data.response || 'Sem resposta.' });
                    } catch (e) {
                        this.chatMessages.push({ role: 'assistant', content: 'Erro ao conectar com a IA.' });
                    }
                    this.chatLoading = false;
                    this.$nextTick(() => { this.$refs.chatBox.scrollTop = this.$refs.chatBox.scrollHeight; });
                }
            }
        }
    </script>
</x-app-layout>
