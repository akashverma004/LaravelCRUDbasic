<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $nameToCode = [
            'ANDAMAN AND NICOBAR ISLANDS' => 'AN',
            'ANDHRA PRADESH' => 'AP',
            'ARUNACHAL PRADESH' => 'AR',
            'ASSAM' => 'AS',
            'BIHAR' => 'BR',
            'CHANDIGARH' => 'CH',
            'CHHATTISGARH' => 'CT',
            'DADRA AND NAGAR HAVELI AND DAMAN AND DIU' => 'DN',
            'DELHI' => 'DL',
            'GOA' => 'GA',
            'GUJARAT' => 'GJ',
            'HIMACHAL PRADESH' => 'HP',
            'HARYANA' => 'HR',
            'JHARKHAND' => 'JH',
            'JAMMU AND KASHMIR' => 'JK',
            'KARNATAKA' => 'KA',
            'KERALA' => 'KL',
            'LADAKH' => 'LA',
            'LAKSHADWEEP' => 'LD',
            'MAHARASHTRA' => 'MH',
            'MEGHALAYA' => 'ML',
            'MANIPUR' => 'MN',
            'MADHYA PRADESH' => 'MP',
            'MIZORAM' => 'MZ',
            'NAGALAND' => 'NL',
            'ODISHA' => 'OD',
            'PUNJAB' => 'PB',
            'PUDUCHERRY' => 'PY',
            'RAJASTHAN' => 'RJ',
            'SIKKIM' => 'SK',
            'TELANGANA' => 'TG',
            'TAMIL NADU' => 'TN',
            'TRIPURA' => 'TR',
            'UTTAR PRADESH' => 'UP',
            'UTTARAKHAND' => 'UT',
            'WEST BENGAL' => 'WB',
        ];

        $employees = DB::table('employees')->select(['id', 'country', 'state'])->get();

        foreach ($employees as $employee) {
            $country = strtoupper(trim((string) ($employee->country ?? 'IN')));
            $stateRaw = strtoupper(trim((string) ($employee->state ?? '')));
            $state = $nameToCode[$stateRaw] ?? (strlen($stateRaw) <= 3 ? $stateRaw : null);

            if (! $state) {
                continue;
            }

            DB::table('employees')
                ->where('id', $employee->id)
                ->update([
                    'country' => $country,
                    'state' => $state,
                ]);
        }

        $policies = DB::table('holiday_policies')->select(['id', 'country_code', 'state_code'])->get();
        foreach ($policies as $policy) {
            $country = strtoupper(trim((string) ($policy->country_code ?? 'IN')));
            $stateRaw = strtoupper(trim((string) ($policy->state_code ?? '')));
            $state = $nameToCode[$stateRaw] ?? (strlen($stateRaw) <= 3 ? $stateRaw : null);

            if (! $state) {
                continue;
            }

            DB::table('holiday_policies')
                ->where('id', $policy->id)
                ->update([
                    'country_code' => $country,
                    'state_code' => $state,
                ]);
        }
    }

    public function down(): void
    {
        // No-op: cannot safely restore old free-text values.
    }
};
