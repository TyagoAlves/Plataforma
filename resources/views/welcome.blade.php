<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'MindContainer') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased dark bg-gray-950 text-gray-100">
        <div class="min-h-screen flex flex-col">

            <nav class="backdrop-blur-xl bg-gray-900/80 border-b border-white/10">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex items-center">
                            <x-application-logo class="block h-9 w-auto fill-current text-indigo-400" />
                            <span class="ml-3 text-xl font-semibold text-white">MindContainer</span>
                        </div>
                        <div class="flex items-center space-x-4">
                            @auth
                                <a href="{{ route('dashboard') }}" class="text-sm text-gray-300 hover:text-white transition">Dashboard</a>
                            @else
                                <a href="{{ route('login') }}" class="text-sm text-gray-300 hover:text-white transition">Login</a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="px-4 py-2 bg-indigo-600 rounded-lg text-white text-sm font-medium hover:bg-indigo-500 transition">Cadastre-se</a>
                                @endif
                            @endauth
                        </div>
                    </div>
                </div>
            </nav>

            <main class="flex-1">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
                    <div class="text-center mb-16">
                        <h1 class="text-5xl font-bold bg-gradient-to-r from-indigo-400 via-purple-400 to-pink-400 bg-clip-text text-transparent mb-4">
                            Estude com Inteligência
                        </h1>
                        <p class="text-xl text-gray-400 max-w-3xl mx-auto">
                            Transforme seus materiais de estudo em simulados interativos, slides explicativos e podcasts didáticos com o poder da IA.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-16">
                        <div class="backdrop-blur-xl bg-white/5 border border-white/10 rounded-2xl p-8 text-center hover:bg-white/10 transition">
                            <div class="text-4xl mb-4">📝</div>
                            <h3 class="text-lg font-semibold text-white mb-2">Simulados Inteligentes</h3>
                            <p class="text-gray-400 text-sm">Gere bancos de questões personalizados com feedback em tempo real para cada matéria.</p>
                        </div>
                        <div class="backdrop-blur-xl bg-white/5 border border-white/10 rounded-2xl p-8 text-center hover:bg-white/10 transition">
                            <div class="text-4xl mb-4">📊</div>
                            <h3 class="text-lg font-semibold text-white mb-2">Slides Explicativos</h3>
                            <p class="text-gray-400 text-sm">Resumos visuais dinâmicos gerados automaticamente a partir do seu conteúdo.</p>
                        </div>
                        <div class="backdrop-blur-xl bg-white/5 border border-white/10 rounded-2xl p-8 text-center hover:bg-white/10 transition">
                            <div class="text-4xl mb-4">🎧</div>
                            <h3 class="text-lg font-semibold text-white mb-2">Podcasts Didáticos</h3>
                            <p class="text-gray-400 text-sm">Áudios com debates simulados para aprender em qualquer lugar, a qualquer hora.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-16">
                        <div class="backdrop-blur-xl bg-white/5 border border-white/10 rounded-2xl p-8 hover:bg-white/10 transition">
                            <div class="flex items-center mb-4">
                                <span class="text-2xl mr-3">📤</span>
                                <h3 class="text-lg font-semibold text-white">Upload de Materiais</h3>
                            </div>
                            <p class="text-gray-400 text-sm">Faça upload de PDFs, textos ou digite diretamente o conteúdo da sua matéria. A IA processa e gera automaticamente os recursos de estudo.</p>
                        </div>
                        <div class="backdrop-blur-xl bg-white/5 border border-white/10 rounded-2xl p-8 hover:bg-white/10 transition">
                            <div class="flex items-center mb-4">
                                <span class="text-2xl mr-3">💻</span>
                                <h3 class="text-lg font-semibold text-white">OpenCode Integrado</h3>
                            </div>
                            <p class="text-gray-400 text-sm">Ambiente de desenvolvimento integrado diretamente no navegador. Visualize e modifique o código fonte do seu ambiente de aprendizado.</p>
                        </div>
                    </div>

                    <div class="text-center">
                        @auth
                            <a href="{{ route('study.dashboard') }}" class="inline-flex items-center px-8 py-4 bg-indigo-600 rounded-xl text-white font-semibold text-lg hover:bg-indigo-500 transition shadow-lg shadow-indigo-500/25">
                                Acessar Plataforma
                            </a>
                        @else
                            <a href="{{ route('register') }}" class="inline-flex items-center px-8 py-4 bg-indigo-600 rounded-xl text-white font-semibold text-lg hover:bg-indigo-500 transition shadow-lg shadow-indigo-500/25">
                                Começar Agora
                            </a>
                        @endauth
                    </div>
                </div>
            </main>

            <footer class="border-t border-white/10 py-8">
                <div class="max-w-7xl mx-auto px-4 text-center text-gray-500 text-sm">
                    {{ config('app.name', 'MindContainer') }} — Plataforma de Estudos com IA &copy; {{ date('Y') }}
                </div>
            </footer>

        </div>
    </body>
</html>
