<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;

class SetupFinancePermissions extends Command
{
    protected $signature = 'finance:setup-permissions';
    protected $description = 'Setup finance management permissions';

    public function handle()
    {
        $this->info('Setting up finance management permissions...');

        // Create permissions
        $permissions = [
            'manage-expenses',
            'manage-stock-credits', 
            'manage-salary-payments',
            'view-finance'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web'
            ]);
            $this->info("Created permission: {$permission}");
        }

        // Create admin role
        $adminRole = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web'
        ]);

        // Assign all permissions to admin role
        $adminRole->syncPermissions($permissions);
        $this->info('Assigned all permissions to admin role');

        // Assign admin role to all existing users
        $users = User::all();
        foreach ($users as $user) {
            if (!$user->hasRole('admin')) {
                $user->assignRole('admin');
                $this->info("Assigned admin role to user: {$user->name}");
            }
        }

        $this->success('Finance permissions setup completed successfully!');
    }
}