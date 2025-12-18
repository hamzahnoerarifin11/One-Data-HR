# Release notes — feat/rekrutmen-tailadmin

Release date: 2025-12-17

Summary:
- Full recruitment UI refactor to TailAdmin/Tailwind:** reworked recruitment pages (dashboard, kandidat, posisi management, pemberkasan, per-stage metric pages) to TailAdmin-styled components.
- Reusable modal dialog implemented and used across the recruitment UI (replaces browser confirm()).
- Posisi management: CRUD API (admin-protected for mutations) + AJAX-driven management UI and an inline Add-Posisi modal on dashboard.
- Daily recruitment metrics: new `rekrutmen_daily` table, model, API (index/store/update/destroy), calendar-style demo UI, and seeding for demo data.
- Per-stage metric pages: CV, Psikotes, Kompetensi, Interview HR/User — with charts and CSV export endpoints.
- Pemberkasan monitoring page implemented and wired to metrics.
- Seeders added: `PosisiModalTestSeeder`, `RekrutmenDailySeeder`. Database seeding and a `db:setup` composer script available.
- Authorization: mutation endpoints for posisi and rekrutmen daily are restricted to `role === 'admin'`.
- Tests: Feature tests added for posisi authorization, posisi CRUD flows, rekrutmen daily endpoints, and per-stage pages.
- CI: GitHub Actions workflow added to run migrations, seeders, and tests on push/PR.

Notes & follow-ups:
- Local PHPUnit install has dev-dependency conflicts in some local environments; CI runs tests in GitHub Actions. If you want local tests, I can attempt a safe dev-dependency upgrade, or you can run tests in CI.
- Manual browser QA recommended: verify Add-Posisi modal in dashboard, calendar editor flow (add/edit), per-stage pages charts, and pemberkasan monitor behavior.

How to apply locally:
1. composer install
2. php artisan migrate --force
3. php artisan db:seed
4. Start server: php artisan serve
5. Visit /dashboard and verify recruitment pages

Changelog: see commits on branch `feat/rekrutmen-tailadmin` for file-level diffs.
