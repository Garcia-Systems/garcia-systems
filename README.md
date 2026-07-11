# Garcia Systems

**Turning Business Problems Into Products, Systems, and Intelligent Workflows**

Garcia Systems is a Laravel application for presenting practical systems consulting content, AI readiness tools, contact capture, and early opportunity atlas content for operations-focused teams.

## Current Phase

Phase 1 focuses on public positioning and content foundations:

- Published article and video previews.
- AI readiness assessment intake and scoring.
- Contact inquiry capture.
- Opportunity atlas sample content.
- Service and tool landing pages.

## Local Development

Garcia Systems is developed locally with Docker and Laravel Sail. PHP, Composer, MySQL, Redis, Node, Vite, Mailpit, Meilisearch, Selenium, and supporting tooling run in containers; you do not need to install PHP, Composer, Node, MySQL, or Redis directly on your laptop.

### Prerequisites

- Docker Engine or Docker Desktop.
- Docker Compose.
- Git.
- No host PHP, Composer, Node, MySQL, or Redis installation is required.

### Fresh-clone setup

From a fresh checkout, `vendor/bin/sail` will not exist until Composer dependencies have been installed. Bootstrap those dependencies with Laravel Sail's temporary Composer container first:

```bash
git clone git@github.com:Garcia-Systems/garcia-systems.git
cd garcia-systems

docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php85-composer:latest \
    composer install --ignore-platform-reqs

cp .env.example .env
./vendor/bin/sail up -d
./vendor/bin/sail artisan key:generate
./vendor/bin/sail npm install
./vendor/bin/sail artisan migrate --seed
./vendor/bin/sail npm run dev
```

Keep `./vendor/bin/sail npm run dev` running while working on frontend assets. In another terminal, visit the application at `http://localhost`.

Review `.env.example` for required production and optional local variables before deploying, but do not add production deployment instructions here.

### Daily Sail commands

```bash
# Start or restart the local containers in the background.
./vendor/bin/sail up -d

# Stop the local containers without deleting volumes.
./vendor/bin/sail down

# View logs for all containers, or pass a service name such as laravel.test.
./vendor/bin/sail logs
./vendor/bin/sail logs laravel.test

# Open a shell inside the application container.
./vendor/bin/sail shell

# Rebuild containers after Sail runtime, Dockerfile, or dependency changes.
./vendor/bin/sail build --no-cache
./vendor/bin/sail up -d
```

Use Sail for project commands so they run in the same containerized environment as the application:

```bash
./vendor/bin/sail artisan about
./vendor/bin/sail composer install
./vendor/bin/sail npm install
```

### Local services and ports

The local Docker Compose stack exposes these development services:

| Service | URL or host | Default forwarded port | Notes |
| --- | --- | --- | --- |
| Application | `http://localhost` | `${APP_PORT:-80}` | Main Laravel application served by the `laravel.test` container. |
| Vite dev server | `http://localhost:5173` | `${VITE_PORT:-5173}` | Frontend development server used by `./vendor/bin/sail npm run dev`. |
| Mailpit SMTP | `127.0.0.1:1025` | `${FORWARD_MAILPIT_PORT:-1025}` | Development mail capture service; `.env.example` currently defaults mail to the log driver, so Mailpit is a convenience unless local mail settings are changed. |
| Mailpit dashboard | `http://localhost:8025` | `${FORWARD_MAILPIT_DASHBOARD_PORT:-8025}` | Browser UI for messages captured by Mailpit. |
| Meilisearch | `http://localhost:7700` | `${FORWARD_MEILISEARCH_PORT:-7700}` | Development search service included in the Sail stack; currently a convenience and may not be used by application code. |
| MySQL | `127.0.0.1:3306` | `${FORWARD_DB_PORT:-3306}` | MySQL is available to the app container as `mysql`; `.env.example` defaults to SQLite, so update local DB variables if you want Laravel to use this service. |
| Redis | `127.0.0.1:6379` | `${FORWARD_REDIS_PORT:-6379}` | Redis is available to the app container as `redis`; currently a convenience unless cache, queue, or session settings are changed to Redis-backed drivers. |
| Selenium | Internal Docker network only | Not forwarded | Browser-testing convenience service available to containers on the Sail network. |

### Frontend assets

```bash
# Local development with Vite hot reloading.
./vendor/bin/sail npm run dev

# Production asset build, run through Sail.
./vendor/bin/sail npm run build
```

## Testing

Run tests through Sail so PHPUnit uses the containerized PHP environment.

```bash
# Run a focused test file or filter.
./vendor/bin/sail artisan test tests/Feature/ExampleTest.php
./vendor/bin/sail artisan test --filter=ExampleTest

# Run the full test suite.
./vendor/bin/sail artisan test

# Run the same coverage command used by CI.
./vendor/bin/sail artisan test --coverage-clover=coverage.xml
```

## Product Direction

Garcia Systems helps teams turn operational friction into focused software, automation, and intelligent workflow improvements. Future phases may add richer Atlas functionality, admin workflows, authentication, and deeper implementation tooling after the public foundation is stable.

## Database Seeding

Required and optional environment variables are documented in `.env.example`; use placeholder values only and configure real secrets in your local `.env` or deployment platform.

The default `DatabaseSeeder` is safe to run repeatedly for local development: it bootstraps the administrator only when needed, refreshes lookup/reference data, and loads starter public content idempotently.

For production, run seeders intentionally:

```bash
# One-time administrator bootstrap; requires ADMIN_EMAIL and ADMIN_PASSWORD only when the account does not already exist.
./vendor/bin/sail artisan db:seed --class=Database\\Seeders\\AdministratorSeeder --force

# Optional starter content and reference data.
./vendor/bin/sail artisan db:seed --class=Database\\Seeders\\StarterPublicContentSeeder --force
```
