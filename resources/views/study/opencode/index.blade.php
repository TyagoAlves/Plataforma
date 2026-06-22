<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-100 leading-tight">OpenCode - Ambiente de Desenvolvimento</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="backdrop-blur-xl bg-white/5 border border-white/10 rounded-2xl overflow-hidden" x-data="opencode()" x-init="init()">
                <div class="flex h-[80vh]">
                    <!-- Sidebar -->
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

                    <!-- Code Editor Panel -->
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
                        <div x-show="saveMessage" x-text="saveMessage" class="px-4 py-2 text-sm border-t border-white/10"
                            :class="saveMessage.includes('✅') ? 'text-green-400' : 'text-red-400'"></div>
                    </div>

                    <!-- Chat Panel -->
                    <div class="flex-1 flex flex-col bg-gradient-to-b from-gray-950 via-gray-900 to-gray-950 relative" x-show="activeTab === 'chat'">
                        <!-- Chat Header -->
                        <div class="px-4 py-2 border-b border-white/10 flex items-center justify-between">
                            <span class="text-xs text-gray-500 flex items-center gap-1">
                                <span class="w-2 h-2 rounded-full" :class="geminiAvailable ? 'bg-green-400' : 'bg-yellow-400'"></span>
                                <span x-text="geminiAvailable ? 'Gemini Online' : 'Modo offline'"></span>
                            </span>
                            <button @click="clearChat" class="text-xs text-gray-500 hover:text-gray-300 transition">
                                Limpar chat
                            </button>
                        </div>

                        <!-- Messages -->
                        <div class="flex-1 overflow-y-auto p-4 space-y-4" x-ref="chatBox">
                            <template x-for="(msg, i) in chatMessages" :key="i">
                                <div class="flex" :class="msg.role === 'user' ? 'justify-end' : 'justify-start'">
                                    <div class="max-w-[85%] rounded-2xl px-4 py-3 text-sm"
                                        :class="msg.role === 'user'
                                            ? 'bg-indigo-600/20 border border-indigo-500/30 text-gray-100'
                                            : msg.role === 'error'
                                                ? 'bg-red-900/20 border border-red-500/30 text-red-300'
                                                : 'bg-white/5 border border-white/10 text-gray-300'">
                                        <p class="text-xs font-medium mb-1"
                                            :class="msg.role === 'user' ? 'text-indigo-300 text-right' : msg.role === 'error' ? 'text-red-400' : 'text-emerald-300'"
                                            x-text="msg.role === 'user' ? 'Você' : msg.role === 'error' ? 'Erro' : 'IA'"></p>
                                        <div class="whitespace-pre-wrap leading-relaxed" x-html="renderMessage(msg.content)"></div>
                                    </div>
                                </div>
                            </template>

                            <!-- Loading indicator -->
                            <div x-show="chatLoading" class="flex justify-start">
                                <div class="bg-white/5 border border-white/10 rounded-2xl px-4 py-3 text-sm">
                                    <div class="flex items-center gap-2">
                                        <span class="inline-flex gap-1">
                                            <span class="w-2 h-2 bg-indigo-400 rounded-full animate-bounce" style="animation-delay: 0ms"></span>
                                            <span class="w-2 h-2 bg-indigo-400 rounded-full animate-bounce" style="animation-delay: 150ms"></span>
                                            <span class="w-2 h-2 bg-indigo-400 rounded-full animate-bounce" style="animation-delay: 300ms"></span>
                                        </span>
                                        <span class="text-gray-500 text-xs" x-text="loadingText"></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Welcome message -->
                            <div x-show="chatMessages.length === 0 && !chatLoading" class="flex items-center justify-center h-full">
                                <div class="text-center text-gray-500">
                                    <p class="text-4xl mb-3">🤖</p>
                                    <p class="text-lg mb-1">OpenCode Assistente</p>
                                    <p class="text-sm max-w-md">
                                        Peça ajuda com código, gere quizzes, ou mande executar código.<br>
                                        <span class="text-xs text-gray-600">Ex: "roda um loop", "soma 5 + 3", "teste com array"</span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Input -->
                        <div class="p-4 border-t border-white/10">
                            <form @submit.prevent="sendChat" class="flex gap-2">
                                <div class="flex-1 relative">
                                    <input type="text" x-model="chatInput" placeholder="Peça ao sistema..."
                                        class="w-full bg-gray-800/50 border border-white/10 rounded-xl px-4 py-3 pr-10 text-sm text-gray-200 placeholder-gray-500 focus:outline-none focus:border-indigo-500/50 focus:ring-1 focus:ring-indigo-500/30 transition"
                                        :disabled="chatLoading"
                                        x-ref="chatInput"
                                        @keydown.window="if ($event.key === '/' && activeTab !== 'chat') { $event.preventDefault(); activeTab = 'chat'; $nextTick(() => $refs.chatInput?.focus()); }">
                                    <button type="button" @click="chatInput = ''" x-show="chatInput.length > 0"
                                        class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-300 text-lg leading-none">
                                        ×
                                    </button>
                                </div>
                                <button type="submit" :disabled="!chatInput.trim() || chatLoading"
                                    class="px-5 py-3 bg-indigo-600 rounded-xl text-white text-sm font-medium hover:bg-indigo-500 disabled:opacity-40 disabled:cursor-not-allowed transition flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19V5m0 0l-7 7m7-7l7 7"/>
                                    </svg>
                                    Enviar
                                </button>
                            </form>
                            <p class="text-xs text-gray-600 mt-1.5 text-center">
                                Pressione <kbd class="px-1 py-0.5 bg-gray-800 rounded text-gray-400">/</kbd> para focar no chat
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Tab Switcher -->
                <div class="border-t border-white/10 flex">
                    <button @click="activeTab = 'code'" class="flex-1 py-2 text-sm font-medium transition"
                        :class="activeTab === 'code' ? 'text-indigo-300 bg-white/5' : 'text-gray-500 hover:text-gray-300'">
                        Editor
                    </button>
                    <button @click="activeTab = 'chat'" class="flex-1 py-2 text-sm font-medium transition relative"
                        :class="activeTab === 'chat' ? 'text-indigo-300 bg-white/5' : 'text-gray-500 hover:text-gray-300'">
                        Chat IA
                        <span x-show="chatMessages.length > 0" class="ml-1 text-xs text-gray-500" x-text="`(${chatMessages.length})`"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
        .msg-code-block {
            background: rgba(0,0,0,0.4);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 0.5rem;
            padding: 0.75rem;
            margin: 0.5rem 0;
            overflow-x: auto;
            font-family: ui-monospace, monospace;
            font-size: 0.8125rem;
            line-height: 1.5;
        }
        .msg-inline-code {
            background: rgba(0,0,0,0.3);
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: 0.25rem;
            padding: 0.125rem 0.375rem;
            font-family: ui-monospace, monospace;
            font-size: 0.8125rem;
        }
        .msg-bold { font-weight: 600; }
        kbd {
            border: 1px solid rgba(255,255,255,0.1);
            font-family: inherit;
        }
        .chat-msg-enter {
            animation: slideIn 0.2s ease-out;
        }
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>

    <script>
        function opencode() {
            return {
                activeTab: 'chat',
                files: [],
                selectedPath: null,
                currentFile: null,
                fileContent: '',
                saveMessage: '',
                chatInput: '',
                chatMessages: [],
                chatLoading: false,
                loadingText: 'Processando...',
                geminiAvailable: false,

                async init() {
                    const res = await fetch('{{ route("study.opencode.browse") }}?path={{ base_path() }}');
                    const data = await res.json();
                    if (data.type === 'dir') {
                        this.files = data.files.map(f => ({ ...f, expanded: false, children: [] }));
                    }
                    this.showWelcome();
                },

                showWelcome() {
                    const welcome = 'Olá! 👋 Bem-vindo ao OpenCode!\n\nComandos rápidos:\n\\- "roda um loop" - executa código\n\\- "soma 5 + 3" - cálculo matemático\n\\- "tabuada do 7" - tabuada\n\\- "array" - manipulação de arrays\n\\- "imc" - calculadora de IMC\n\\- "fibonacci" - sequência de Fibonacci\n\\- "ajuda" - lista completa de comandos\n\nComo posso ajudar?';
                    this.chatMessages.push({ role: 'assistant', content: welcome });
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
                    this.loadingText = 'Analisando mensagem...';
                    this.activeTab = 'chat';

                    try {
                        this.loadingText = 'Consultando IA...';
                        const res = await fetch('{{ route("study.opencode.chat") }}', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body: JSON.stringify({ message: msg, context: this.currentFile ? this.fileContent.substring(0, 2000) : '' })
                        });
                        if (!res.ok) throw new Error(`HTTP ${res.status}`);
                        const data = await res.json();
                        if (data.response) {
                            this.chatMessages.push({ role: 'assistant', content: data.response });
                        } else {
                            throw new Error('Resposta vazia');
                        }
                    } catch (e) {
                        this.loadingText = 'Tentando modo offline...';
                        try {
                            const fallbackRes = await fetch('{{ route("study.opencode.local-chat") }}', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                                body: JSON.stringify({ message: msg })
                            });
                            if (fallbackRes.ok) {
                                const fallbackData = await fallbackRes.json();
                                if (fallbackData.response) {
                                    this.chatMessages.push({ role: 'assistant', content: fallbackData.response });
                                    this.chatLoading = false;
                                    this.scrollToBottom();
                                    return;
                                }
                            }
                        } catch (fbErr) {}

                        this.chatMessages.push({
                            role: 'error',
                            content: '❌ Erro ao conectar com a IA. Verifique sua conexão ou tente novamente.\n\nComandos offline disponíveis: "ola", "php", "quiz", "slide", "ajuda", "roda", "soma", "teste", "array"'
                        });
                    }
                    this.chatLoading = false;
                    this.scrollToBottom();
                },

                clearChat() {
                    this.chatMessages = [];
                    this.showWelcome();
                },

                scrollToBottom() {
                    this.$nextTick(() => {
                        if (this.$refs.chatBox) {
                            this.$refs.chatBox.scrollTop = this.$refs.chatBox.scrollHeight;
                        }
                    });
                },

                renderMessage(content) {
                    if (!content) return '';
                    let html = this.escapeHtml(content);

                    // Code blocks ```lang\n...```
                    html = html.replace(/```(\w*)\s*\n?([\s\S]*?)```/g, (match, lang, code) => {
                        const langLabel = lang ? `<span style="color: #818cf8; font-size: 0.7rem; display: block; margin-bottom: 0.25rem;">${this.escapeHtml(lang)}</span>` : '';
                        return `<div class="msg-code-block">${langLabel}<pre style="margin:0; white-space: pre-wrap;">${this.escapeHtml(code)}</pre></div>`;
                    });

                    // Inline code `code`
                    html = html.replace(/`([^`]+)`/g, '<code class="msg-inline-code">$1</code>');

                    // Bold **text**
                    html = html.replace(/\*\*(.+?)\*\*/g, '<strong class="msg-bold">$1</strong>');

                    // Lines starting with "- " or "* " or digits.
                    html = html.replace(/^[\s]*[-*]\s+(.+)$/gm, '<span style="display: block; padding-left: 1rem; position: relative;">&bull; $1</span>');

                    return html;
                },

                escapeHtml(text) {
                    const div = document.createElement('div');
                    div.textContent = text;
                    return div.innerHTML;
                }
            }
        }
    </script>
</x-app-layout>
