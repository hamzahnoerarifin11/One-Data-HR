Recruitment Feature — One Data HR

Overview
- Recruitment dashboard and CRUD features implemented under `/rekrutmen`.
- Metrics endpoints:
  - `/rekrutmen/metrics/candidates` (filter: `posisi_id`, `from`, `to`)
  - `/rekrutmen/metrics/cv` (filter: `posisi_id`, `from`, `to`)
  - `/rekrutmen/metrics/psikotes` (filter: `posisi_id`, `from`, `to`)
  - `/rekrutmen/metrics/kompetensi` (filter: `posisi_id`, `from`, `to`)
  - `/rekrutmen/metrics/hr`, `/rekrutmen/metrics/user`
  - `/rekrutmen/metrics/progress` (gives percent per stage)
  - `/rekrutmen/metrics/candidates/export` (CSV export, accepts same filters)
  - `/rekrutmen/metrics/cv/export` (CSV export of CV passes)
  - `/rekrutmen/metrics/psikotes/export` (CSV export of psikotes passes)
  - `/rekrutmen/metrics/progress/export` (CSV export of recruitment progress)

Permissions
- Editing recruitment process (`/rekrutmen/proses`) and pemberkasan (`/rekrutmen/pemberkasan/*`) is restricted to users with `role === 'admin'`.

Seeding & Tests
- Run `php artisan db:seed --class=Database\\Seeders\\RecruitmentSeeder` to load sample recruitment data (includes positions, candidates, processes, pemberkasan).
- Tests added: feature tests for metrics, exports, CRUD, and permission checks.
- If `php artisan test` fails due to missing test runner adapters (e.g., Collision), run `composer install` to ensure vendor dependencies are present.

UI
- Recruitment dashboard is implemented using the TailAdmin layout (cards, responsive grid, ApexCharts).
- Dashboard filters update charts and Export CSV dropdown (Kandidat, CV, Psikotes, Progress).

Deployment notes
- Ensure database connection config in `.env` is correct and run `php artisan migrate --seed` in staging/production if seeding is desired.
- Exports stream CSV; ensure `php://output` is allowed by PHP configuration (default for web apps).
- For production, consider restricting export endpoints to authorized roles (currently available to authenticated users; additional policies can be applied).

Notes
- Charts use ApexCharts now.
- Additional exports or role-based policies can be added if required.
