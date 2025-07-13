<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Employee;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminEmployee = Employee::create([
            'employee_id' => Str::uuid(),
            'name' => 'Administrator',
            'join_date' => now(),
        ]);

        $hrdEmployee = Employee::create([
            'employee_id' => Str::uuid(),
            'name' => 'Agus Saputra',
            'join_date' => now(),
        ]);

        User::create([
            'user_id' => Str::uuid(),
            'email' => 'admin@admin.com',
            'password' => Hash::make('bosskubabi123'),
            'role' => 'admin',
            'employee_id' => $adminEmployee->employee_id,
        ]);

        User::create([
            'user_id' => Str::uuid(),
            'email' => 'agus@gmail.com',
            'password' => Hash::make('password123'),
            'role' => 'hr',
            'employee_id' => $hrdEmployee->employee_id,
        ]);
    }
}
