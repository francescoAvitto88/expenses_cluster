<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
        ]);
        $admin->assignRole('admin');

        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'user@example.com',
        ]);
        $user->assignRole('user');
    }
}
