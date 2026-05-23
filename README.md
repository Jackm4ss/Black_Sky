# Black Sky Enterprise

Laravel 11 and React 18 SPA monolith for Black Sky Enterprise event, news, portfolio, member, and admin workflows.

## Stack

- Backend: Laravel 11, PHP 8.2+, Fortify, Sanctum, Spatie Permission, Filament 3
- Frontend: React 18, Vite, React Router 7, TanStack Query, React Hook Form, Zod
- Styling: Tailwind CSS 4, Radix UI primitives, custom design system in `DESIGN.md`
- Local defaults: SQLite, database-backed session/cache/queue, log mailer

## Requirements

- PHP 8.2+
- Composer
- Node.js and npm
- SQLite for local development

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
- `figma:asset/<file>` imports resolve to `src/assets/<file>` in `vite.config.ts`.
- Keep both the React and Tailwind Vite plugins enabled; they are required by the current build flow.
- Check `DESIGN.md` before user-facing UI changes.

## Common Local Issues

- Auth cookies depend on `SANCTUM_STATEFUL_DOMAINS`; update it if local ports or hosts change.
- Queued work will not run in local development unless `composer dev` or `php artisan queue:listen` is running.
- Admin/media upload work usually needs `php artisan storage:link`.
- Public page changes may need updates in three places: API/resource payloads, React pages, and `routes/web.php` SEO metadata.
