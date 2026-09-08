<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Kabag Humas',
            'username' => 'kabag',
            'password' => 'password123',
            'role' => 'kabag',
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Staff Humas',
            'username' => 'staff',
            'password' => 'password123',
            'role' => 'staff',
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Intern Humas',
            'username' => 'intern',
            'password' => 'password123',
            'role' => 'intern',
            'is_active' => true,
        ]);
    }
}