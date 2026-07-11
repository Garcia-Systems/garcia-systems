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

```bash
composer install
npm install
cp .env.example .env
# Review .env.example for required production and optional local variables before deploying.
php artisan key:generate
php artisan migrate --seed
npm run dev
php artisan serve
```

## Testing

```bash
php artisan test
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
