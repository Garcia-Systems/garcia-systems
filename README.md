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
