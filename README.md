# FinPulse — Financial Tracker

A full-stack personal finance application for tracking income, expenses, budgets, and analytics. The app is branded **FinPulse** in the UI and runs as a **Laravel 11 monolith**: Blade templates serve the frontend, and a JSON REST API (same origin) powers all data operations.

**Repository:** [github.com/pasindi15/Financial-tracker](https://github.com/pasindi15/Financial-tracker)

---

## Table of Contents

- [Tech Stack](#tech-stack)
- [Architecture Overview](#architecture-overview)
- [How Frontend and Backend Connect](#how-frontend-and-backend-connect)
- [Laravel & PHP Implementation](#laravel--php-implementation)
- [Project Structure](#project-structure)
- [Database Schema](#database-schema)
- [API Reference](#api-reference)
- [Authentication](#authentication)
- [Setup with Laravel Herd (Local)](#setup-with-laravel-herd-local)
- [Setup with Docker](#setup-with-docker)
- [Environment Configuration](#environment-configuration)
- [Development Workflow](#development-workflow)
- [Demo Credentials](#demo-credentials)
- [Troubleshooting](#troubleshooting)

---

## Tech Stack

| Layer | Technology | Notes |
|-------|------------|-------|
| **Backend** | PHP 8.1+, Laravel 11 | MVC, Eloquent ORM, REST API |
| **Auth** | Laravel Sanctum | Bearer token API authentication |
| **Database** | MySQL 8.0 | SQLite supported for quick local testing |
| **Frontend** | Blade + Vanilla JavaScript | No React/Vue build step |
| **Styling** | Tailwind CSS (CDN) | Utility-first UI |
| **Charts** | ApexCharts (CDN) | Dashboard & reports visualizations |
| **Tables** | Tabulator (CDN) | Sortable/filterable data grids |
| **Icons** | Lucide (CDN) | Sidebar and UI icons |
| **Exports** | Maatwebsite Excel, DomPDF | `.xlsx` and `.pdf` report downloads |
| **Local dev** | Laravel Herd | PHP, Composer, and optional MySQL on Windows/macOS |
| **Containerized dev** | Docker + Docker Compose | PHP app container + MySQL container |

---

## Architecture Overview

```
┌─────────────────────────────────────────────────────────────────┐
│                         Browser                                  │
│  ┌──────────────────┐    ┌──────────────────────────────────┐   │
│  │  Blade Views     │    │  JavaScript (fetch API)          │   │
│  │  login.blade.php │    │  localStorage: auth_token        │   │
│  │  dashboard, etc. │───▶│  Authorization: Bearer {token}   │   │
│  └────────┬─────────┘    └──────────────┬───────────────────┘   │
└───────────┼─────────────────────────────┼───────────────────────┘
            │ GET /dashboard              │ GET/POST /api/*
            ▼                             ▼
┌─────────────────────────────────────────────────────────────────┐
│                    Laravel 11 (PHP)                              │
│  routes/web.php          routes/api.php                          │
│       │                        │                                 │
│       ▼                        ▼                                 │
│  Return Blade HTML      API Controllers (JSON)                   │
│                              │                                   │
│                              ▼                                   │
│                         Eloquent Models                          │
│                              │                                   │
└──────────────────────────────┼───────────────────────────────────┘
                               ▼
                    ┌─────────────────────┐
                    │   MySQL / SQLite     │
                    │  users, categories,  │
                    │  transactions,       │
                    │  budgets, tokens     │
                    └─────────────────────┘
```

**Request flow:**

1. User visits `/login` → Laravel returns the login Blade page.
2. Login form POSTs to `/api/login` → Laravel validates credentials and returns a Sanctum token.
3. Token is stored in `localStorage` as `auth_token`.
4. User is redirected to `/dashboard` → Laravel returns the dashboard Blade shell.
5. JavaScript on each page calls `/api/*` endpoints with the Bearer token to load and mutate data.

There is **no separate frontend repo** and **no Vite/npm build**. Pages and API share the same Laravel app and origin (`http://localhost:8000`).

---

## How Frontend and Backend Connect

### 1. Web routes (page shells)

Defined in `routes/web.php`. These return HTML only — no business data is embedded server-side for dashboards.

| URL | View file | Purpose |
|-----|-----------|---------|
| `/` | redirect | Redirects to `/dashboard` |
| `/login` | `resources/views/login.blade.php` | Sign-in page |
| `/dashboard` | `resources/views/dashboard.blade.php` | KPI cards & charts |
| `/transactions` | `resources/views/transactions.blade.php` | Transaction CRUD table |
| `/categories` | `resources/views/categories.blade.php` | Category management |
| `/budgets` | `resources/views/budgets.blade.php` | Budget vs actual |
| `/reports` | `resources/views/reports.blade.php` | Analytics, pivot, exports |

All authenticated pages extend `resources/views/layouts/app.blade.php`, which provides the sidebar, shared styles, auth guard, and `apiHeaders` helper.

### 2. API routes (JSON data)

Defined in `routes/api.php`. All protected routes require `Authorization: Bearer {token}`.

The frontend calls these from inline `<script>` blocks using the browser `fetch()` API. Example from the shared layout:

```javascript
const token = localStorage.getItem('auth_token');
const apiHeaders = {
    'Authorization': 'Bearer ' + token,
    'Content-Type': 'application/json',
    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
};
fetch('/api/dashboard/summary', { headers: apiHeaders })
```

### 3. Page → API mapping

| Frontend page | API endpoints used |
|---------------|-------------------|
| `login.blade.php` | `POST /api/login` |
| `layouts/app.blade.php` | `GET /api/dashboard/summary` (sidebar stats) |
| `dashboard.blade.php` | `GET /api/dashboard/summary`, `GET /api/transactions` |
| `transactions.blade.php` | `GET/POST/DELETE /api/transactions`, `GET /api/categories` |
| `categories.blade.php` | `GET/POST/DELETE /api/categories` |
| `budgets.blade.php` | `GET /api/reports/budget-vs-actual` |
| `reports.blade.php` | `GET /api/reports/pivot`, `monthly-trend`, `budget-vs-actual`, `dashboard/summary`; export via `/api/reports/export-excel` and `/api/reports/export-pdf` |

### 4. Auth guard on the client

- `layouts/app.blade.php` redirects to `/login` if `localStorage.auth_token` is missing.
- `login.blade.php` redirects to `/dashboard` if a token already exists.
- Logout clears `localStorage` and sends the user to `/login`.

### 5. File downloads (special case)

Excel/PDF exports use browser navigation (not `fetch`) because they trigger file downloads:

```javascript
window.location.href = '/api/reports/export-excel?token=' + token;
```

The `AuthenticateQueryToken` middleware (`app/Http/Middleware/AuthenticateQueryToken.php`) copies the `?token=` query param into the `Authorization` header so Sanctum can authenticate the request.

---

## Laravel & PHP Implementation

### Application bootstrap

| File | Role |
|------|------|
| `public/index.php` | Web entry point — loads Composer autoloader and bootstraps Laravel |
| `bootstrap/app.php` | Registers web/API routes, middleware aliases, and Sanctum stateful API |
| `artisan` | CLI entry point for migrations, seeders, and `php artisan serve` |
| `server.php` | Legacy built-in server router (used by some hosting setups) |
| `composer.json` | PHP dependencies and PSR-4 autoloading (`App\` → `app/`) |

### Routing configuration

`bootstrap/app.php` wires everything together:

```php
->withRouting(
    web: __DIR__.'/../routes/web.php',
    api: __DIR__.'/../routes/api.php',
    commands: __DIR__.'/../routes/console.php',
    health: '/up',
)
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'auth.query' => \App\Http\Middleware\AuthenticateQueryToken::class,
    ]);
    $middleware->statefulApi();
})
```

- **Web routes** prefix: none (e.g. `/dashboard`)
- **API routes** prefix: `/api` (e.g. `/api/transactions`)
- **Health check**: `GET /up`

### Controllers (API layer)

All API logic lives under `app/Http/Controllers/Api/`:

| Controller | File | Responsibility |
|------------|------|----------------|
| `DashboardController` | `DashboardController.php` | Income/expense totals, savings rate, monthly trend, category breakdown |
| `TransactionController` | `TransactionController.php` | CRUD for transactions with filters (type, category, date range) |
| `CategoryController` | `CategoryController.php` | CRUD for income/expense categories |
| `BudgetController` | `BudgetController.php` | CRUD for monthly category budgets |
| `ReportController` | `ReportController.php` | Pivot tables, budget vs actual, trends, Excel/PDF export |

Login is handled inline in `routes/api.php` (not a dedicated controller).

### Eloquent models

| Model | File | Relationships |
|-------|------|-----------------|
| `User` | `app/Models/User.php` | `hasMany` categories; uses `HasApiTokens` (Sanctum) |
| `Category` | `app/Models/Category.php` | `hasMany` transactions, budgets |
| `Transaction` | `app/Models/Transaction.php` | `belongsTo` category |
| `Budget` | `app/Models/Budget.php` | `belongsTo` user, category |

### Database layer

| Path | Purpose |
|------|---------|
| `database/migrations/` | Schema definitions (users, categories, transactions, budgets, personal_access_tokens) |
| `database/seeders/DatabaseSeeder.php` | Seeds demo user, 12 categories, 12 months of transactions & budgets |
| `database/factories/UserFactory.php` | Faker factory used by the seeder |

Run migrations and seed:

```bash
php artisan migrate --seed
```

### Exports

| File | Purpose |
|------|---------|
| `app/Exports/TransactionsExport.php` | Maatwebsite Excel export class |
| `resources/views/exports/transactions-pdf.blade.php` | Blade template rendered to PDF via DomPDF |

### Configuration

| File | Purpose |
|------|---------|
| `config/app.php` | App name, timezone, locale |
| `config/database.php` | MySQL/SQLite connection settings (reads `.env`) |
| `config/auth.php` | Authentication guards |
| `config/sanctum.php` | Sanctum token & stateful domain settings |
| `config/cors.php` | Cross-origin rules (same-origin app, minimal CORS need) |
| `.env` | Local environment variables (not committed) |
| `.env.docker` | Template env values for Docker (`DB_HOST=db`) |

### PHP packages (`composer.json`)

**Production:**
- `laravel/framework` ^11.0
- `laravel/sanctum` ^4.0 — API token auth
- `maatwebsite/excel` ^3.1 — Excel exports
- `barryvdh/laravel-dompdf` ^2.0 — PDF exports
- `guzzlehttp/guzzle` ^7.2

**Development:**
- `laravel/sail` — optional Docker tooling
- `laravel/dusk` — browser testing
- `phpunit/phpunit` ^11.0
- `laravel/pint` — code style

Install dependencies:

```bash
composer install
```

---

## Project Structure

```
Financial Tracker/
├── app/
│   ├── Exports/
│   │   └── TransactionsExport.php       # Excel export logic
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/
│   │   │   │   ├── BudgetController.php
│   │   │   │   ├── CategoryController.php
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── ReportController.php
│   │   │   │   └── TransactionController.php
│   │   │   └── Controller.php           # Base controller
│   │   └── Middleware/
│   │       └── AuthenticateQueryToken.php  # Token via ?token= for downloads
│   ├── Models/
│   │   ├── Budget.php
│   │   ├── Category.php
│   │   ├── Transaction.php
│   │   └── User.php
│   └── Providers/
│       └── AppServiceProvider.php
├── bootstrap/
│   ├── app.php                          # Application bootstrap & routing
│   └── providers.php
├── config/                              # Laravel configuration files
├── database/
│   ├── factories/
│   │   └── UserFactory.php
│   ├── migrations/
│   │   ├── 2024_01_01_000000_create_users_table.php
│   │   ├── 2024_01_01_000001_create_categories_table.php
│   │   ├── 2024_01_01_000002_create_transactions_table.php
│   │   ├── 2024_01_01_000003_create_budgets_table.php
│   │   └── 2019_12_14_000001_create_personal_access_tokens_table.php
│   └── seeders/
│       └── DatabaseSeeder.php           # Demo data (Alex Morgan, 12 months)
├── public/
│   ├── index.php                        # HTTP entry point
│   └── .htaccess                        # Apache rewrite rules
├── resources/
│   └── views/
│       ├── layouts/
│       │   └── app.blade.php            # Main layout, sidebar, apiHeaders
│       ├── exports/
│       │   └── transactions-pdf.blade.php
│       ├── login.blade.php
│       ├── dashboard.blade.php
│       ├── transactions.blade.php
│       ├── categories.blade.php
│       ├── budgets.blade.php
│       └── reports.blade.php
├── routes/
│   ├── web.php                          # Page routes (Blade views)
│   ├── api.php                          # JSON API routes
│   └── console.php                      # Artisan commands
├── storage/                             # Logs, sessions, compiled views, cache
├── docker-compose.yml                   # App + MySQL services
├── Dockerfile                           # PHP 8.2 image for Docker
├── .env.docker                          # Docker environment template
├── HERD_SETUP_INSTRUCTIONS.md           # Detailed Herd setup guide
├── artisan                              # Laravel CLI
├── composer.json                        # PHP dependencies
└── server.php                           # Built-in server router
```

---

## Database Schema

```
users
├── id, name, email, password, remember_token, timestamps

categories
├── id, user_id (FK), name, type (income|expense), color, timestamps

transactions
├── id, user_id (FK), category_id (FK), amount, type (income|expense),
│   date, description, timestamps

budgets
├── id, user_id (FK), category_id (FK), amount, month, year, timestamps

personal_access_tokens          # Laravel Sanctum
├── id, tokenable_type, tokenable_id, name, token, abilities, timestamps
```

All user-owned tables cascade on delete when a user is removed.

---

## API Reference

### Public

| Method | Endpoint | Description |
|--------|----------|-------------|
| `POST` | `/api/login` | Authenticate; returns `{ "token": "..." }` |

**Login body (JSON):**
```json
{ "email": "test@example.com", "password": "password" }
```

### Protected (Bearer token required)

| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/api/categories` | List categories |
| `POST` | `/api/categories` | Create category |
| `PUT/PATCH` | `/api/categories/{id}` | Update category |
| `DELETE` | `/api/categories/{id}` | Delete category |
| `GET` | `/api/transactions` | List transactions (filters: `type`, `category_id`, `date_from`, `date_to`) |
| `POST` | `/api/transactions` | Create transaction |
| `PUT/PATCH` | `/api/transactions/{id}` | Update transaction |
| `DELETE` | `/api/transactions/{id}` | Delete transaction |
| `GET` | `/api/budgets` | List budgets |
| `POST` | `/api/budgets` | Create budget |
| `PUT/PATCH` | `/api/budgets/{id}` | Update budget |
| `DELETE` | `/api/budgets/{id}` | Delete budget |
| `GET` | `/api/dashboard/summary` | Dashboard KPIs, trends, category breakdown |
| `GET` | `/api/reports/pivot?year=2026` | Category × month pivot table |
| `GET` | `/api/reports/budget-vs-actual?month=6&year=2026` | Budget comparison |
| `GET` | `/api/reports/monthly-trend?year=2026` | Monthly income vs expense |
| `GET` | `/api/reports/export-excel?token={token}` | Download Excel (query token) |
| `GET` | `/api/reports/export-pdf?token={token}` | Download PDF (query token) |

---

## Authentication

1. **Login:** `POST /api/login` validates email/password against the `users` table.
2. **Token:** Laravel Sanctum creates a personal access token via `$user->createToken('app')`.
3. **Storage:** Frontend saves the plain-text token in `localStorage` as `auth_token`.
4. **Requests:** Every API call sends `Authorization: Bearer {token}`.
5. **Middleware:** `auth:sanctum` on protected routes in `routes/api.php`.
6. **Exports:** `auth.query` middleware allows passing the token as a URL query parameter for file downloads.

Sanctum configuration: `config/sanctum.php`  
Token table migration: `database/migrations/2019_12_14_000001_create_personal_access_tokens_table.php`

---

## Setup with Laravel Herd (Local)

[Laravel Herd](https://herd.laravel.com/) provides PHP, Composer, and optional services on Windows/macOS without manual PATH setup.

### Quick steps

1. **Install Herd** and open the application.
2. **Link the project:** Herd → **Link** → select `e:\Projects\Financial Tracker`.
3. **Open Herd Terminal** (three-dot menu → Terminal) so PHP and Composer are on PATH.
4. **Install dependencies:**
   ```bash
   composer install
   ```
5. **Environment:**
   ```bash
   cp .env.docker .env   # or create .env manually
   php artisan key:generate
   ```
   For Herd/local MySQL, set in `.env`:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=financial_tracker
   DB_USERNAME=root
   DB_PASSWORD=
   ```
   Create the database:
   ```sql
   CREATE DATABASE financial_tracker;
   ```
   **Alternative (no MySQL):** use SQLite:
   ```
   DB_CONNECTION=sqlite
   DB_DATABASE=database/database.sqlite
   ```
   Then create the file: `touch database/database.sqlite` (or `New-Item` on PowerShell).

6. **Migrate and seed:**
   ```bash
   php artisan migrate --seed
   ```

7. **Start the server:**
   ```bash
   php artisan serve
   ```

8. **Open the app:** [http://localhost:8000/login](http://localhost:8000/login)

See also: `HERD_SETUP_INSTRUCTIONS.md` for step-by-step screenshots-style instructions and troubleshooting.

---

## Setup with Docker

Docker runs the full stack (PHP app + MySQL) in containers. No local PHP/MySQL install required.

### Prerequisites

- [Docker Desktop](https://www.docker.com/products/docker-desktop/) installed and running

### Files involved

| File | Purpose |
|------|---------|
| `docker-compose.yml` | Defines `app` (Laravel) and `db` (MySQL 8.0) services |
| `Dockerfile` | PHP 8.2 image with extensions: `pdo_mysql`, `mbstring`, `gd`, `zip`, etc. |
| `.env.docker` | Environment template with `DB_HOST=db` for container networking |

### Services (`docker-compose.yml`)

| Service | Container name | Port | Details |
|---------|---------------|------|---------|
| `app` | `financial-tracker-app` | `8000:8000` | Runs `composer install`, `key:generate`, `migrate --seed`, `artisan serve` |
| `db` | `financial-tracker-db` | `3306:3306` | MySQL 8.0, database `financial_tracker`, user `laravel` / password `laravel_password` |

### Start

```bash
# Copy Docker env if .env is missing
cp .env.docker .env

# Build and start (detached)
docker-compose up -d

# View logs
docker-compose logs -f app
```

The app container automatically runs migrations and seeders on first start.

**Open the app:** [http://localhost:8000/login](http://localhost:8000/login)

### Stop

```bash
docker-compose down          # Stop containers
docker-compose down -v       # Stop and remove database volume
```

### Run commands inside the container

```bash
docker-compose exec app php artisan migrate:fresh --seed
docker-compose exec app php artisan tinker
```

---

## Environment Configuration

Key variables in `.env`:

| Variable | Local (Herd) | Docker |
|----------|--------------|--------|
| `APP_URL` | `http://localhost:8000` | `http://localhost:8000` |
| `DB_CONNECTION` | `mysql` or `sqlite` | `mysql` |
| `DB_HOST` | `127.0.0.1` | `db` |
| `DB_PORT` | `3306` | `3306` |
| `DB_DATABASE` | `financial_tracker` | `financial_tracker` |
| `DB_USERNAME` | `root` (or your user) | `laravel` |
| `DB_PASSWORD` | (empty or your password) | `laravel_password` |

Generate application key after creating `.env`:

```bash
php artisan key:generate
```

---

## Development Workflow

```bash
# Install PHP dependencies
composer install

# Run migrations (with demo data)
php artisan migrate --seed

# Start development server
php artisan serve

# Fresh database reset
php artisan migrate:fresh --seed

# Clear caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

**Frontend changes:** Edit Blade files under `resources/views/`. No build step — refresh the browser.

**Backend changes:** Edit controllers/models under `app/`. Laravel auto-reloads on each request when using `php artisan serve`.

**Health check:** `GET http://localhost:8000/up`

---

## Demo Credentials

After running `php artisan migrate --seed`:

| Field | Value |
|-------|-------|
| Email | `test@example.com` |
| Password | `password` |
| Name | Alex Morgan |

The seeder creates 12 income/expense categories and a full year of sample transactions and budgets.

---

## Troubleshooting

| Problem | Solution |
|---------|----------|
| `php` or `composer` not found | Use **Herd Terminal**, not plain PowerShell |
| `SQLSTATE[HY000] [2002] Connection refused` | Start MySQL, use Docker, or switch to SQLite in `.env` |
| Blank dashboard / 401 errors | Log in again; check `localStorage.auth_token` in browser DevTools |
| `No application encryption key` | Run `php artisan key:generate` |
| Docker port 8000 in use | Change `"8000:8000"` in `docker-compose.yml` or stop the conflicting process |
| Composer timeout | Run `composer install --no-interaction` |
| Views not updating | Run `php artisan view:clear` |

---

## License

MIT (Laravel framework default license)
