<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $tenantIds = DB::table('tenants')->pluck('id')->all();
        if (empty($tenantIds)) {
            $tenantIds = [1];
        }

        $users = [
            [
                'name' => 'Admin User',
                'email' => 'admin@hrmsai.test',
                'password' => Hash::make('password123'),
                'age' => '35',
                'city' => 'New Delhi',
            ],
            [
                'name' => 'HR Manager',
                'email' => 'hr.manager@hrmsai.test',
                'password' => Hash::make('password123'),
                'age' => '32',
                'city' => 'Mumbai',
            ],
            [
                'name' => 'Team Manager',
                'email' => 'manager@hrmsai.test',
                'password' => Hash::make('password123'),
                'age' => '38',
                'city' => 'Bangalore',
            ],
            [
                'name' => 'HR Officer',
                'email' => 'hr.officer@hrmsai.test',
                'password' => Hash::make('password123'),
                'age' => '28',
                'city' => 'Hyderabad',
            ],
            [
                'name' => 'Ava Thomas',
                'email' => 'ava@hrmsai.test',
                'password' => Hash::make('password123'),
                'age' => '26',
                'city' => 'Pune',
            ],
        ];

        foreach ($tenantIds as $tenantId) {
            foreach ($users as $user) {
                $isPlatformAdmin = (int) $tenantId === 1 && $user['email'] === 'admin@hrmsai.test';
                User::query()->updateOrCreate(
                    ['tenant_id' => $tenantId, 'email' => $user['email']],
                    array_merge($user, ['tenant_id' => $tenantId, 'is_platform_admin' => $isPlatformAdmin])
                );
            }

            // Assign roles by stable email scoped per tenant.
            $roleMap = [
                'admin@hrmsai.test' => 'admin',
                'hr.manager@hrmsai.test' => 'hr_manager',
                'manager@hrmsai.test' => 'manager',
                'hr.officer@hrmsai.test' => 'hr_officer',
                'ava@hrmsai.test' => 'employee',
            ];

            foreach ($roleMap as $email => $roleName) {
                $user = User::query()
                    ->where('tenant_id', $tenantId)
                    ->where('email', $email)
                    ->first();
                $role = Role::query()
                    ->where('tenant_id', $tenantId)
                    ->where('name', $roleName)
                    ->first();

                if ($user && $role) {
                    $user->assignRole($role);
                }
            }
        }
    }
}
