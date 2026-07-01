<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin User',
            'username' => 'admin',
            'email' => 'admin@sportsbook.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'status' => 'active',
            'balance' => 10000.00,
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Test User',
            'username' => 'testuser',
            'email' => 'test@sportsbook.com',
            'password' => Hash::make('test1234'),
            'role' => 'user',
            'status' => 'active',
            'balance' => 500.00,
            'email_verified_at' => now(),
        ]);
    }
}
