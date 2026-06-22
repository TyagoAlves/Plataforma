<!DOCTYPE html>
<html lang="{{ str_replace("_", "-", app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Plataforma de Processos</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(["resources/css/app.css", "resources/js/app.js"])
</head>
<body class="antialiased bg-[#0f1117] text-white min-h-screen selection:bg-fuchsia-500 selection:text-white">
    <nav class="container mx-auto px-6 py-4 flex justify-between items-center">
        <div class="flex items-center gap-3">
            <svg viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-auto h-10">
                <defs>
                    <linearGradient id="gradPlataforma" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" stop-color="#8b5cf6" />
                        <stop offset="100%" stop-color="#ec4899" />
                    </linearGradient>
                </defs>
                <rect x="20" y="25" width="45" height="55" rx="4" stroke="url(#gradPlataforma)" stroke-width="6" stroke-linejoin="round"/>
                <rect x="35" y="15" width="45" height="55" rx="4" stroke="#a855f7" stroke-width="6" opacity="0.5" stroke-linejoin="round"/>
                <line x1="32" y1="45" x2="53" y2="45" stroke="url(#gradPlataforma)" stroke-width="4" stroke-linecap="round"/>
                <line x1="32" y1="58" x2="45" y2="58" stroke="url(#gradPlataforma)" stroke-width="4" stroke-linecap="round"/>
            </svg>
            <span class="text-xl font-semibold tracking-wide">Plataforma de Processos</span>
        </div>
        <div class="space-x-4">
            @if (Route::has("login"))
                @auth
                    <a href="{{ url("/dashboard") }}" class="text-gray-300 hover:text-white transition">Dashboard</a>
                @else
                    <a href="{{ route("login") }}" class="text-gray-300 hover:text-white transition">Login</a>
                    @if (Route::has("register"))
                        <a href="{{ route("register") }}" class="bg-gradient-to-r from-purple-600 to-pink-600 px-5 py-2 rounded-lg font-medium hover:opacity-90 transition shadow-lg shadow-purple-500/30">Registrar</a>
                    @endif
                @endauth
            @endif
        </div>
    </nav>
    <main class="container mx-auto px-6 pt-20 pb-16 text-center flex flex-col items-center">
        <h1 class="text-5xl md:text-6xl font-extrabold mb-6 tracking-tight">
            Gestão <span class="text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-pink-500">Inteligente</span>
        </h1>
        <p class="text-gray-400 text-lg md:text-xl max-w-2xl mb-10">
            Centralize, automatize e resolva. Uma plataforma desenhada para otimizar o fluxo do seu Service Desk, garantindo eficiência e transparência do início ao fim do processo.
        </p>
        <a href="{{ route("login") }}" class="bg-gradient-to-r from-purple-600 to-pink-600 px-8 py-4 rounded-xl text-lg font-bold hover:scale-105 transition-transform duration-300 shadow-xl shadow-purple-500/20">
            Acessar Plataforma
        </a>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mt-24 w-full max-w-6xl text-left">
            <div class="bg-[#161b22] p-6 rounded-2xl border border-gray-800 hover:border-purple-500/50 transition duration-300">
                <div class="bg-purple-500/10 w-12 h-12 rounded-lg flex items-center justify-center mb-4"><span class="text-2xl">⚡</span></div>
                <h3 class="text-xl font-bold mb-2">Automação de Fluxos</h3>
                <p class="text-gray-400 text-sm">Crie regras automáticas para triagem e roteamento de tickets e chamados internos.</p>
            </div>
            <div class="bg-[#161b22] p-6 rounded-2xl border border-gray-800 hover:border-pink-500/50 transition duration-300">
                <div class="bg-pink-500/10 w-12 h-12 rounded-lg flex items-center justify-center mb-4"><span class="text-2xl">📊</span></div>
                <h3 class="text-xl font-bold mb-2">Service Desk Ágil</h3>
                <p class="text-gray-400 text-sm">Painéis intuitivos para acompanhamento de SLAs e resolução de demandas em tempo real.</p>
            </div>
            <div class="bg-[#161b22] p-6 rounded-2xl border border-gray-800 hover:border-purple-500/50 transition duration-300">
                <div class="bg-purple-500/10 w-12 h-12 rounded-lg flex items-center justify-center mb-4"><span class="text-2xl">🔒</span></div>
                <h3 class="text-xl font-bold mb-2">Transparência</h3>
                <p class="text-gray-400 text-sm">Histórico completo e auditável de todas as interações e mudanças de status nos processos.</p>
            </div>
            <div class="bg-[#161b22] p-6 rounded-2xl border border-gray-800 hover:border-pink-500/50 transition duration-300">
                <div class="bg-pink-500/10 w-12 h-12 rounded-lg flex items-center justify-center mb-4"><span class="text-2xl">📂</span></div>
                <h3 class="text-xl font-bold mb-2">Gestão Documental</h3>
                <p class="text-gray-400 text-sm">Anexe e organize arquivos essenciais diretamente na interface de cada solicitação.</p>
            </div>
        </div>
    </main>
</body>
</html>
