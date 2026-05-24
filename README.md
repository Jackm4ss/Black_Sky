# Black Sky Enterprise

Laravel 11 and React 18 SPA monolith for Black Sky Enterprise event, news, portfolio, member, and admin workflows.

## Stack

- Backend: Laravel 11, PHP 8.2+, Fortify, Sanctum, Spatie Permission, Filament 3
- Frontend: React 18, Vite, React Router 7, TanStack Query, React Hook Form, Zod
- Styling: Tailwind CSS 4, Radix UI primitives, custom design system in `DESIGN.md`
- Local defaults: SQLite, database-backed session/cache/queue, log mailer, Scout collection search

## Requirements

- PHP 8.2+
- Composer
- Node.js and npm
- SQLite for local development
- Production services: Redis, Meilisearch, and a real mail transport

## Setup

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
```

On Windows PowerShell, use this instead of `cp` if needed:

```powershell
Copy-Item .env.example .env
```

The default seeded admin account is controlled by `BLACK_SKY_ADMIN_*` in `.env.example`:

- Email: `admin@blacksky.test`
- Password: `password`

## Development

Start the full local stack:

```bash
composer dev
```

This runs:

- Laravel server on `http://127.0.0.1:8000`
- Queue listener
- Laravel Pail logs
- Vite dev server on `http://127.0.0.1:5173`

Run services individually:

```bash
php artisan serve
npm run dev
php artisan queue:listen
php artisan pail
```

## Verification

```bash
vendor/bin/phpunit
npm run build
```

Focused PHP checks:

```bash
vendor/bin/phpunit tests/Feature/PublicEventTest.php
vendor/bin/phpunit --filter test_public_event_index_returns_upcoming_events
vendor/bin/pint --test app routes tests
```

Apply PHP formatting:

```bash
vendor/bin/pint app routes tests
```

There is no configured JavaScript lint, typecheck, or test script; use `npm run build` for frontend verification.

## Queues, Search, And Mail

Local development intentionally stays lightweight:

- `QUEUE_CONNECTION=database`, `CACHE_STORE=database`, and `SESSION_DRIVER=database`
- `MAIL_MAILER=log`
- `SCOUT_DRIVER=collection`

Production is stricter. `App\Support\ProductionServices` checks `APP_ENV=production` during boot and fails fast unless Redis, Meilisearch, and a real mail transport are configured. Keep `BLACK_SKY_ENFORCE_PRODUCTION_SERVICES=true` for production deploys.

Queued work currently includes member notification broadcasts and verification emails on the `notifications` queue. Production workers should run both `default` and `notifications`.

Public event, news, and portfolio search use Laravel Scout. After content model or search setting changes, sync/import indexes:

```bash
php artisan scout:sync-index-settings
php artisan scout:import "App\Models\Event"
php artisan scout:import "App\Models\BlogPost"
php artisan scout:import "App\Models\PortfolioWork"
```

## First VPS Deployment Notes

Target VPS for the first production deployment:

| Item | Detail |
|---|---|
| Hostname | `server.vimobe.net` |
| Public IP | `84.247.144.89` |
| OS | AlmaLinux 8.10 |
| Virtualization | KVM |
| CPU | 6 vCPU, AMD EPYC Processor |
| RAM | 15 GiB total, about 10 GiB available at audit time |
| Swap | None configured |
| Disk | 200 GB total, root `/` about 194 GB |
| Disk usage | About 50% used at audit time |

This is enough for an initial single-server deployment if the app is kept conservative. Avoid running too many workers because there is no swap; memory spikes from image uploads should fail gracefully instead of pushing the VPS into OOM.

Recommended first-pass services on this VPS:

- Web server: Nginx or Apache in front of PHP-FPM.
- PHP runtime: PHP 8.2+ with OPcache enabled.
- Database: MySQL/MariaDB on the same VPS for the first deploy, then move out later if traffic grows.
- Queue/cache/session: Redis is required in production.
- Search: Meilisearch is required in production for public search indexes.
- Mail: SMTP or another real mail transport is required in production.
- Node.js: needed for `npm run build`, not needed as a long-running production service.
- Scheduler: one Laravel scheduler entry only.

Recommended PHP-FPM sizing for 6 vCPU / 16 GB RAM:

