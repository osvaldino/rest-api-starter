# 🚀 Laravel 12 + PHP 8.4 REST API Starter

[![CI](https://github.com/osvaldino/rest-api-starter/workflows/CI/badge.svg)](https://github.com/osvaldino/rest-api-starter/actions)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![PHP 8.4](https://img.shields.io/badge/PHP-8.4-777BB4.svg)](https://www.php.net/)
[![Laravel 12](https://img.shields.io/badge/Laravel-12-FF2D20.svg)](https://laravel.com/)

> **Production-ready API boilerplate** com autenticação Sanctum, responses padronizadas, exception handling global,
> filtros/paginação e CRUD completo de exemplo.

---

## ✨ Features

✅ **Laravel 12** + **PHP 8.4** com `strict_types` em todos os arquivos <br>
✅ **Autenticação Sanctum** (Bearer tokens) <br>
✅ **Responses padronizadas** (contrato consistente JSON) <br>
✅ **Exception handling global** em `bootstrap/app.php` <br>
✅ **Middlewares customizados** (Request ID, JSON enforcer) <br>
✅ **Filtros e paginação** com limites e ordenação <br>
✅ **CRUD completo**: Projects, Tasks, Categories <br>
✅ **Arquitetura limpa**: Actions, Services, Resources, Requests <br>
✅ **Docker Compose** para ambiente local (PHP 8.4 + PostgreSQL 16) <br>
✅ **GitHub Actions CI** (testes, Pint, PHPStan nível 5) <br>
✅ **Testes com Pest** (80%+ coverage obrigatório) <br>
✅ **Soft deletes** em todos os models <br>
✅ **PostgreSQL** como banco de dados <br>
✅ **Code quality**: PSR-12, Larastan, Laravel Pint <br>

---

## 🚀 Quick Start

### 1️⃣ Clone o repositório

```bash
git clone https://github.com/osvaldino/rest-api-starter.git
cd rest-api-starter
```

### 2️⃣ Configure o ambiente

```bash
cp .env.example .env
# Ajuste as variáveis se necessário (portas, credenciais)
```

### 3️⃣ Suba o ambiente com Docker

```bash
docker compose up -d
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
```

**Pronto!** A API estará rodando em `http://localhost`.

**Demo user:** `demo@example.com` / `password`

---

## 📂 Estrutura do Projeto

```
app/
├── Actions/         # Casos de uso (Create, Update, Delete)
├── Services/        # Regras de domínio e orquestração
├── Http/
│   ├── Controllers/Api/V1/  # Controllers REST versionados
│   ├── Requests/            # Validação e normalização
│   ├── Resources/           # Transformação de output
│   └── Middleware/          # Middlewares customizados
├── Models/          # Eloquent models
├── Enums/           # Enums (status, códigos)
└── Support/         # Helpers (ApiResponse, Filtros)
```

**Por quê essa estrutura?**

- **Actions**: encapsulam casos de uso específicos (ex: `CreateProjectAction`)
- **Services**: orquestram lógica de domínio e aplicam filtros/paginação
- **Requests**: validam e normalizam inputs (single responsibility)
- **Resources**: transformam models em JSON consistente
- **Support**: utilitários reutilizáveis (responses, filtros)

---

## 🔐 Autenticação (Sanctum)

### Registrar usuário

```bash
curl -X POST http://localhost/api/v1/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

**Resposta:**

```json
{
    "success": true,
    "message": "User registered successfully",
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "created_at": "2024-01-01T00:00:00+00:00",
            "updated_at": "2024-01-01T00:00:00+00:00"
        },
        "token": "1|abc123..."
    },
    "meta": {
        "request_id": "9c7f2a15-3b4e-4f6d-8e9a-1b2c3d4e5f6g",
        "timestamp": "2024-01-01T00:00:00+00:00"
    },
    "errors": null
}
```

### Login

```bash
curl -X POST http://localhost/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "john@example.com",
    "password": "password123"
  }'
```

### Obter perfil

```bash
curl -X GET http://localhost/api/v1/auth/me \
  -H "Authorization: Bearer 1|abc123..."
```

### Logout

```bash
curl -X POST http://localhost/api/v1/auth/logout \
  -H "Authorization: Bearer 1|abc123..."
```

---

## 📦 CRUD de Exemplo (Projects)

### Listar projetos com filtros e paginação

```bash
curl -X GET "http://localhost/api/v1/projects?search=Laravel&status=in_progress&sort=-created_at&page=1&per_page=15" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

**Parâmetros de query:**

| Parâmetro  | Descrição                                | Exemplo              |
|------------|------------------------------------------|----------------------|
| `search`   | Busca em `name` e `description`          | `search=Laravel`     |
| `status`   | Filtra por status                        | `status=in_progress` |
| `sort`     | Ordena por campo (prefixo `-` para DESC) | `sort=-created_at`   |
| `page`     | Número da página                         | `page=1`             |
| `per_page` | Itens por página (1-100)                 | `per_page=15`        |

**Campos de ordenação permitidos:** `created_at`, `updated_at`, `name`, `status`

**Status disponíveis:** `planning`, `in_progress`, `completed`, `on_hold`, `cancelled`

**Resposta:**

```json
{
    "success": true,
    "message": "Projects retrieved successfully",
    "data": [
        {
            "id": 1,
            "name": "Laravel API",
            "description": "REST API with Laravel 12",
            "status": "in_progress",
            "status_label": "In Progress",
            "created_at": "2024-01-01T00:00:00+00:00",
            "updated_at": "2024-01-01T00:00:00+00:00"
        }
    ],
    "meta": {
        "request_id": "uuid-here",
        "timestamp": "2024-01-01T00:00:00+00:00",
        "pagination": {
            "current_page": 1,
            "per_page": 15,
            "total": 1,
            "last_page": 1,
            "from": 1,
            "to": 1
        }
    },
    "errors": null
}
```

### Criar projeto

```bash
curl -X POST http://localhost/api/v1/projects \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "New Project",
    "description": "Project description",
    "status": "planning"
  }'
```

### Visualizar projeto

```bash
curl -X GET http://localhost/api/v1/projects/1 \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Atualizar projeto

```bash
curl -X PUT http://localhost/api/v1/projects/1 \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Updated Project",
    "description": "Updated description",
    "status": "in_progress"
  }'
