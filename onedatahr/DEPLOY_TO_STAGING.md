Deploy to staging — Minimal steps

1) Ensure branch `feat/rekrutmen-tailadmin` is merged into `main` (or create PR for review).
2) On staging server:
   - git fetch && git checkout main && git pull origin main
   - composer install --no-dev --optimize-autoloader
   - cp .env.example .env (update DB and environment settings)
   - php artisan key:generate
   - php artisan migrate --force
   - php artisan db:seed --class=Database\\Seeders\\PosisiModalTestSeeder --class=Database\\Seeders\\RekrutmenDailySeeder   - php artisan migrate --force (ensure new migration adding rekrutmen_daily stage counts is applied)   - php artisan cache:clear && php artisan config:clear
   - Restart php-fpm / queue workers if necessary
3) Smoke tests on staging:
   - Login as admin -> verify dashboard KPIs
   - Test add-posisi, calendar edits, per-stage pages, pemberkasan monitor
   - Verify calendar candidate assignment workflows (create via quick-add and existing candidate assignment)
4) Run tests locally before deploying:
   - Ensure your .env.testing is configured or use sqlite memory for tests.
   - Run migrations: `php artisan migrate`
   - Run test suite: `composer test` (or `php artisan test` / `vendor/bin/phpunit`)
   - Fix failing tests before pushing to staging.

5) Rollback: if database changes cause issues, restore DB from backup and revert commit.

Note:
- Prefer running migrations via CI/CD pipeline with atomic deploys and backups.
- If seeding on staging, avoid seeding production-only seeders that may overwrite real data.
