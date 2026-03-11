---
name: peopleflow-hrms
description: Onboard Codex to the PeopleFlow HRMS Laravel application and guide safe changes across its multi-tenant, role-gated modules. Use when working in this repo on routes, controllers, middleware, models, Blade views, policy management, tenant onboarding, self-service, analytics, documents, payroll, shifts, or any change that needs app-specific context about tenant scoping, RBAC, route registration, or validation and service patterns.
---

# PeopleFlow HRMS

## Overview

Use this skill to build working context quickly before changing the app. Read the repo map first, then load only the reference file that matches the feature area you are touching.

## Quick Start

1. Read `references/app-map.md` to identify the relevant route file, controller namespace, and view area.
2. Read `references/access-and-policies.md` if the change touches authentication, tenant scoping, roles, permissions, invitations, onboarding, or policy CRUD and evaluation.
3. Inspect the concrete files named in those references before editing. Do not assume the older service-layer pattern applies everywhere.
4. Preserve tenant scoping and role checks when adding queries, routes, or actions.
5. Validate with the narrowest useful command after edits.

## Follow the App's Real Patterns

- Treat the app as a mixed architecture. Core HR modules are documented with controllers, form requests, services, and Blade views; newer feature areas often use feature-specific controller folders with more logic in-controller.
- Start route discovery in `app/Providers/RouteServiceProvider.php`. The app loads `routes/web.php`, `routes/auth.php`, `routes/notifications.php`, `routes/self-service.php`, `routes/documents.php`, `routes/performance.php`, `routes/analytics.php`, `routes/onboarding.php`, and `routes/extra_features.php`.
- Expect most authenticated web features to run behind `auth`, `tenant`, and `tenant.active`. The main dashboard group in `routes/web.php` also includes `must.change.password` and `tenant.setup`.
- Expect API policy endpoints to live under `routes/api.php` with `auth:sanctum`, `tenant`, and `role:admin,hr_manager`.

## Preserve Tenant and RBAC Guarantees

- Check `app/Http/Kernel.php` for middleware aliases before adding route guards.
- Check `app/Http/Middleware/SetTenantContext.php` and `app/Models/Concerns/BelongsToTenant.php` before changing tenant-aware queries. Many models rely on the global tenant scope and automatic `tenant_id` assignment.
- Check `app/Models/User.php`, `app/Http/Middleware/CheckRole.php`, `app/Http/Middleware/CheckPermission.php`, and the role-management controllers before changing roles or permissions.
- Keep platform-admin flows separate from tenant-admin flows. Tenant management routes use `can:manage-tenants` under `/platform/tenants`.

## Choose the Right Validation

- Run `php -l` on touched PHP files for fast syntax checks.
- Run the smallest relevant Laravel test or feature-specific command available in the repo.
- If you change routes or middleware, re-read the route file and confirm the middleware chain still matches nearby patterns.
- If you change tenant-aware models or controllers, verify every write path still sets or inherits `tenant_id`.

## Use the References

- Use `references/app-map.md` for module boundaries, route files, controller namespaces, and where to look first for common tasks.
- Use `references/access-and-policies.md` for tenant context, role and permission enforcement, invitation and onboarding flows, and the policy management surface.
- Use `rg -n "tenant|role:|permission|PolicyDefinitions|TenantContext"` across `app` and `routes` when a change spans multiple modules.