```

### Deletar projeto

```bash
curl -X DELETE http://localhost/api/v1/projects/1 \
  -H "Authorization: Bearer YOUR_TOKEN"
```

---

## 📋 Padrão de Resposta

### Sucesso

```json
{
    "success": true,
    "message": "Operation successful",
    "data": {},
    "meta": {
        "request_id": "uuid-here",
        "timestamp": "2024-01-01T00:00:00+00:00",
        "pagination": {
            "current_page": 1,
            "per_page": 15,
            "total": 100,
            "last_page": 7,
            "from": 1,
            "to": 15
        }
    },
    "errors": null
}
```

### Erro

```json
{
    "success": false,
    "message": "Validation failed",
    "data": null,
    "meta": {
        "request_id": "uuid-here",
        "timestamp": "2024-01-01T00:00:00+00:00"
    },
    "errors": [
        {
            "code": "validation_error",
            "field": "email",
            "detail": "The email field is required"
        }
    ]
}
```

**Códigos HTTP:**

- `200` OK
- `201` Created
- `400` Bad Request
- `401` Unauthenticated
- `403` Forbidden
- `404` Not Found
- `405` Method Not Allowed
- `422` Validation Error
- `429` Too Many Requests
- `500` Internal Server Error

**Cabeçalhos personalizados:**

- `X-Request-ID`: UUID único para rastreamento

---

## 🔍 Filtros e Paginação

### Projects

| Parâmetro  | Tipo   | Descrição                            |
|------------|--------|--------------------------------------|
| `search`   | string | Busca em `name` e `description`      |
| `status`   | string | Filtra por status exato              |
| `sort`     | string | Ordena por campo (ex: `-created_at`) |
| `page`     | int    | Página atual                         |
| `per_page` | int    | Itens por página (1-100)             |

### Tasks

| Parâmetro     | Tipo   | Descrição                      |
|---------------|--------|--------------------------------|
| `search`      | string | Busca em `title`               |
| `project_id`  | int    | Filtra por projeto             |
| `category_id` | int    | Filtra por categoria           |
| `done`        | bool   | Filtra por status de conclusão |
| `sort`        | string | Ordena por campo               |
| `page`        | int    | Página atual                   |
| `per_page`    | int    | Itens por página (1-100)       |

### Categories

| Parâmetro  | Tipo   | Descrição                |
|------------|--------|--------------------------|
| `search`   | string | Busca em `name`          |
| `sort`     | string | Ordena por campo         |
| `page`     | int    | Página atual             |
| `per_page` | int    | Itens por página (1-100) |

---

## 🧪 Testes

```bash
# Rodar todos os testes
docker compose exec app php artisan test

# Com coverage (mínimo 80% obrigatório)
docker compose exec app php artisan test --coverage --min=80

# Apenas testes de Feature
docker compose exec app php artisan test --testsuite=Feature

# Rodar Pest diretamente
docker compose exec app ./vendor/bin/pest

# Testes em paralelo
docker compose exec app php artisan test --parallel
```

---

## 🔧 Comandos Úteis

```bash
# Subir ambiente
docker compose up -d

