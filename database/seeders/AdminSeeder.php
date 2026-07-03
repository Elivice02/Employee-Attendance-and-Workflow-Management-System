<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'System Administrator',
            'email' => 'elivicebarthon0210@gmail.com',
            'password' => Hash::make('employee2026'),
            'role' => 'admin',
            'must_change_password' => true,
        ]);
    }
}