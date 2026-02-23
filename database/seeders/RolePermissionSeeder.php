<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            'manage-projects',
            'manage-users',
            'manage-distributors',
            'manage-products',
            'scan-qr',
            'manage-stock',
            'stock-out',
            'manage-sales',
            'view-sales',
            'manage-claims',
            'view-claims',
            'submit-claims',
            'approve-claims',
            'manage-roles',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions(Permission::all());

        $projectManager = Role::firstOrCreate(['name' => 'project_manager']);
        $projectManager->syncPermissions([
            'manage-users',
            'manage-distributors',
            'manage-products',
            'manage-stock',
            'stock-out',
            'manage-sales',
            'manage-claims',
            'approve-claims',
        ]);

        $qa = Role::firstOrCreate(['name' => 'qa']);
        $qa->syncPermissions(['manage-products', 'scan-qr', 'stock-out']);

        $distributor = Role::firstOrCreate(['name' => 'distributor']);
        $distributor->syncPermissions(['view-sales', 'manage-sales', 'manage-stock']);

        $buyer = Role::firstOrCreate(['name' => 'buyer']);
        $buyer->syncPermissions(['view-sales', 'submit-claims', 'view-claims']);
    }
}
