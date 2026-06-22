<x-guest-layout>
    <div class="min-h-screen bg-gray-900 flex flex-col items-center justify-center p-4 sm:p-8">
        <div class="w-full max-w-4xl">
            <div class="backdrop-blur-xl bg-white/10 border border-white/20 rounded-3xl shadow-2xl p-8 sm:p-12 text-center glass-panel">
                <h1 class="text-4xl sm:text-6xl font-bold text-white mb-6 tracking-tight">
                    Plataforma de Gestão de Processos
                </h1>
                <p class="text-lg sm:text-xl text-gray-300 mb-8 max-w-2xl mx-auto leading-relaxed">
                    Sistema inteligente para gestão, análise e tramitação de processos administrativos
                    com integração de inteligência artificial para sugestão de respostas.
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-10 text-left">
                    <div class="backdrop-blur-lg bg-white/5 border border-white/10 rounded-xl p-6 glass-card">
                        <div class="text-indigo-400 text-3xl mb-3">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <h3 class="text-white font-semibold mb-2">Gestão de Processos</h3>
                        <p class="text-gray-400 text-sm">Organize e acompanhe todos os processos em um único lugar com categorização inteligente.</p>
                    </div>
                    <div class="backdrop-blur-lg bg-white/5 border border-white/10 rounded-xl p-6 glass-card">
                        <div class="text-emerald-400 text-3xl mb-3">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                        <h3 class="text-white font-semibold mb-2">IA Integrada</h3>
                        <p class="text-gray-400 text-sm">Sugestões inteligentes de respostas geradas por inteligência artificial para agilizar o trabalho.</p>
                    </div>
                    <div class="backdrop-blur-lg bg-white/5 border border-white/10 rounded-xl p-6 glass-card">
                        <div class="text-amber-400 text-3xl mb-3">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        </div>
                        <h3 class="text-white font-semibold mb-2">Segurança e Conformidade</h3>
                        <p class="text-gray-400 text-sm">Acesso seguro com autenticação e conformidade com normas de governança de TI.</p>
                    </div>
                </div>
                @if (Route::has('login'))
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="inline-flex items-center justify-center px-8 py-4 backdrop-blur-lg bg-indigo-600/80 hover:bg-indigo-600 text-white font-semibold rounded-xl transition-all duration-200 border border-indigo-400/30 shadow-lg hover:shadow-indigo-500/25">
                                Acessar Dashboard
                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-8 py-4 backdrop-blur-lg bg-indigo-600/80 hover:bg-indigo-600 text-white font-semibold rounded-xl transition-all duration-200 border border-indigo-400/30 shadow-lg hover:shadow-indigo-500/25">
                                Entrar no Sistema
                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-8 py-4 backdrop-blur-lg bg-white/10 hover:bg-white/20 text-white font-semibold rounded-xl transition-all duration-200 border border-white/20 shadow-lg">
                                    Criar Conta
                                </a>
                            @endif
                        @endauth
                    </div>
                @endif
            </div>
            <div class="text-center mt-8 text-gray-500 text-sm">
                &copy; {{ date('Y') }} Plataforma de Gestão de Processos. Todos os direitos reservados.
            </div>
        </div>
    </div>

    <style>
        .glass-panel {
            background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.05) 100%);
        }
        .glass-card {
            transition: all 0.3s ease;
        }
        .glass-card:hover {
            transform: translateY(-4px);
            background: rgba(255,255,255,0.1);
            border-color: rgba(255,255,255,0.3);
        }
    </style>
</x-guest-layout>
