<?php

namespace Database\Seeders;

use App\Models\Officer;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class OfficerSeeder extends Seeder
{
    public function run(): void
    {
        // Create officer role if not exists
        $officerRole = Role::firstOrCreate(['name' => 'officer', 'guard_name' => 'officer']);
        
        // Create officers and assign role
        Officer::factory(5)->create()->each(function ($officer) use ($officerRole) {
            $officer->assignRole($officerRole);
        });
    }
}
