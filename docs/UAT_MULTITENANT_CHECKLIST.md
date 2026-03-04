# UAT Multi-Tenant Checklist

Use two tenants: `Tenant A` and `Tenant B`.

## A. Access & Identity

- Create admin user in both tenants.
- Login as Tenant A admin and verify:
  - only Tenant A employees/departments/leaves are visible.
- Login as Tenant B admin and verify same isolation.

## B. Employee & Department Isolation

- Tenant A:
  - Create department + employee.
  - Try creating employee with Tenant B department ID (should fail validation).
- Tenant B:
  - Search employee by email that exists in both tenants.
  - Confirm only Tenant B employee appears.

## C. Role/Permission Isolation

- Tenant A:
  - Create a role and assign to a user.
- Tenant B:
  - Attempt assignment using Tenant A role ID via form/API request.
  - Confirm request is rejected.

## D. Leave Isolation

- Tenant A:
  - Create leave request for Tenant A employee (should pass).
  - Try leave for Tenant B employee ID (should fail).
- Tenant B:
  - Open leave calendar and confirm Tenant A leaves are not visible.

## E. Policy Isolation

- Tenant A:
  - Create/update leave policy via API.
- Tenant B:
  - Confirm Tenant A policy is not listed.
- API check:
  - Send `tenant_id` of another tenant in payload.
  - Confirm server still writes/reads current tenant context only.

## F. Holiday Policy & Calendar Isolation

- Tenant A:
  - Create holiday policy + dates.
- Tenant B:
  - Confirm those policies/dates are not visible/editable.

## G. Seeder Verification

- Run seeders.
- Confirm each tenant has:
  - default roles/permissions
  - default policy records
  - sample hierarchy data

## H. Regression Smoke

Run:

```bash
php artisan test --filter=PhaseTwoTenantIsolationTest
php artisan test --filter=PhaseThreeTenantBoundaryTest
php artisan test --filter=PolicyManagementFeatureTest
```

