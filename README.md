# Garcia Systems

**Turning Business Problems Into Products, Systems, and Intelligent Workflows**

Garcia Systems is a Laravel application presenting a business-first Solutions Engineering and systems consulting practice, its specialized assessment tools, and applied Opportunity Atlas case studies.

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

Garcia Systems starts with the business question and operating reality, then evaluates existing capabilities, economics, feasibility, and solution options. Recommendations may include using or configuring an existing system, buying a product, integrating systems, building narrowly, deferring work, or doing nothing. AI remains one useful technology within that broader Solutions Engineering toolkit.

## Database Seeding

Required and optional environment variables are documented in `.env.example`; use placeholder values only and configure real secrets in your local `.env` or deployment platform.

### Contact form verification

The public contact form uses Cloudflare Turnstile. Both production and local environments require valid credentials configured through `TURNSTILE_SITE_KEY` and `TURNSTILE_SECRET_KEY`. Never commit the credentials to the repository.

### Fresh/local database setup

The default `DatabaseSeeder` establishes starter articles, videos, Opportunity Atlas examples, and lookup/reference content for a fresh local or test database. Seeders are installation tools; do not use the broad starter seeder to update an established production database.

For fresh environments, run seeders intentionally:

```bash
# One-time administrator bootstrap; requires ADMIN_EMAIL and ADMIN_PASSWORD only when the account does not already exist.
./vendor/bin/sail artisan db:seed --class=Database\\Seeders\\AdministratorSeeder --force

# Optional starter content and reference data.
./vendor/bin/sail artisan db:seed --class=Database\\Seeders\\StarterPublicContentSeeder --force
```

### Existing production database

After deploying an intentional Garcia Systems positioning/reference-data change to Laravel Cloud, use this post-deployment sequence:

```bash
# 1. Deploy tested application code.
# 2. Run only the deployment's normal additive migrations, when present.
php artisan migrate --force

# 3. Preview and apply the narrowly scoped managed reference-content refresh.
php artisan garcia:refresh-positioning-content --dry-run
php artisan garcia:refresh-positioning-content

# 4. Preview and apply the separately scoped managed Atlas-example refresh.
php artisan garcia:refresh-atlas-content --dry-run
php artisan garcia:refresh-atlas-content
```

Inspect every reported create, update, relationship change, and protected/customized skip before applying either refresh. The positioning command creates missing canonical reference records only when their identity does not collide with existing data. The Atlas command manages only explicitly owned demo/case-study workflows, friction points, and their canonical solution-pattern relationships; it expects the positioning reference catalog to be present and does not broadly recreate or rewrite it. Each command updates a record only when both its explicit Garcia Systems managed-content key and its last-applied content hash prove ownership and show that it has not subsequently been customized. A matching slug is not ownership; an unmarked collision, a missing provenance hash, or a changed managed record is skipped rather than claimed or overwritten. Dry-run reports the same decisions with zero persistent writes.

Never use database refresh/reset commands or broad production reseeding for this workflow. Neither refresh command touches articles, videos, publication states, real video URLs, inquiries, assessment submissions, administrator accounts, or other unrelated production content. Legacy Atlas rows without explicit ownership remain protected and may coexist with managed examples.
