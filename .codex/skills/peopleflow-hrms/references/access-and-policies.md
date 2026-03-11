# Access and Policies

## Tenant Context

- Middleware aliases are defined in `app/Http/Kernel.php`.
- `tenant` maps to `app/Http/Middleware/SetTenantContext.php`.
- `tenant.active` maps to `app/Http/Middleware/EnsureTenantIsActive.php`.
- `tenant.setup` maps to `app/Http/Middleware/RedirectIfTenantSetupIncomplete.php`.
- `must.change.password` maps to `app/Http/Middleware/MustChangePassword.php`.
- `SetTenantContext` resolves tenant ID from the authenticated user first, then from the `X-Tenant-Id` header.

## Model Scoping

- Models using `App\Models\Concerns\BelongsToTenant` receive a global scope on `tenant_id`.
- The same trait auto-fills `tenant_id` on create when `TenantContext` is set.
- When bypassing the global scope intentionally, use the model's tenant-aware helpers or make the scope removal explicit and justified.

## RBAC Surface

- Route guards commonly use `role:admin,hr_manager`.
- Platform tenant management is separated behind `can:manage-tenants`.
- User-role behavior is centered in `app/Models/User.php`, `app/Http/Controllers/UserRoleController.php`, and `app/Http/Controllers/RoleController.php`.
- Roles are tenant-bound through the `user_role` pivot and tenant-aware role queries in `User::roles()`.

## Authentication and Invitation Flows

- Auth routes live in `routes/auth.php`.
- Company creation starts in `CompanySignupController`.
- Tenant invitation acceptance starts in `TenantInvitationController`.
- The main app routes in `routes/web.php` require password change, tenant activation, and tenant setup before normal dashboard access.

## Policy Surface

### Web policy management

- Route prefix: `/policies` in `routes/web.php`.
- Public policy viewer: dashboard controller action `myPolicies`.
- Admin and HR manager management UI: controllers in `app/Http/Controllers/Policies/`.

### API policy management

- Route prefix: `/api/policies` in `routes/api.php`.
- Protected by `auth:sanctum`, `tenant`, and `role:admin,hr_manager`.
- Every policy type exposes CRUD plus `POST /{type}/{id}/evaluate` and `POST /{type}/evaluate-active`.

### Registered policy types

- `leave`
- `attendance`
- `holiday`
- `payroll`
- `probation`
- `notice-period`
- `overtime`
- `wfh`
- `reimbursement`
- `code-of-conduct`

## Policy Change Workflow

1. Inspect `app/Support/PolicyDefinitions.php` for the canonical slug, model, and field schema.
2. Inspect the matching web controller and API controller.
3. Inspect the model and any request class involved in persistence or evaluation.
4. Preserve tenant filtering in list, create, update, and evaluate paths.
5. Re-check both `routes/web.php` and `routes/api.php` if the change affects URLs or allowed actions.
