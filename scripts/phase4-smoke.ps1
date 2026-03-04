Set-StrictMode -Version Latest
$ErrorActionPreference = "Stop"

function Run-Step {
    param(
        [Parameter(Mandatory = $true)][string]$Title,
        [Parameter(Mandatory = $true)][string]$Command
    )

    Write-Host ""
    Write-Host "==> $Title" -ForegroundColor Cyan
    powershell -NoProfile -Command $Command
    if ($LASTEXITCODE -ne 0) {
        throw "Step failed: $Title"
    }
}

Run-Step -Title "Framework health" -Command "php artisan about"
Run-Step -Title "Migration status" -Command "php artisan migrate:status"
Run-Step -Title "Critical policy routes" -Command "php artisan route:list --path=api/policies"
Run-Step -Title "Phase 2 tenant tests" -Command "php artisan test --filter=PhaseTwoTenantIsolationTest"
Run-Step -Title "Phase 3 tenant tests" -Command "php artisan test --filter=PhaseThreeTenantBoundaryTest"
Run-Step -Title "Policy feature tests" -Command "php artisan test --filter=PolicyManagementFeatureTest"

Write-Host ""
Write-Host "Phase 4 smoke checks passed." -ForegroundColor Green
