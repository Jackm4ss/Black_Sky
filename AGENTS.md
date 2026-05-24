# Agent Instructions

## Package Manager
- Use Composer for Laravel/PHP and npm for frontend; `package-lock.json` is the JS lockfile.
- No JS lint, typecheck, or test script is configured; use `npm run build` for frontend verification.

## Commands
| Task | Command |
|------|---------|
| Full dev stack | `composer dev` |
| Laravel only | `php artisan serve` |
| Vite only | `npm run dev` |
| Frontend build | `npm run build` |
| All PHP tests | `vendor/bin/phpunit` |
| Single PHP test file | `vendor/bin/phpunit tests/Feature/PublicEventTest.php` |
| Single PHP test | `vendor/bin/phpunit --filter test_public_event_index_returns_upcoming_events` |
| PHP style check/fix | `vendor/bin/pint --test <path>` / `vendor/bin/pint <path>` |
| Local DB reset | `php artisan migrate:fresh --seed` |

## Local Environment
- `.env.example` defaults to SQLite plus database-backed session/cache/queue, log mailer, and Scout collection search.
- `composer dev` runs Laravel on `http://127.0.0.1:8000` and Vite on `http://127.0.0.1:5173`; both ports are in `SANCTUM_STATEFUL_DOMAINS`.
- `DatabaseSeeder` creates the admin from `BLACK_SKY_ADMIN_*`; defaults are `admin@blacksky.test` / `password`.

## Production Services
- Production must use Redis for queue/cache/session, Meilisearch for Scout search, and a real mail transport.
- `App\Support\ProductionServices` fails boot in `APP_ENV=production` when required services are missing; do not weaken this for deploy fixes.
- Public search uses Scout/Meilisearch through `App\Support\PublicSearch`; local/testing may keep database or collection fallback.
- Verification email and member broadcasts use the `notifications` queue; production workers must include `default` and `notifications`.

## Architecture
- Laravel 11 API + React SPA monolith; `routes/web.php` adds SEO metadata for public pages before the SPA catch-all.
- Custom API routes live in `routes/api.php`; Fortify auth routes are prefixed with `/api`, and project endpoints are under `/api/v1`.
- SPA entry is `src/main.tsx` -> `src/app/App.tsx`; React routes live in `src/app/App.tsx`.
- Vite also builds `src/filament-auth-shape-grid.tsx` and `src/filament-admin.ts` for Filament admin UI enhancements.
- Filament admin is mounted at `/admin`; resources/pages live under `app/Filament`.

## Frontend Quirks
- `@/` resolves to `src/`.
- Keep both Vite React and Tailwind plugins; `vite.config.ts` notes Make depends on them even when Tailwind is not actively changed.
- Check `DESIGN.md` before changing user-facing UI; it contains the project tokens and component patterns.
- Rich HTML is sanitized on both sides: keep `app/Support/RichContentSanitizer.php` and `src/app/lib/sanitize-rich-html.ts` allowlists aligned before using `dangerouslySetInnerHTML`.

## Testing Notes
- PHPUnit uses in-memory SQLite and array cache/session/queue from `phpunit.xml`.
- Feature tests that render `view('app')` without built assets should call `$this->withoutVite()`.
- Public page changes often need all three surfaces updated: API controller/resource, SPA route/page, and `routes/web.php` SEO metadata.
- Search model changes may need `php artisan scout:sync-index-settings` and `php artisan scout:import "App\Models\<Model>"` in deployment docs.
- For deployment work, use `README.md` First VPS Deployment Notes and No-Downtime Update Notes; production deploys should use release directories, a `current` symlink, and `php artisan queue:restart`.

## Commit Attribution
- Do not add AI `Co-Authored-By` trailers unless the user explicitly asks for them.
