<?php

namespace Database\Seeders;

use App\Models\Courier;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class CourierSeeder extends Seeder
{
    public function run(): void
    {
        // Create courier role if not exists
        $courierRole = Role::firstOrCreate(['name' => 'courier', 'guard_name' => 'courier']);
        
        // Create sample couriers with demo data
        $couriers = [
            [
                'name' => 'Ade Kurir',
                'email' => 'ade.kurir@aplikasipinjam.com',
                'password' => bcrypt('password123'),
                'phone' => '08123456789',
                'address' => 'Jl. Kurir No. 1, Jakarta',
            ],
            [
                'name' => 'Budi Pengiriman',
                'email' => 'budi.pengiriman@aplikasipinjam.com',
                'password' => bcrypt('password123'),
                'phone' => '08234567890',
                'address' => 'Jl. Pengiriman No. 2, Jakarta',
            ],
            [
                'name' => 'Citra Antar',
                'email' => 'citra.antar@aplikasipinjam.com',
                'password' => bcrypt('password123'),
                'phone' => '08345678901',
                'address' => 'Jl. Antar No. 3, Jakarta',
            ],
        ];

        foreach ($couriers as $courierData) {
            $courier = Courier::firstOrCreate(
                ['email' => $courierData['email']],
                $courierData
            );
            $courier->assignRole($courierRole);
        }

        $this->command->info('Courier seeder completed successfully!');
    }
}
