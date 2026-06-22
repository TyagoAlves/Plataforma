# Plataforma de Gestão de Processos

Sistema web para gerenciamento de processos documentais com sugestões de resposta por IA, desenvolvido com Laravel.

## Funcionalidades

- **Landing Page** — Apresentação institucional do sistema
- **Dashboard** — Visão geral com categorias e contagem de processos
- **Gestão de Processos** — Listagem, filtros (data, tipo, palavra-chave), detalhes e submissão de respostas
- **Sugestão por IA** — Geração automática de sugestão de resposta com base no conteúdo do processo
- **API REST** — Endpoints para recebimento e consulta de processos via sistemas externos
- **Autenticação** — Login, registro e gerenciamento de perfil
- **Tema Escuro** — Interface com glassmorphism e tema escuro nativo

## Requisitos

- PHP 8.2+
- Composer
- Node.js 22+
- SQLite (desenvolvimento) / MySQL (produção)

## Instalação

```bash
git clone https://github.com/TyagoAlves/Plataforma.git
cd Plataforma
cp .env.example .env
php artisan key:generate
composer install
npm install
npm run build
php artisan migrate --seed
php artisan serve
```

## Deploy

O sistema está disponível em: [http://18.188.189.197](http://18.188.189.197)

### Credenciais de Teste

- **E-mail:** admin@exemple.com
- **Senha:** admin123

## Estrutura do Projeto

```
Plataforma/
├── app/
│   ├── Http/Controllers/
│   │   ├── ProcessController.php    # CRUD e resposta de processos
│   │   ├── ApiController.php        # API REST
│   │   └── DashboardController.php  # Dashboard
│   └── Models/
│       ├── Process.php
│       ├── ProcessCategory.php
│       └── ProcessResponse.php
├── database/
│   └── migrations/                  # Schema do banco
├── resources/
│   └── views/
│       ├── processes/               # Views de listagem e detalhe
│       ├── layouts/                 # App, guest, navigation
│       ├── auth/                    # Login, registro, senha
│       └── profile/                 # Perfil do usuário
└── routes/
    ├── web.php                      # Rotas web
    └── api.php                      # Rotas da API
```

## API

| Método | Rota | Descrição | Autenticação |
|--------|------|-----------|-------------|
| POST | `/api/processes/receive` | Receber novo processo | Pública |
| GET | `/api/processes/{id}` | Consultar processo | Sanctum |
| POST | `/api/processes/{id}/response` | Enviar resposta | Sanctum |

## Documentação

- [Modelagem do Sistema](/storage/modelagem_sistema.pdf) — Diagramas, arquitetura e especificação técnica do sistema.

## Licença

Este projeto é open-source sob a licença MIT.