```ini
pm = dynamic
pm.max_children = 24
pm.start_servers = 6
pm.min_spare_servers = 4
pm.max_spare_servers = 10
pm.max_requests = 500
```

If MySQL is also busy on the same VPS, start with `pm.max_children = 18` and increase after checking real memory usage. For image-heavy admin uploads, do not raise this too high; each active upload can temporarily use more memory during compression.

Recommended PHP limits for the current upload policy:

```ini
memory_limit = 512M
upload_max_filesize = 110M
post_max_size = 110M
max_execution_time = 120
max_input_time = 120
opcache.enable=1
opcache.enable_cli=1
opcache.validate_timestamps=0
opcache.memory_consumption=256
opcache.max_accelerated_files=20000
```

Recommended Nginx upload limit:

```nginx
client_max_body_size 110M;
```

Recommended Laravel production commands after pulling code and installing dependencies:

```bash
composer install --no-dev --optimize-autoloader
npm ci
npm run build
php artisan migrate --force
php artisan storage:link
php artisan scout:sync-index-settings
php artisan scout:import "App\Models\Event"
php artisan scout:import "App\Models\BlogPost"
php artisan scout:import "App\Models\PortfolioWork"
php artisan optimize
```

Recommended environment values for first deploy:

```dotenv
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.example
FRONTEND_URL=https://your-domain.example
SANCTUM_STATEFUL_DOMAINS=your-domain.example
SESSION_SECURE_COOKIE=true
SESSION_ENCRYPT=true
LOG_LEVEL=warning
QUEUE_CONNECTION=redis
CACHE_STORE=redis
SESSION_DRIVER=redis
SCOUT_DRIVER=meilisearch
SCOUT_QUEUE=true
MEILISEARCH_HOST=http://127.0.0.1:7700
MEILISEARCH_KEY=change-me
MAIL_MAILER=smtp
MAIL_HOST=smtp.example.com
MAIL_PORT=587
MAIL_USERNAME=your-smtp-user
MAIL_PASSWORD=your-smtp-password
MAIL_FROM_ADDRESS=noreply@your-domain.example
BLACK_SKY_ENFORCE_PRODUCTION_SERVICES=true
```

When `APP_ENV=production`, the app fails fast unless Redis, Meilisearch, and a real mailer are configured. Local and testing environments can keep the lightweight defaults.

Recommended Supervisor queue workers for this VPS:

```ini
[program:blacksky-worker-default]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/Black_Sky/artisan queue:work redis --queue=default --sleep=3 --tries=3 --timeout=120 --memory=256
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
numprocs=2
redirect_stderr=true
stdout_logfile=/path/to/Black_Sky/storage/logs/worker-default.log
stopwaitsecs=180

[program:blacksky-worker-notifications]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/Black_Sky/artisan queue:work redis --queue=notifications --sleep=3 --tries=3 --timeout=120 --memory=256
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
numprocs=2
redirect_stderr=true
stdout_logfile=/path/to/Black_Sky/storage/logs/worker-notifications.log
stopwaitsecs=180
```

Keep total queue workers around 4 processes at first. Increase only after checking CPU, memory, Redis queue wait time, and mail/search indexing throughput.

Recommended scheduler cron:

```cron
* * * * * cd /path/to/Black_Sky && php artisan schedule:run >> /dev/null 2>&1
```

Basic bottleneck checks after deployment:

```bash
free -h
df -h
php artisan queue:failed
php artisan about
php artisan scout:sync-index-settings
tail -f storage/logs/laravel.log
```

Operational notes:

- Add 2-4 GB swap if possible; it is not a performance feature, but it gives the VPS a safety buffer during rare memory spikes.
- Enable log rotation for Laravel logs and Supervisor worker logs.
- Keep only one deployment build active at a time; do not run multiple `npm run build` processes on the VPS.
- Keep uploads on `storage/app/public` for the first deploy, but plan object storage/CDN once media traffic grows.
- Run `php artisan optimize:clear && php artisan optimize` after config or route changes.

### No-Downtime Update Notes

For future revisions and feature updates, use a release-directory deployment instead of editing the live folder directly. The web server should point to a stable `current` symlink, not to one fixed checkout folder.

Recommended directory layout:

```text
/var/www/blacksky/
  current -> /var/www/blacksky/releases/20260524013000
  releases/
    20260524013000/
    20260525094500/
  shared/
    .env
    storage/
```

