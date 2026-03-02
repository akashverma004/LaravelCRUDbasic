<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin User',
                'email' => 'admin@hrmsai.test',
                'age' => '35',
                'city' => 'New Delhi',
            ],
            [
                'name' => 'HR Manager',
                'email' => 'hr.manager@hrmsai.test',
                'age' => '32',
                'city' => 'Mumbai',
            ],
            [
                'name' => 'Team Manager',
                'email' => 'manager@hrmsai.test',
                'age' => '38',
                'city' => 'Bangalore',
            ],
            [
                'name' => 'HR Officer',
                'email' => 'hr.officer@hrmsai.test',
                'age' => '28',
                'city' => 'Hyderabad',
            ],
            [
                'name' => 'Ava Thomas',
                'email' => 'ava@hrmsai.test',
                'age' => '26',
                'city' => 'Pune',
            ],
        ];

        foreach ($users as $index => $user) {
            User::create($user);
        }

        // Assign roles to users
        User::find(1)->assignRole('admin');
        User::find(2)->assignRole('hr_manager');
        User::find(3)->assignRole('manager');
        User::find(4)->assignRole('hr_officer');
        User::find(5)->assignRole('employee');
    }
}
