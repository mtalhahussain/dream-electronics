<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions only if they don't exist
        $permissions = [
            // Product permissions
            'view-products',
            'create-products',
            'edit-products',
            'delete-products',
            
            // Customer permissions
            'view-customers',
            'create-customers',
            'edit-customers',
            'delete-customers',
            
            // Sales permissions
            'view-sales',
            'create-sales',
            'edit-sales',
            'delete-sales',
            'pay-installments',
            
            // Finance permissions
            'view-finance',
            'manage-finance',
            
            // Employee permissions
            'view-employees',
            'create-employees',
            'edit-employees',
            'delete-employees',
            
            // Branch permissions
            'view-branches',
            'create-branches',
            'edit-branches',
            'delete-branches',
            
            // SMS permissions
            'view-sms',
            'send-sms',
            'manage-sms',
            
            // Settings permissions
            'view-settings',
            'manage-settings',
            
            // Reports permissions
            'view-reports',
            'export-reports',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions only if they don't exist
        
        // Admin role - has all permissions
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        if ($adminRole->permissions()->count() === 0) {
            $adminRole->givePermissionTo(Permission::all());
        }

        // Manager role - has most permissions except system management
        $managerRole = Role::firstOrCreate(['name' => 'manager']);
        if ($managerRole->permissions()->count() === 0) {
            $managerRole->givePermissionTo([
                'view-products', 'create-products', 'edit-products',
                'view-customers', 'create-customers', 'edit-customers',
                'view-sales', 'create-sales', 'edit-sales', 'pay-installments',
                'view-finance',
                'view-employees', 'create-employees', 'edit-employees',
                'view-branches',
                'view-sms', 'send-sms',
                'view-settings',
                'view-reports', 'export-reports',
            ]);
        }

        // Sale Manager role - focused on sales operations
        $saleManagerRole = Role::firstOrCreate(['name' => 'sale_manager']);
        if ($saleManagerRole->permissions()->count() === 0) {
            $saleManagerRole->givePermissionTo([
                'view-products',
                'view-customers', 'create-customers', 'edit-customers',
                'view-sales', 'create-sales', 'edit-sales', 'pay-installments',
                'view-finance',
                'view-sms',
                'view-reports',
            ]);
        }

        // Assign admin role to the test user
        $adminUser = \App\Models\User::where('email', 'admin@admin.com')->first();
        if ($adminUser && !$adminUser->hasRole('admin')) {
            $adminUser->assignRole('admin');
            $this->command->info('Admin role assigned to admin@admin.com');
        }

        $this->command->info('Roles and permissions created successfully!');
    }
}