Nginx should use:

```nginx
root /var/www/blacksky/current/public;
```

Deploy flow for a new release:

```bash
release="/var/www/blacksky/releases/$(date +%Y%m%d%H%M%S)"

git clone --depth=1 git@github.com:your-org/your-repo.git "$release"
cd "$release"

ln -sfn /var/www/blacksky/shared/.env .env
rm -rf storage
ln -sfn /var/www/blacksky/shared/storage storage

composer install --no-dev --optimize-autoloader
npm ci
npm run build

php artisan migrate --force
php artisan storage:link
php artisan scout:sync-index-settings
php artisan scout:import "App\Models\Event"
php artisan scout:import "App\Models\BlogPost"
php artisan scout:import "App\Models\PortfolioWork"
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

ln -sfn "$release" /var/www/blacksky/current

sudo systemctl reload nginx
sudo systemctl reload php-fpm

cd /var/www/blacksky/current
php artisan queue:restart
```

Keep Supervisor worker commands pointed at `/var/www/blacksky/current/artisan`, not at a specific release folder. `php artisan queue:restart` lets existing jobs finish, then workers restart into the new release.

No-downtime migration rules:

- Only deploy backward-compatible migrations while old code can still receive traffic.
- Add nullable columns first, deploy code that writes both old and new fields, backfill data, then remove old columns in a later release.
- Do not rename/drop columns in the same release that starts using the new schema.
- Avoid `php artisan down` for normal releases. Use it only for emergency maintenance.
- Run destructive data changes as queued/background jobs where possible.

Fast rollback:

```bash
previous="/var/www/blacksky/releases/20260524013000"
ln -sfn "$previous" /var/www/blacksky/current
sudo systemctl reload nginx
sudo systemctl reload php-fpm
cd /var/www/blacksky/current && php artisan queue:restart
```

Keep at least the latest 3-5 releases so rollback stays quick:

```bash
ls -dt /var/www/blacksky/releases/* | tail -n +6 | xargs -r rm -rf
```

## Application Map

- `routes/web.php` serves SEO-aware public pages and the SPA catch-all.
- `routes/api.php` contains custom API routes and Sanctum user routes.
- Fortify auth routes are prefixed with `/api`.
- React entrypoint: `src/main.tsx`
- React routes: `src/app/App.tsx`
- API client: `src/app/lib/http.ts`
- Filament admin: `/admin`, with resources and pages under `app/Filament`
- Vite inputs: `src/main.tsx`, `src/filament-auth-shape-grid.tsx`, `src/filament-admin.ts`

## API Highlights

Base URL in local development: `http://127.0.0.1:8000/api`

- `GET /api/health`
- `GET /api/sanctum/csrf-cookie`
- `GET /api/user`
- `GET /api/verify-email/{id}/{hash}`
- `GET /api/v1/events`
- `GET /api/v1/events/{slug}`
- `GET /api/v1/portfolio`
- `GET /api/v1/portfolio/{slug}`
- `GET /api/v1/news`
- `GET /api/v1/news/{slug}`
- `GET /api/v1/me/dashboard`
- `PATCH|POST /api/v1/me/account`
- `PATCH /api/v1/me/password`
- `POST /api/v1/logout`

For cookie-based SPA auth, request `/api/sanctum/csrf-cookie` before login or other state-changing auth requests.

## Database And Seeds

```bash
php artisan migrate
php artisan db:seed
php artisan migrate:fresh --seed
```

Seeders create demo events, news, portfolio work, roles, users, and the admin account.

PHPUnit uses in-memory SQLite and array cache/session/queue from `phpunit.xml`.

## Frontend Notes

- Use `@/` imports for `src/`.
- Keep both the React and Tailwind Vite plugins enabled; they are required by the current build flow.
- Check `DESIGN.md` before user-facing UI changes.

## Common Local Issues

- Auth cookies depend on `SANCTUM_STATEFUL_DOMAINS`; update it if local ports or hosts change.
- Queued work will not run in local development unless `composer dev` or `php artisan queue:listen` is running.
- Admin/media upload work usually needs `php artisan storage:link`.
- Public page changes may need updates in three places: API/resource payloads, React pages, and `routes/web.php` SEO metadata.
