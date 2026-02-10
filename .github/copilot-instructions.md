## Quick context for AI coding agents

This repository is a Laravel (PHP 8.2, Laravel 12) monolith for a salon/nails booking app.
Focus: backend Laravel controllers and services in `app/`, Blade views in `resources/views`, and a Vite/Tailwind frontend build.

Key files to consult first
- `routes/web.php` — central routing, includes `api/salon` API routes and web routes (e.g. `booking.*`, `salon.*`).
- `app/Http/Controllers/*` — controllers map HTTP endpoints to domain logic.
- `app/Services/Salon/` and `app/Services/` — business logic and service boundaries (use these rather than littering controllers).
- `app/Models/` — domain models (see `Appointment.php`, `Customer.php`, `Service.php`).
- `resources/views/salon/*` — Blade templates for UI (example: `resources/views/salon/booking/waiting-list.blade.php`).
- `app/Providers/AppServiceProvider.php` — application boot behavior (shares `currencySymbol` globally; database schema checks can be present in boot).

Developer workflows & commands (concrete)
- Local dev: `composer run dev` (defined in `composer.json`) runs `php artisan serve`, `php artisan queue:listen`, and `npm run dev` concurrently.
- Setup: `composer run setup` will install dependencies, copy `.env.example` to `.env`, run migrations and build assets.
- Frontend: `npm run dev` (Vite) and `npm run build` for production — see `package.json`.
- Tests: `composer run test` or `php artisan test`; PHPUnit is configured to use in-memory SQLite in `phpunit.xml` (DB_CONNECTION=sqlite, DB_DATABASE=:memory:). Write tests assuming that environment.

Project-specific conventions
- API namespace: internal salon API routes are prefixed with `api/salon` (see `routes/web.php`). Use `api.salon.*` route names when linking.
- Middleware: many salon routes use `salon.auth` — ensure new endpoints respect this middleware or are explicitly public.
- Data flows: controllers are thin; domain logic lives in `app/Services` and models. Prefer adding methods to services rather than expanding controllers.
- Views: Blade templates use shared view data from providers (e.g. `currencySymbol` from `AppServiceProvider`). If you need global view data, check providers first.

Integration & infra notes
- Database migrations live in `database/migrations`. Use Laravel migrations for schema changes; tests rely on in-memory sqlite.
- Storage: public assets are in `public/build` (Vite output). If servicing user uploads, check `public/storage` and run `php artisan storage:link` when relevant.
- Queues: dev uses `php artisan queue:listen` (composer dev script). Tests set `QUEUE_CONNECTION=sync`.

Examples (where to change behavior)
- Add an API endpoint: add route to `routes/web.php` under `api/salon`, implement handler in `app/Http/Controllers/Salon*Controller.php`, move business rules to `app/Services/Salon`.
- Add a view helper: update `AppServiceProvider::boot()` (`app/Providers/AppServiceProvider.php`) to share values across views.

Do not assume
- No existing Copilot/agent docs found — this file is the authoritative short summary.
- External 3rd-party integrations are not present in root manifests; consult `composer.json` and `package.json` for packages.

Verification checklist for PRs by an agent
- Runs: `composer install` then `composer run test` (or `php artisan test`) — tests should pass locally with in-memory sqlite.
- Frontend build: `npm ci` then `npm run build` if assets changed.
- Ensure new routes have names and are placed under correct prefixes (`api/salon` or `salon.`).

If anything here is unclear or you want additional rules (formatting, linting, test style, required PR checklists), tell me which area to expand and I will iterate.
