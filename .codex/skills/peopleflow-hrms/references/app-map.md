# PeopleFlow HRMS App Map

## Core Stack

- Laravel 10 with Blade views, Tailwind CSS, Alpine.js, and Chart.js.
- Main docs in the repo root include `README.md`, `ARCHITECTURE.md`, and `PROJECT_STRUCTURE.md`.
- Route registration starts in `app/Providers/RouteServiceProvider.php`.

## Route Files

- `routes/web.php`: dashboard, attendance punch flows, org chart, departments, employees, leave requests, policy management, roles, user-role assignment, tenant users, and platform tenant management.
- `routes/auth.php`: login, register, company signup, password reset, invitation acceptance, and profile routes.
- `routes/self-service.php`: employee self-service profile, account, photo, education, experience, and skill endpoints.
- `routes/documents.php`: document list, upload, download, delete, and employee picker.
- `routes/performance.php`: goals, reviews, and one-on-one notes.
- `routes/analytics.php`: analytics dashboard and data endpoint.
- `routes/onboarding.php`: onboarding dashboard, task completion, and assignment.
- `routes/extra_features.php`: audit, assets, payroll, and shifts.
- `routes/api.php`: policy CRUD and policy evaluation endpoints for admin and HR manager users.

## Module Map

### Core HR modules

- Departments: `app/Http/Controllers/DepartmentController.php`, service and request classes under `app/Services` and `app/Http/Requests`.
- Employees: `app/Http/Controllers/EmployeeController.php`, with related service and request classes.
- Leaves: `app/Http/Controllers/LeaveRequestController.php`, plus request and service classes.
- Dashboard: `app/Http/Controllers/DashboardController.php`.

### Policy management

- Web controllers: `app/Http/Controllers/Policies/`.
- API controllers: `app/Http/Controllers/Api/Policies/`.
- Shared policy registry: `app/Support/PolicyDefinitions.php`.

### Feature-specific folders

- Documents: `app/Http/Controllers/Documents/`.
- Performance: `app/Http/Controllers/Performance/`.
- Analytics: `app/Http/Controllers/Analytics/`.
- Onboarding: `app/Http/Controllers/Onboarding/`.
- Payroll: `app/Http/Controllers/Payroll/`.
- Shifts: `app/Http/Controllers/Shifts/`.
- Assets: `app/Http/Controllers/Assets/`.
- Audit: `app/Http/Controllers/Audit/`.
- Self-service: `app/Http/Controllers/SelfService/`.

## View Areas

- Older HR UI lives under `resources/views/hrms/`.
- Authentication and profile views follow the auth scaffolding already present in the repo.
- For feature folders added later, verify whether the UI is Blade-driven, AJAX-driven, or mixed before editing markup.

## Working Heuristics

- Start with the route file, then open the controller, then inspect any request, service, model, or Blade files it uses.
- Do not assume every controller delegates to a service. The documented service layer is strongest in the original HR modules.
- When adding a new feature route, place it beside related endpoints in the existing route file instead of growing `routes/web.php` arbitrarily.
- When changing queries, check whether the model uses `App\Models\Concerns\BelongsToTenant`.

## Useful Search Patterns

- Route entry point: `rg -n "Route::" routes`
- Tenant scoping: `rg -n "TenantContext|BelongsToTenant|tenant.active|tenant.setup" app routes`
- Role gates: `rg -n "role:|permission:|can:" routes app`
- Policy surface: `rg -n "PolicyDefinitions|evaluateActive|evaluate" app routes`
