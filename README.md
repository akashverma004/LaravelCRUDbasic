# PeopleFlow HRMS (Laravel)

A modern HRMS starter built with **Laravel 10**, **Tailwind CSS**, **Alpine.js**, and **Chart.js**.
The app is structured to be scalable and deployment-friendly for shared hosting like **Hostinger**.

## Modules included

- HR dashboard with live metrics (employees, departments, leaves, attendance)
- Department management
- Employee management
- Leave request management
- Attendance overview
- Seeded demo data for quick onboarding

## Tech stack

- PHP 8.1+
- Laravel 10
- Blade + Tailwind CSS
- Alpine.js (interactive tabs)
- Chart.js (analytics visualization)
- MySQL/SQLite compatible schema

## Local setup

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm install
npm run build
php artisan serve
```

Open: `http://127.0.0.1:8000`

## Hostinger deployment guide

1. Create a MySQL database in Hostinger hPanel.
2. Upload project files to your hosting account (excluding large local-only folders if needed).
3. Point domain document root to `public/`.
4. Configure `.env`:
   - `APP_ENV=production`
   - `APP_DEBUG=false`
   - Database credentials from hPanel
5. Run on server terminal/SSH:

```bash
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan db:seed --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

6. Ensure `storage/` and `bootstrap/cache/` are writable.

## Scalability notes

- Uses Eloquent relationships for clean module separation.
- Tables are normalized with indexed relationships and unique constraints.
- Designed for extension with payroll, performance, and recruitment modules.

## Operations & UAT

- Production rollout guide: `docs/PHASE4_PROD_ROLLOUT.md`
- Multi-tenant UAT checklist: `docs/UAT_MULTITENANT_CHECKLIST.md`
- Smoke checks script (PowerShell): `scripts/phase4-smoke.ps1`
