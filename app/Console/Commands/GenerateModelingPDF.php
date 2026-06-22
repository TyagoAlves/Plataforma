<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Barryvdh\DomPDF\Facade\Pdf;

class GenerateModelingPDF extends Command
{
    protected $signature = 'generate:pdf';
    protected $description = 'Generate the conceptual modeling PDF document';

    public function handle()
    {
        $html = $this->getHtmlContent();
        $pdf = Pdf::loadHTML($html);
        $path = storage_path('app/public/modelagem_sistema.pdf');
        $pdf->save($path);
        $this->info("PDF generated successfully at: {$path}");
    }

    private function getHtmlContent(): string
    {
        return '
        <html>
        <head>
            <style>
                body { font-family: sans-serif; padding: 40px; color: #1a1a2e; }
                h1 { color: #4f46e5; border-bottom: 2px solid #4f46e5; padding-bottom: 10px; }
                h2 { color: #3730a3; margin-top: 30px; }
                h3 { color: #4338ca; }
                pre { background: #f1f5f9; padding: 15px; border-radius: 8px; overflow-x: auto; }
                code { background: #f1f5f9; padding: 2px 6px; border-radius: 4px; }
                ul { margin: 10px 0; padding-left: 20px; }
                li { margin: 5px 0; }
                .section { margin: 30px 0; padding: 20px; border: 1px solid #e2e8f0; border-radius: 8px; }
                table { width: 100%; border-collapse: collapse; margin: 15px 0; }
                th, td { border: 1px solid #e2e8f0; padding: 10px; text-align: left; }
                th { background: #4f46e5; color: white; }
            </style>
        </head>
        <body>
            <h1>Documento de Modelagem Conceitual do Sistema</h1>
            <p><strong>Projeto:</strong> Plataforma de Gestão e Análise de Processos</p>
            <p><strong>Versão:</strong> 1.0</p>
            <p><strong>Data:</strong> ' . now()->format('d/m/Y') . '</p>

            <div class="section">
                <h2>1. Arquitetura de Pastas</h2>
                <pre>
Plataforma/
├── app/
│   ├── Console/
│   │   └── Commands/
│   │       └── GenerateModelingPDF.php
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/           # Controllers do Breeze
│   │   │   ├── ApiController.php
│   │   │   ├── DashboardController.php
│   │   │   ├── ProcessController.php
│   │   │   └── ProfileController.php
│   │   └── Requests/
│   ├── Models/
│   │   ├── Process.php
│   │   ├── ProcessCategory.php
│   │   └── ProcessResponse.php
│   └── Providers/
├── bootstrap/
├── config/
├── database/
│   ├── migrations/            # Migrations das tabelas
│   └── seeders/
│       ├── CategorySeeder.php
│       ├── ProcessSeeder.php
│       └── DatabaseSeeder.php
├── public/
│   ├── build/                 # Assets compilados (Vite)
│   └── index.php
├── resources/
│   └── views/
│       ├── layouts/           # Layouts (app, guest, navigation)
│       ├── auth/              # Views de autenticação
│       ├── processes/
│       │   ├── index.blade.php
│       │   └── show.blade.php
│       ├── profile/
│       ├── components/
│       ├── dashboard.blade.php
│       └── welcome.blade.php
├── routes/
│   ├── web.php               # Rotas web
│   ├── api.php               # Rotas da API
│   └── auth.php              # Rotas de autenticação
├── tailwind.config.js
├── vite.config.js
└── composer.json
                </pre>
            </div>

            <div class="section">
                <h2>2. Modelagem Conceitual de Dados</h2>

                <h3>Entidades e Relacionamentos</h3>
                <table>
                    <tr><th>Entidade</th><th>Atributos</th></tr>
                    <tr><td>User</td><td>id, name, email, password, timestamps</td></tr>
                    <tr><td>ProcessCategory</td><td>id, name, slug, description, icon, timestamps</td></tr>
                    <tr><td>Process</td><td>id, process_category_id (FK), number, title, content, status, date, type, timestamps</td></tr>
                    <tr><td>ProcessResponse</td><td>id, process_id (FK), ai_suggestion, final_response, status, timestamps</td></tr>
                </table>

                <h3>Relacionamentos</h3>
                <ul>
                    <li><strong>Categoria 1:N Processo</strong> — Uma categoria pode conter vários processos</li>
                    <li><strong>Processo 1:1 Resposta</strong> — Cada processo possui uma resposta gerada por IA</li>
                </ul>
            </div>

            <div class="section">
                <h2>3. Fluxo de Dados das APIs</h2>

                <h3>API de Recepção de Processos (POST /api/processes/receive)</h3>
                <pre>
Requisição:
{
    "category_slug": "administrativo",
    "number": "ADM-0001/2026",
    "title": "Título do Processo",
    "content": "Conteúdo detalhado...",
    "type": "requerimento",
    "date": "2026-06-22"
}

Resposta (201):
{
    "message": "Processo recebido com sucesso",
    "process": { ... }
}
                </pre>

                <h3>API de Detalhe do Processo (GET /api/processes/{id})</h3>
                <pre>
Resposta (200):
{
    "id": 1,
    "category": { ... },
    "response": {
        "ai_suggestion": "Sugestão gerada pela IA...",
        "status": "suggested"
    },
    ...
}
                </pre>

                <h3>API de Submissão de Resposta (POST /api/processes/{id}/response)</h3>
                <pre>
Requisição:
{
    "final_response": "Resposta editada pelo usuário..."
}

Resposta (200):
{
    "message": "Resposta enviada com sucesso",
    "response": { ... }
}
                </pre>

                <h3>Fluxo de Integração com IA</h3>
                <ol>
                    <li>Sistema recebe processo via API de recepção</li>
                    <li>Usuário abre o processo no dashboard</li>
                    <li>Sistema aciona mock de IA que analisa o conteúdo</li>
                    <li>IA retorna "Sugestão de Resposta" e preenche o textarea</li>
                    <li>Usuário edita a resposta se necessário</li>
                    <li>Usuário clica em "Enviar Resposta"</li>
                    <li>Sistema faz POST para a API de recepção com a resposta final</li>
                </ol>
            </div>

            <div class="section">
                <h2>4. Governança de TI</h2>
                <h3>Boas Práticas Adotadas</h3>
                <ul>
                    <li><strong>Segregação de Funções:</strong> Separação entre autenticação, lógica de negócio e visualização (MVC)</li>
                    <li><strong>Controle de Acesso:</strong> Autenticação obrigatória para rotas protegidas (middleware auth)</li>
                    <li><strong>Auditoria:</strong> Timestamps automáticos (created_at, updated_at) em todas as tabelas</li>
                    <li><strong>Integridade Referencial:</strong> Chaves estrangeiras com cascadeOnDelete</li>
                    <li><strong>Validação de Dados:</strong> Validação de requests com Form Request e validate()</li>
                    <li><strong>Segurança:</strong> CSRF protection, SQL injection prevention (Eloquent ORM), senhas hasheadas (bcrypt)</li>
                    <li><strong>Infraestrutura como Código:</strong> Variáveis de ambiente (.env) para configuração sensível</li>
                    <li><strong>Versionamento:</strong> Commits organizados por features com Conventional Commits</li>
                </ul>
            </div>

            <div class="section">
                <h2>5. Stack Tecnológica</h2>
                <table>
                    <tr><th>Componente</th><th>Tecnologia</th></tr>
                    <tr><td>Framework</td><td>Laravel 13 (PHP 8.5)</td></tr>
                    <tr><td>Frontend</td><td>Tailwind CSS 4 + Blade + Alpine.js</td></tr>
                    <tr><td>Banco de Dados</td><td>SQLite (dev) / MySQL (prod)</td></tr>
                    <tr><td>Autenticação</td><td>Laravel Breeze</td></tr>
                    <tr><td>Build</td><td>Vite</td></tr>
                    <tr><td>Servidor Web</td><td>Nginx</td></tr>
                    <tr><td>Infraestrutura</td><td>AWS EC2 (t2.micro) - Free Tier</td></tr>
                    <tr><td>Versionamento</td><td>Git + GitHub</td></tr>
                </table>
            </div>
        </body>
        </html>';
    }
}
