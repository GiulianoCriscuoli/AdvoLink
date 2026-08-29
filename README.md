<p align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="320" alt="Laravel Logo">
</p>

<h1 align="center">AdvoLink</h1>

<p align="center">
  API multi-tenant construída em Laravel, com autenticação via Sanctum (cookie httpOnly) e login social com Google.
</p>

<p align="center">
  <img alt="PHP" src="https://img.shields.io/badge/PHP-8.3%2B-777bb4?logo=php&logoColor=white">
  <img alt="Laravel" src="https://img.shields.io/badge/Laravel-13-ff2d20?logo=laravel&logoColor=white">
  <img alt="License" src="https://img.shields.io/badge/license-MIT-blue">
</p>

---

## Sobre o projeto

O AdvoLink é uma API multi-tenant: cada organização (**Tenant**) pode ter vários usuários vinculados através de uma tabela pivô (`tenant_user`), cada um com um papel (`owner`, `admin`, `member`) e associado a um plano (`Plan`).

A autenticação é feita via [Laravel Sanctum](https://laravel.com/docs/sanctum), com o token entregue em um cookie `httpOnly`, e também é possível autenticar via **Google OAuth** (Laravel Socialite).

## Stack

| Camada       | Tecnologia                          |
|--------------|--------------------------------------|
| Backend      | PHP 8.3+, Laravel 13                 |
| Auth         | Laravel Sanctum, Laravel Socialite   |
| Banco        | MySQL 8.0                            |
| Frontend/build | Vite, Tailwind CSS                 |
| Infra local  | Docker + Docker Compose (Apache)     |
| Testes       | PHPUnit                              |

## Estrutura principal

```
app/
├── Enums/Role.php                 # owner | admin | member
├── Http/
│   ├── Controllers/Api/           # Controllers da API
│   ├── Middleware/                # Auth via cookie + Sanctum
│   └── Requests/Auth/             # Validação de login/registro
├── Models/                        # User, Tenant, Plan
├── Repositories/                  # Camada de acesso a dados
└── Services/                      # Regras de negócio (ex.: AuthService)
```

## Como rodar localmente

### Opção 1 — Docker (recomendado)

```bash
cp .env.example .env
docker compose up -d --build
docker compose exec apache composer install
docker compose exec apache php artisan key:generate
docker compose exec apache php artisan migrate
```

A aplicação sobe em `http://localhost:8092` e o MySQL fica exposto em `localhost:3310`.

### Opção 2 — Ambiente local (PHP + Composer + Node)

```bash
composer setup   # instala deps, cria .env, gera key, roda migrations, builda assets
composer dev     # sobe servidor, queue, logs (pail) e vite em paralelo
```

## Variáveis de ambiente relevantes

Além das padrões do Laravel, configure:

```env
DB_DATABASE=advolink
DB_USERNAME=advolink
DB_PASSWORD=secret
DB_ROOT_PASSWORD=secret

FRONTEND_URL=http://localhost:5173

GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_REDIRECT_URI=http://localhost:8092/auth/google/callback
```

## Rotas da API

| Método | Rota                     | Descrição                          | Auth |
|--------|--------------------------|-------------------------------------|------|
| POST   | `/cadastrar`             | Registro de novo usuário            | —    |
| POST   | `/login`                 | Login (email + senha)               | —    |
| GET    | `/auth/google`           | Redireciona para o OAuth do Google  | —    |
| GET    | `/auth/google/callback`  | Callback do OAuth                   | —    |
| GET    | `/me`                    | Dados do usuário autenticado        | ✅   |
| POST   | `/logout`                | Encerra a sessão (revoga o token)   | ✅   |

A autenticação das rotas protegidas usa o middleware `cookie.auth`, que lê o token do cookie `auth_token` e resolve o usuário via Sanctum.

## Testes

```bash
composer test
# ou
php artisan test
```

## Contribuindo

1. Crie uma branch a partir de `main`
2. Rode `php artisan pint` antes de commitar (padrão de código do projeto)
3. Garanta que `php artisan test` passe
4. Abra um Pull Request descrevendo a mudança

## Licença

Este projeto é open-source, licenciado sob a [licença MIT](https://opensource.org/licenses/MIT).
