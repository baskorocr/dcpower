<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
        ]);

        // Create default admin
        $admin = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@dcpower.com',
            'password' => bcrypt('password'),
            'phone' => '081234567890',
        ]);
        $admin->assignRole('admin');
    }
}