# Parar ambiente
docker compose down

# Logs
docker compose logs -f app

# Rodar migrations
docker compose exec app php artisan migrate

# Rodar seeds
docker compose exec app php artisan db:seed

# Fresh migration + seed
docker compose exec app php artisan migrate:fresh --seed

# Laravel Pint (code formatting)
docker compose exec app ./vendor/bin/pint

# PHPStan (static analysis)
docker compose exec app ./vendor/bin/phpstan analyse

# Limpar caches
docker compose exec app php artisan optimize:clear

# Acessar shell do container
docker compose exec app sh

# Acessar PostgreSQL
docker compose exec pgsql psql -U sail -d laravel_api
```

---

## 🔄 Autenticação JWT (Opcional)

Este starter usa **Sanctum** por padrão, mas você pode alternar para **JWT** seguindo estes passos:

### 1. Instalar tymon/jwt-auth

```bash
docker compose exec app composer require tymon/jwt-auth
docker compose exec app php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"
docker compose exec app php artisan jwt:secret
```

### 2. Configurar guard no `config/auth.php`

```php
'guards' => [
    'api' => [
        'driver' => 'jwt', // era 'sanctum'
        'provider' => 'users',
    ],
],
```

### 3. Implementar `JWTSubject` no Model User

```php
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
```

### 4. Atualizar AuthService

```php
use Tymon\JWTAuth\Facades\JWTAuth;

public function createToken(User $user): string
{
    return JWTAuth::fromUser($user);
}
```

### 5. Atualizar LogoutAction

```php
public function execute(User $user): void
{
    JWTAuth::invalidate(JWTAuth::getToken());
}
```

**Pronto!** Agora a API usa JWT. O contrato de endpoints permanece o mesmo.

---

## 🐳 Docker

O projeto usa Docker Compose com:

- **PHP 8.4 FPM** (Alpine) com Nginx + Supervisord
- **PostgreSQL 16** (Alpine)
- **OPcache** habilitado com JIT
- **Volumes** para código e persistência do banco
- **Healthchecks** configurados

### Customizar serviços

Edite `compose.yaml` para adicionar Redis, Mailhog, etc:

```yaml
redis:
    image: 'redis:alpine'
    ports:
        - '6379:6379'
    networks:
        - app-network
```

---

## 🚀 Deploy

### Build de produção

```bash
# Build Docker image
docker build -t laravel-api:latest -f docker/php/Dockerfile .

# Otimizações Laravel (dentro do container)
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

### Variáveis de ambiente (produção)

```env
APP_ENV=production
APP_DEBUG=false
DB_CONNECTION=pgsql
DB_HOST=seu-host
DB_DATABASE=seu-banco
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
```

### Checklist de deploy

- [ ] `APP_DEBUG=false`
- [ ] `APP_ENV=production`
- [ ] Banco de dados configurado
- [ ] Migrations executadas
- [ ] Caches criados (config, route, view)
- [ ] HTTPS habilitado
- [ ] CORS configurado
- [ ] Rate limiting configurado
- [ ] Logs configurados
- [ ] Backups automatizados

---

## 🤝 Contribuindo

1. Fork o projeto
2. Crie uma branch (`git checkout -b feature/amazing-feature`)
3. Commit suas mudanças (`git commit -m 'Add amazing feature'`)
4. Push para a branch (`git push origin feature/amazing-feature`)
5. Abra um Pull Request

### Padrões de código

- **PSR-12** para estilo de código
- **Strict types** (`declare(strict_types=1)`) em todos os arquivos PHP
- **Final classes** quando apropriado
- **Constructor property promotion** para DI
- **Pest** para testes (não PHPUnit style)
- **Coverage mínimo** de 80%
- **PHPStan nível 5** sem erros

### Rodar CI localmente

```bash
# Formatting
docker compose exec app ./vendor/bin/pint --test

# Static analysis
docker compose exec app ./vendor/bin/phpstan analyse --memory-limit=512M

# Tests com coverage
docker compose exec app php artisan test --coverage --min=80
```

---

## 📄 Licença

Este projeto está sob a licença MIT. Veja o arquivo [LICENSE](https://opensource.org/licenses/MIT) para mais
detalhes. <br>

---

## 🙏 Créditos

Desenvolvido com ❤️ usando:

- [Laravel 12](https://laravel.com)
- [Laravel Sanctum](https://laravel.com/docs/sanctum)
- [Pest PHP](https://pestphp.com)
- [Larastan](https://github.com/larastan/larastan)
- [Laravel Pint](https://laravel.com/docs/pint)

---

**Happy coding!** 🚀
