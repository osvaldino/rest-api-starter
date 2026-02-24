# 📊 FASE 1 — DESCOBERTA DO PROJETO

Análise completa do código-fonte realizada em 2026-02-23.

## 🎯 Tecnologias Core

- **Framework**: Laravel 12.0 (mais recente)
- **PHP**: 8.2+ (requisito mínimo)
- **Banco de Dados**: PostgreSQL 18 (via Docker/Sail)
- **Suporte**: SQLite, MySQL, MariaDB também configurados

## 🧪 Stack de Testes

- **Framework de Testes**: Pest 4.4 (não PHPUnit tradicional)
- **Plugin Laravel**: pestphp/pest-plugin-laravel 4.1
- **Mocks**: Mockery 1.6
- **Dados Falsos**: FakerPHP 1.23
- **Collision**: 8.6 (relatórios de erro elegantes)

## 🎨 Frontend & Build

- **Bundler**: Vite 7.0.7
- **CSS Framework**: Tailwind CSS 4.0 (versão mais recente)
- **Plugin**: @tailwindcss/vite 4.0
- **HTTP Client**: Axios 1.11.0
- **Template Engine**: Blade (Laravel nativo)
- **Concurrency Tool**: concurrently 9.0.1 (para dev multi-processo)

## 🗄️ Estrutura de Banco de Dados

### Tabelas Existentes

1. **users** - autenticação, perfil
   - id, name, email, email_verified_at, password, remember_token, timestamps
2. **password_reset_tokens** - recuperação de senha
   - email (PK), token, created_at
3. **sessions** - sessões de usuário (DB-based)
   - id (PK), user_id, ip_address, user_agent, payload, last_activity
4. **cache + cache_locks** - cache persistente
5. **jobs + job_batches + failed_jobs** - filas de trabalho

### Recursos Disponíveis

- Migrations (versionamento de schema)
- Seeders (população de dados)
- Factories (geração de dados de teste)

## 🔐 Autenticação & Segurança

- **Guard Padrão**: Session-based (web)
- **Provider**: Eloquent (model User)
- **Password Hashing**: Bcrypt (12 rounds configuráveis)
- **Proteção**: CSRF, Password Reset Tokens
- **Model User**: Notifiable, HasFactory, possui email verification (opcional)

## 📦 Infraestrutura & Ferramentas

- **Docker**: Laravel Sail 1.41 (ambiente completo)
- **Code Formatting**: Laravel Pint 1.24
- **Log Viewer**: Laravel Pail 1.2.2
- **REPL**: Laravel Tinker 2.10.1
- **Queue System**: Database driver (configurável para Redis/SQS)
- **Cache Driver**: Database (configurável para Redis/Memcached)
- **Mail System**: Log driver (configurável para SMTP/Mailgun/etc)
- **Storage**: Local filesystem (configurável para S3)

## 🏗️ Arquitetura Atual

### Padrões em Uso

- MVC (Model-View-Controller)
- Repository Pattern (via Eloquent ORM)
- Service Container (DI nativo Laravel)
- Facades Pattern
- Observer Pattern (Events/Listeners - disponível)
- Factory Pattern (para testes e criação de objetos)

### Estrutura de Diretórios

```
app/
├── Http/
│   └── Controllers/     # Controllers HTTP
├── Models/              # Eloquent Models
└── Providers/           # Service Providers

database/
├── factories/           # Model Factories
├── migrations/          # Schema migrations
└── seeders/            # Data seeders

tests/
├── Feature/            # Testes de integração/feature
└── Unit/              # Testes unitários

routes/
├── web.php            # Rotas web
└── console.php        # Comandos Artisan

resources/
├── views/             # Blade templates
├── js/                # JavaScript assets
└── css/               # CSS/Tailwind
```

### Estruturas NÃO Presentes

(mas suportadas pelo framework)

- API Routes (sem routes/api.php)
- Middleware customizado
- Form Requests (validação)
- Resources/Transformers (API)
- Jobs/Listeners/Events customizados
- Policies/Gates (autorização)
- Broadcasting (WebSockets)
- Notifications customizadas

## 🛠️ Scripts de Desenvolvimento

### Composer Scripts

- `composer setup` - setup completo do projeto
- `composer dev` - ambiente dev completo (server + queue + logs + vite)
- `composer test` - executa suite de testes

### NPM Scripts

- `npm run dev` - Vite dev server
- `npm run build` - Build de produção

## 🔄 Sistema de Filas

- Driver: Database (configurável)
- Suporte a job batches
- Rastreamento de falhas
- Retry logic configurável

## 📝 Logging & Monitoramento

- **Driver**: Stack (múltiplos canais)
- **Canal Padrão**: Single (arquivo único)
- **Deprecations**: Separados
- **Tool**: Laravel Pail (tail em tempo real)

## 🌐 Configurações de Ambiente

### Variáveis Críticas

- Locale: en (inglês)
- Timezone: UTC (inferido, não explícito)
- Session: Database-backed, lifetime 120min
- Queue: Database-backed
- Cache: Database-backed
- Broadcast: Log (desenvolvimento)
- Mail: Log (desenvolvimento)

## 🎯 Estado do Projeto

- **Tipo**: Starter Kit / Skeleton API
- **Maturidade**: Projeto base pronto para expansão
- **Features Implementadas**: Autenticação básica, estrutura MVC
- **Features Pendentes**: Toda a lógica de negócio específica

## 🔍 Integrações Externas

### Disponíveis mas não configuradas

- AWS S3 (filesystem)
- Redis (cache/queue/session)
- Memcached (cache)
- Email services (SMTP, Mailgun, Postmark, etc)

## 📋 Padrões de Código

- **EditorConfig**: 4 espaços, LF, UTF-8, trim whitespace
- **Formatter**: Laravel Pint (PSR-12 + Laravel style)
- **Namespace**: PSR-4 autoloading

---

**Data da Análise**: 2026-02-23
**Analisado por**: Claude Code (Sonnet 4.5)
