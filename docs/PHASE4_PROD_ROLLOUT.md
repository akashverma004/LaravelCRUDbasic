# Phase 4 Production Rollout Guide

This guide is for deploying the multi-tenant HRMS safely.

## 1. Pre-Deployment Checklist

- Confirm backup exists for:
  - Application files
  - Database snapshot
- Confirm `.env` is correct for production:
  - `APP_ENV=production`
  - `APP_DEBUG=false`
  - DB credentials
  - Queue/cache/session drivers
- Confirm all pending migrations are reviewed.
- Confirm maintenance window and rollback owner.

## 2. Deployment Steps

Run in order:

```bash
php artisan down --render="errors::503"
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan up
```

Optional (first setup only):

```bash
php artisan db:seed --class=RolePermissionSeeder --force
php artisan db:seed --class=UserSeeder --force
php artisan db:seed --class=PolicySeeder --force
```

## 3. Post-Deployment Validation

Run smoke script:

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\phase4-smoke.ps1
```

Manual checks:

- Login/logout works
- Employee list loads
- Leave calendar loads
- Policy pages (web + API) load
- Admin can approve/reject leave from pending/all tabs

## 4. Rollback Plan

If release is unhealthy:

```bash
php artisan down
# restore previous code artifact
# restore database backup if migration-related breakage
php artisan config:clear
php artisan cache:clear
php artisan up
```

## 5. Operational Notes

- Tenant isolation relies on:
  - `tenant` middleware
  - tenant global scopes on models
  - tenant-scoped validation rules
- Do not bypass Eloquent/global scopes in controllers for tenant data unless explicitly scoped by tenant.

