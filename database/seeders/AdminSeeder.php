<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin role if not exists
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'admin']);
        
        // Create admins and assign role
        Admin::factory(5)->create()->each(function ($admin) use ($adminRole) {
            $admin->assignRole($adminRole);
        });
    }
}
