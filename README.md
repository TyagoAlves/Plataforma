# MindContainer - Plataforma Interativa de Estudos com IA

Plataforma de estudos baseada em IA que transforma materiais acadêmicos em simulados interativos, slides explicativos e podcasts didáticos.

## Funcionalidades

- **Autenticação Avançada** — Login com tema escuro + glassmorphism, áreas isoladas por usuário
- **Aprendizado Sob Demanda** — Upload de PDFs/TXT ou digitação de conteúdo para processamento por IA
- **Banco de Questões** — Simulados interativos com feedback em tempo real
- **Slides Explicativos** — Resumos visuais renderizados na tela com navegação interativa
- **Podcasts Didáticos** — Scripts de debate simulando duas pessoas discutindo a matéria
- **OpenCode Integrado** — Editor de código diretamente no navegador para modificar o ambiente de aprendizado

## Arquitetura AWS Free Tier

| Componente | Tecnologia | Custo |
|---|---|---|
| **Instância** | EC2 t2.micro / t3.micro (1 única) | Free Tier (750h/mês) |
| **Isolamento** | Docker (containers por usuário) | Zero custo adicional |
| **Banco** | SQLite (containerizado) | Sem RDS, sem custo |
| **Cache** | Database cache via SQLite | Sem Redis/Memcached |
| **Storage** | Local (disco da instância) | Sem S3 |

### Abordagem de Isolamento (Abordagem A - Recomendada)

Uma única instância EC2 Free Tier rodando Docker, onde cada novo usuário ganha um container isolado de forma leve, sem custos extras.

## Requisitos

- PHP 8.3+
- Composer
- Node.js 22+
- Docker (produção) ou SQLite (desenvolvimento)

## Instalação (Desenvolvimento)

```bash
git clone https://github.com/TyagoAlves/MindContainer.git
cd MindContainer
cp .env.example .env
php artisan key:generate
composer install
npm install
npm run build
php artisan migrate --seed
php artisan serve
```

## Deploy (Produção - Docker)

```bash
chmod +x deploy.sh
./deploy.sh docker
```

Ou manualmente:

```bash
sudo docker compose up -d --build
sudo docker compose exec app php artisan migrate --force
```

## Estrutura do Projeto

```
MindContainer/
├── app/
│   ├── Http/Controllers/
│   │   ├── StudyController.php         # Dashboard de estudos
│   │   ├── SubjectController.php       # CRUD de matérias
│   │   ├── StudyMaterialController.php # Upload e processamento
│   │   ├── QuizController.php          # Simulados interativos
│   │   ├── SlideController.php         # Slides explicativos
│   │   ├── PodcastController.php       # Podcasts didáticos
│   │   └── OpenCodeController.php      # Editor de código
│   └── Models/
│       ├── Subject.php
│       ├── StudyMaterial.php
│       ├── Quiz.php
│       ├── QuizQuestion.php
│       ├── Slide.php
│       └── Podcast.php
├── database/
│   └── migrations/                     # Schema do banco
├── resources/views/study/              # Views da plataforma
│   ├── subjects/
│   ├── materials/
│   ├── quizzes/
│   ├── slides/
│   ├── podcasts/
│   └── opencode/
└── routes/web.php                      # Rotas da aplicação
```

## Monitoramento de Armazenamento

O agente DevOps monitora `df -h` continuamente. Se o disco chegar a 85%, limpa caches:
```bash
sudo docker system prune -f
sudo docker compose exec app php artisan optimize:clear
sudo docker compose exec app php artisan view:clear
```

## Licença

Este projeto é open-source sob a licença MIT.
