<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()['cache']->forget('spatie.permission.cache');

        // Create permissions for each guard
        $guards = ['web', 'admin', 'officer'];
        
        foreach ($guards as $guard) {
            $this->createPermissionsForGuard($guard);
            $this->createRolesForGuard($guard);
        }
    }

    private function createPermissionsForGuard(string $guard): void
    {
        $permissions = [
            // User Management
            'create-user', 'read-user', 'update-user', 'delete-user', 'list-users',
            // Book Management
            'create-book', 'read-book', 'update-book', 'delete-book', 'list-books',
            // Product Management
            'create-product', 'read-product', 'update-product', 'delete-product', 'list-products',
            // Category Management
            'create-category', 'read-category', 'update-category', 'delete-category', 'list-categories',
            // Package Management
            'create-package', 'read-package', 'update-package', 'delete-package', 'list-packages',
            // Payment Management
            'create-payment', 'read-payment', 'update-payment', 'delete-payment', 'list-payments', 'approve-payment',
            // Loan Management
            'create-loan', 'read-loan', 'update-loan', 'delete-loan', 'list-loans', 'approve-loan', 'reject-loan',
            // Report Access
            'view-reports', 'export-reports',
            // System Management
            'manage-roles', 'manage-permissions', 'view-dashboard', 'view-analytics',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => $guard]);
        }
    }

    private function createRolesForGuard(string $guard): void
    {
        // Super Admin Role
        $superAdmin = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => $guard]);
        $superAdmin->givePermissionTo(Permission::where('guard_name', $guard)->get());

        // Admin Role
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => $guard]);
        $adminPerms = [
            'read-user', 'update-user', 'list-users',
            'read-book', 'update-book', 'list-books',
            'read-product', 'update-product', 'list-products',
            'read-category', 'update-category', 'list-categories',
            'read-package', 'update-package', 'list-packages',
            'read-payment', 'list-payments',
            'read-loan', 'list-loans',
            'view-reports', 'export-reports', 'view-dashboard', 'view-analytics',
        ];
        foreach ($adminPerms as $perm) {
            $permission = Permission::where('name', $perm)->where('guard_name', $guard)->first();
            if ($permission) {
                $admin->givePermissionTo($permission);
            }
        }

        // Officer Role
        $officer = Role::firstOrCreate(['name' => 'officer', 'guard_name' => $guard]);
        $officerPerms = [
            'read-user', 'list-users',
            'read-book', 'list-books',
            'read-product', 'list-products',
            'read-category', 'list-categories',
            'read-package', 'list-packages',
            'create-payment', 'read-payment', 'list-payments',
            'create-loan', 'read-loan', 'list-loans', 'approve-loan', 'reject-loan',
            'view-reports', 'view-dashboard',
        ];
        foreach ($officerPerms as $perm) {
            $permission = Permission::where('name', $perm)->where('guard_name', $guard)->first();
            if ($permission) {
                $officer->givePermissionTo($permission);
            }
        }

        // User Role
        $user = Role::firstOrCreate(['name' => 'user', 'guard_name' => $guard]);
        $userPerms = [
            'read-book', 'list-books',
            'read-product', 'list-products',
            'read-category', 'list-categories',
            'read-package', 'list-packages',
            'view-dashboard',
        ];
        foreach ($userPerms as $perm) {
            $permission = Permission::where('name', $perm)->where('guard_name', $guard)->first();
            if ($permission) {
                $user->givePermissionTo($permission);
            }
        }

        $this->command->info("Roles and permissions created for guard: {$guard}");
    }
}
