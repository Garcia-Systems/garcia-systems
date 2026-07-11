# Garcia Systems deployment guide

This guide documents the recommended Laravel Cloud deployment process for `Garcia-Systems/garcia-systems`. It is intentionally documentation-only and does not require changes to application code, Docker configuration, GitHub Actions, authentication, seeders, mail behavior, trusted proxies, or hosting infrastructure.

## 1. Deployment architecture

Laravel Sail is for local development only. This repository uses Sail to provide containerized PHP, Composer, Node, MySQL, Redis, Mailpit, Meilisearch, Selenium, and Vite tooling on developer machines where PHP is not installed directly.

Laravel Cloud should deploy the Laravel application from the GitHub repository, not from `compose.yaml`. The `compose.yaml` stack is a local development convenience and should not be treated as the production topology.

Recommended production components:

- **Laravel web runtime**: serves the Laravel 12 application and compiled Vite/Tailwind assets.
- **Managed MySQL database**: recommended for production data, database-backed sessions, database-backed cache, and database-backed queue tables if queues are enabled.
- **Optional queue worker**: required only when `QUEUE_CONNECTION=database` or another asynchronous queue driver is selected. See [Queue configuration](#10-queue-configuration).
- **External mail provider**: required for outbound lead and AI readiness assessment notifications.
- **Custom domain and HTTPS**: attach the chosen production domain in Laravel Cloud, verify DNS, and confirm HTTPS.

Development-only services from the Sail stack:

- `laravel.test` container image/runtime from Sail.
- Local MySQL container.
- Local Redis container.
- Mailpit SMTP and dashboard.
- Meilisearch.
- Selenium.
- Vite development server.

There is no current production requirement for Meilisearch, Selenium, Mailpit, public file uploads, or a scheduler.

## 2. Environments

Create separate Laravel Cloud environments for **staging** and **production**.

Recommended domain patterns:

- Staging: a staging subdomain such as `staging.<confirmed-domain>`.
- Production: the confirmed root domain such as `<confirmed-domain>`.

Do not invent domain names before the business confirms them. Use the temporary Laravel Cloud domain for initial testing.

Branch strategy:

- Current repository branch: `work`.
- Recommended staging deployment branch: `work` until the team creates a dedicated integration branch.
- Recommended production deployment branch: a protected production branch such as `main` after it exists and is configured in GitHub.
- If Laravel Cloud is configured before a production branch exists, verify the selected branch in the Laravel Cloud dashboard before enabling production auto-deploys.

## 3. Pre-deployment checklist

Before any staging or production deployment:

- Tests are passing.
- Git status is clean.
- Production asset build passes.
- No secrets are committed.
- Database migrations have been reviewed for production impact.
- Seeders have been reviewed and are not configured to run starter content automatically on every deployment.
- Production environment variables are prepared.
- A real mail provider is selected and configured for outbound notifications.
- Database backup plan is confirmed and enabled before production traffic.

## 4. Local verification commands through Laravel Sail

Run project commands through Laravel Sail. Do not use host-installed PHP or Composer commands for local verification.

```bash
# Start containers.
./vendor/bin/sail up -d

# Clear cached configuration before testing environment-sensitive behavior.
./vendor/bin/sail artisan config:clear

# Run focused tests.
./vendor/bin/sail artisan test tests/Feature/LeadTrackingTest.php
./vendor/bin/sail artisan test --filter=TrustedProxyHttpsTest

# Run the full test suite.
./vendor/bin/sail artisan test

# Run the CI-equivalent coverage test command.
./vendor/bin/sail artisan test --coverage-clover=coverage.xml

# Run the production frontend build.
./vendor/bin/sail npm run build

# Inspect Git status.
git status --short
```

## 5. Laravel Cloud application creation

Platform-specific details in this section must be verified in the Laravel Cloud dashboard because this repository does not define Laravel Cloud settings.

1. Connect Laravel Cloud to the GitHub repository `Garcia-Systems/garcia-systems`.
2. Select the deployment branch for the environment.
3. Create the staging environment first.
4. Choose a United States region appropriate for a Virginia-based application. Prefer the nearest suitable US East region available in Laravel Cloud.
5. Select an initial compute size appropriate for a small Laravel application with public pages, admin CRUD, contact intake, assessment intake, and asset serving. Avoid hard-coding pricing or plan names in repository documentation.
6. Attach a managed MySQL database.
7. Enable managed database backups.
8. Use platform-generated database credentials in environment variables. Do not commit or hand-copy credentials into repository files.

## 6. Build commands

Verify Laravel Cloud's detected build pipeline in the dashboard. Laravel Cloud may automatically install PHP dependencies for Laravel applications; if it does, do not duplicate that install step. Configure only missing explicit commands.

Repository-appropriate build commands:

```bash
# Composer production install, when not already handled by Laravel Cloud.
composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader

# Node dependency installation for reproducible Vite builds.
npm ci

# Vite/Tailwind production build.
npm run build

# Laravel optimization after dependencies and environment variables are available.
php artisan optimize
```

Notes:

- Do not run `composer update` during deployment.
- Do not run starter-content seeders during the build phase.
- Do not run `php artisan key:generate` in production. Generate or provide `APP_KEY` securely in environment settings.
- If Laravel Cloud already runs Composer install automatically, configure `npm ci`, `npm run build`, and `php artisan optimize` only where the dashboard requires explicit build steps.

## 7. Deployment commands

Recommended deployment command sequence after a successful build:

```bash
php artisan migrate --force
php artisan config:clear
php artisan optimize
```

Full starter-content seeding must **not** run automatically on every deployment. `DatabaseSeeder` calls administrator bootstrap, lookup/reference data, and starter public content; do not wire `php artisan db:seed` into routine deploys.

Run one-time seeders intentionally only when needed. See [One-time initialization](#8-one-time-initialization).

If a long-running queue worker is configured, restart workers after code is deployed and migrations are complete:

```bash
php artisan queue:restart
```

Restarting workers is not needed when `QUEUE_CONNECTION=sync` and no queue worker process exists.

## 8. One-time initialization

The repository currently provides seeder classes, not custom deployment commands, for production initialization.

### Administrator bootstrap

Run once per environment after `ADMIN_NAME`, `ADMIN_EMAIL`, and `ADMIN_PASSWORD` are configured securely:

```bash
php artisan db:seed --class=Database\\Seeders\\AdministratorSeeder --force
```

When to run:

- Staging: after the first successful staging migration.
- Production: after the first successful production migration and before admin acceptance testing.

Behavior:

- Skips if `ADMIN_EMAIL` is missing.
- Skips without changing the user if an account already exists for `ADMIN_EMAIL`.
- Requires `ADMIN_PASSWORD` only when creating the initial administrator.
- Hashes the password before storing it.

Admin credentials should be temporary bootstrap values and handled through Laravel Cloud environment variables or another secure secret mechanism. Because `AdministratorSeeder` reads `ADMIN_PASSWORD` only when creating a missing admin account and skips existing users without changing passwords, remove `ADMIN_PASSWORD` from the environment after the initial account is created. Keep `ADMIN_EMAIL` and `ADMIN_NAME` only if you intentionally want repeat runs to identify the same bootstrap account; otherwise remove all bootstrap-only values after initialization.

### Starter content and reference data

Run only when the environment intentionally needs starter public content and lookup/reference records:

```bash
php artisan db:seed --class=Database\\Seeders\\StarterPublicContentSeeder --force
```

When to run:

- Staging: acceptable for content smoke testing if the database is empty or disposable.
- Production: only as an explicit launch/content decision, not as an automatic deployment step.

`StarterPublicContentSeeder` calls `LookupReferenceSeeder`, then seeds tags, articles, Opportunity Atlas examples, and videos using idempotent `updateOrCreate` behavior.

## 9. Environment variables

Use `.env.example` as the repository source of truth for expected variables. Never include real credentials in repository files. Staging and production should use different secrets, different database credentials, and different mail-provider credentials or sending domains where practical.

### Application

Required or strongly recommended:

- `APP_NAME`
- `APP_ENV=production` for production and a non-local value such as `staging` for staging.
- `APP_KEY`
- `APP_DEBUG=false`
- `APP_URL`
- `APP_LOCALE`
- `APP_FALLBACK_LOCALE`
- `APP_FAKER_LOCALE`
- `LOG_CHANNEL`
- `LOG_STACK`
- `LOG_LEVEL`

Optional application/logging variables documented in `.env.example` include `APP_PREVIOUS_KEYS`, maintenance settings, Slack logging, Papertrail, syslog, and stderr formatter settings.

### Database

For managed MySQL:

- `DB_CONNECTION=mysql`
- `DB_HOST`
- `DB_PORT`
- `DB_DATABASE`
- `DB_USERNAME`
- `DB_PASSWORD`

Optional database variables include `DB_URL`, `DB_SOCKET`, `DB_CHARSET`, `DB_COLLATION`, `DB_FOREIGN_KEYS`, `MYSQL_ATTR_SSL_CA`, and `DB_SSLMODE`.

### Session, cache, and queue

Recommended initial production values:

- `SESSION_DRIVER=database`
- `SESSION_LIFETIME=120`
- `SESSION_SECURE_COOKIE=true`
- `SESSION_HTTP_ONLY=true`
- `SESSION_SAME_SITE=lax`
- `CACHE_STORE=database`
- `QUEUE_CONNECTION=sync` for first deployment unless a worker is explicitly provisioned; use `database` if a worker is configured.
- `QUEUE_FAILED_DRIVER=database-uuids`

Optional table/connection overrides include `SESSION_CONNECTION`, `SESSION_TABLE`, `DB_CACHE_CONNECTION`, `DB_CACHE_TABLE`, `DB_QUEUE_CONNECTION`, `DB_QUEUE_TABLE`, `DB_QUEUE`, and `DB_QUEUE_RETRY_AFTER`.

### Mail

Production must use a real mailer, not the default log mailer:

- `MAIL_MAILER`
- `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, and optionally `MAIL_SCHEME` for SMTP.
- `MAIL_FROM_ADDRESS`
- `MAIL_FROM_NAME`
- `LEAD_NOTIFICATION_EMAIL`

Provider-specific optional variables include `MAIL_URL`, `MAIL_EHLO_DOMAIN`, `POSTMARK_API_KEY`, `RESEND_API_KEY`, and AWS credentials if using SES.

### Admin bootstrap

Configure only for one-time initialization:

- `ADMIN_NAME`
- `ADMIN_EMAIL`
- `ADMIN_PASSWORD`

Remove `ADMIN_PASSWORD` after the admin account has been created.

### Optional integrations

Configure only if selected:

- Redis variables for Redis-backed cache, queue, or sessions.
- Memcached variables for Memcached-backed cache or sessions.
- AWS variables for SES, SQS, or filesystem integrations.
- SQS, Beanstalkd, DynamoDB, and Slack variables as documented in `.env.example`.

No current production requirement exists for Meilisearch, Selenium, or Mailpit.

## 10. Queue configuration

The current application sends lead and assessment notifications synchronously from the request cycle. `LeadSubmitted` and `AssessmentSubmitted` use the `Queueable` trait but do not implement `ShouldQueue`, and no jobs are dispatched by current application code.

Recommended first deployment:

```env
QUEUE_CONNECTION=sync
```

With `QUEUE_CONNECTION=sync`, no separate queue worker is initially required.

If the team chooses `QUEUE_CONNECTION=database` anyway, or future code adds queued jobs, queued notifications that implement `ShouldQueue`, event listeners that implement `ShouldQueue`, mailables queued with `queue()`, or explicit `dispatch()` calls, provision a worker with:

```bash
php artisan queue:work database --queue=default --tries=3 --timeout=90
```

When a worker is running, include this after deployments:

```bash
php artisan queue:restart
```

Do not speculate about additional workers until repository code introduces asynchronous work.

## 11. Mail configuration

`MAIL_MAILER=log` is useful for development, but it is not suitable for production lead notifications because contact and assessment notifications must be delivered to a real recipient.

Supported provider categories in the repository configuration:

- SMTP providers.
- Amazon SES.
- Postmark.
- Resend.
- Sendmail, only if the platform supports and manages it appropriately.
- Failover or round-robin mailer configurations after primary providers are validated.

Mail testing checklist:

1. Set `MAIL_MAILER` and provider credentials in the Laravel Cloud environment.
2. Set `MAIL_FROM_ADDRESS`, `MAIL_FROM_NAME`, and `LEAD_NOTIFICATION_EMAIL`.
3. Submit `/contact` with a test inquiry and verify that a `LeadSubmitted` notification reaches `LEAD_NOTIFICATION_EMAIL`.
4. Submit `/ai-readiness-assessment` with all current assessment questions answered and verify that an `AssessmentSubmitted` notification reaches `LEAD_NOTIFICATION_EMAIL`.
5. Verify the recipient address in Laravel Cloud environment settings and provider logs.
6. Review Laravel logs for mail transport exceptions.
7. Confirm failure behavior: form data should persist before notification delivery is attempted, and any mail failure should be visible in logs/exception tracking.

## 12. Database migrations, backups, and rollback

Review migrations before deployment, especially migrations that alter existing data, add constraints, drop columns, rename columns, or change indexes.

Backup practice:

- Confirm automated backups are enabled before production launch.
- Take or verify a fresh backup before risky migrations.
- Confirm restore access and retention in the managed database dashboard.

Repository-safe deployment migration command:

```bash
php artisan migrate --force
```

Rollback guidance:

- Prefer code rollback first when a release fails but migrations are backward-compatible.
- Do not recommend or automate destructive production rollbacks.
- For database rollback, inspect the specific migration and data impact first, confirm a backup exists, and decide whether to run a targeted rollback or restore from backup.
- Avoid broad commands such as `migrate:fresh`, `migrate:refresh`, or repeated automatic `migrate:rollback` in production.

After any rollback:

- Verify `/up` returns healthy.
- Verify the home page, articles, videos, Opportunity Atlas, contact form, assessment form, admin login, and admin CRUD.
- Review logs for migration, query, session, cache, and mail errors.
- Confirm database writes still work.

## 13. Domain and HTTPS setup

1. Test the deployed application on the temporary Laravel Cloud domain first.
2. Attach the confirmed custom domain in Laravel Cloud.
3. Create or update DNS records as instructed by Laravel Cloud.
4. Wait for DNS verification in the dashboard.
5. Confirm HTTPS certificate issuance and browser trust.
6. Set `APP_URL` to the canonical HTTPS URL for the environment.
7. Set `SESSION_SECURE_COOKIE=true` for HTTPS environments.
8. Verify trusted proxy handling by confirming generated URLs and redirects use `https://` behind Laravel Cloud's proxy.
9. Choose and verify the root versus `www` redirect behavior in the domain/DNS/dashboard configuration. This repository does not currently define an application-level root/www redirect.

## 14. Staging acceptance checklist

Validate these items in staging before production launch:

- Home page loads.
- Articles index and article detail pages load.
- Videos page loads.
- Opportunity Atlas index and detail pages load.
- Contact form validates, writes to the database, and sends outbound mail.
- AI readiness assessment validates, writes assessment/lead records, and sends outbound mail.
- Admin login works.
- Admin CRUD works for articles, videos, categories, tags, leads, and Opportunity Atlas resources.
- Database writes persist across requests.
- Outbound mail arrives at the configured recipient.
- Mobile layout and navigation work.
- HTTPS is valid.
- Logs show no unresolved errors.
- Health endpoint `/up` returns healthy.

## 15. Production launch checklist

Before launch:

- Staging has been approved.
- Production managed MySQL database is ready.
- Backups are enabled.
- Production secrets are set and differ from staging.
- Production mail provider is configured and tested.
- Administrator bootstrap is completed.
- Migrations are complete.
- Custom domain is connected.
- HTTPS certificate is valid.
- `APP_DEBUG=false` and no debug output is visible.
- Logs and monitoring have been reviewed.
- Rollback path is understood by the release owner.

## 16. Routine deployment procedure

Recommended sequence for later deployments:

1. Confirm CI is passing for the commit to deploy.
2. Review the diff, especially migrations and environment variable changes.
3. Run local Sail verification for changed areas.
4. Build assets with `./vendor/bin/sail npm run build`.
5. Confirm `git status --short` is clean.
6. Deploy to staging from the configured staging branch.
7. Let Laravel Cloud run automated build steps. Verify whether Composer install is automatic in the dashboard; configure only missing build commands.
8. Run deployment commands: `php artisan migrate --force`, then `php artisan config:clear`, then `php artisan optimize`.
9. Restart queue workers only if a worker is provisioned.
10. Smoke-test staging, including forms and `/up`.
11. Promote or merge the approved code to the production deployment branch.
12. Deploy production.
13. Run production migrations with `php artisan migrate --force`.
14. Restart queue workers only if a worker is provisioned.
15. Smoke-test production.
16. Review logs and mail-provider events.

Manual steps include migration review, backup confirmation for risky migrations, environment variable updates, mail-provider verification, acceptance testing, and production go/no-go approval.

Automated or platform-managed steps may include source checkout, dependency installation, build execution, deploy orchestration, HTTPS certificate management, and database backup scheduling, depending on Laravel Cloud dashboard settings.

Do not repeat starter-content seeding during routine deployments.

## 17. Troubleshooting

### Missing `APP_KEY`

Symptoms: encryption, session, or cookie errors; app may fail to boot.

Fix: set a valid `APP_KEY` in Laravel Cloud environment variables. Generate securely outside source control; do not run `key:generate` as a recurring deploy step.

### Database authentication failure

Symptoms: migration failures, 500 errors, session/cache failures with database drivers.

Fix: verify `DB_CONNECTION=mysql`, platform-generated host, port, database, username, and password. Confirm the app environment is attached to the managed database.

### Stale cached environment configuration

Symptoms: app uses old mail, database, session, or URL values after environment changes.

Fix:

```bash
php artisan config:clear
php artisan optimize
```

### Missing Vite manifest

Symptoms: pages fail because `public/build/manifest.json` is missing.

Fix: ensure Node dependencies are installed and `npm run build` runs during the build phase.

### 419 CSRF errors

Symptoms: login, contact, or assessment posts fail with page-expired errors.

Fix: verify `APP_URL`, `SESSION_DRIVER=database`, session table migration, `SESSION_DOMAIN`, `SESSION_SECURE_COOKIE=true` on HTTPS, trusted proxy behavior, and HTTPS redirects.

### Secure-cookie or HTTPS redirect problems

Symptoms: cookies not set, users cannot stay logged in, generated links use HTTP.

Fix: confirm HTTPS certificate, `APP_URL=https://...`, `SESSION_SECURE_COOKIE=true`, and trusted proxy handling. This repository trusts forwarded proxy headers in `bootstrap/app.php`.

### Mail delivery failure

Symptoms: contact or assessment submission errors, or no notification arrives.

Fix: replace `MAIL_MAILER=log` with a real provider, verify credentials, sender/domain verification, `LEAD_NOTIFICATION_EMAIL`, provider events, and Laravel logs.

### Migration failure

Symptoms: deployment command fails or app errors after schema changes.

Fix: stop the release, inspect the failing migration, confirm database backup, decide whether code rollback is enough, and avoid destructive automatic rollback.

### Admin user missing

Symptoms: `/login` works but no known administrator can sign in.

Fix: set temporary `ADMIN_EMAIL`, `ADMIN_PASSWORD`, and optional `ADMIN_NAME`, then run:

```bash
php artisan db:seed --class=Database\\Seeders\\AdministratorSeeder --force
```

Remove `ADMIN_PASSWORD` after creation.

### Writable storage/cache directory errors

Symptoms: view, cache, log, or session write errors.

Fix: verify the Laravel Cloud runtime has writable `storage` and `bootstrap/cache` directories. Clear and rebuild optimization caches after the platform issue is resolved.

## 18. Go/no-go decision template

Use this for each release:

- Release identifier:
- Commit SHA:
- Staging branch/environment:
- Production branch/environment:
- Release owner:
- Migration review completed:
- Backup confirmed:
- Environment variable changes reviewed:
- Mail provider configuration verified:
- Queue worker requirement reviewed:
- Tests passed:
- Asset build passed:
- Staging smoke test passed:
- Production smoke test plan ready:
- Rollback owner and path confirmed:

Go/no-go checklist:

- [ ] CI/tests are passing.
- [ ] Local or staging production asset build passes.
- [ ] Git status is clean before tagging or promoting.
- [ ] Migrations have been reviewed.
- [ ] Backup is enabled and confirmed before risky migrations.
- [ ] `APP_KEY`, database variables, session/cache settings, and mail variables are configured.
- [ ] `MAIL_MAILER` is not `log` in production.
- [ ] `LEAD_NOTIFICATION_EMAIL` is correct.
- [ ] Admin bootstrap has been completed or intentionally deferred with a documented access plan.
- [ ] Staging acceptance checklist is complete.
- [ ] Production rollback path is understood.

Do **not** deploy when any of the following are true:

- Tests fail.
- Production asset build fails.
- Migrations fail or have unresolved risk.
- Required backups are missing or unverified.
- Production mail configuration is missing or still uses `MAIL_MAILER=log`.
- Required secrets are missing.
- Unresolved production errors are present in logs or monitoring.
- The release owner cannot explain the code rollback and database rollback/restore path.
