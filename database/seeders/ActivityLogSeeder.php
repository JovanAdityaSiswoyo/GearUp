<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\Admin;
use App\Models\Officer;
use App\Models\User;
use App\Models\Book;
use App\Models\Product;
use App\Models\Payment;
use Illuminate\Database\Seeder;

class ActivityLogSeeder extends Seeder
{
    public function run(): void
    {
        $admins = Admin::all();
        $officers = Officer::all();
        $users = User::all();

        // Sample admin activities
        if ($admins->isNotEmpty()) {
            $admin = $admins->first();
            
            ActivityLog::create([
                'log_name' => 'admin',
                'description' => 'Admin logged into the system',
                'causer_type' => Admin::class,
                'causer_id' => $admin->id,
                'event' => 'login',
                'properties' => ['ip' => '127.0.0.1', 'user_agent' => 'Mozilla/5.0'],
                'created_at' => now()->subDays(5),
            ]);

            ActivityLog::create([
                'log_name' => 'product',
                'description' => 'Admin created a new product',
                'subject_type' => Product::class,
                'subject_id' => Product::first()?->id,
                'causer_type' => Admin::class,
                'causer_id' => $admin->id,
                'event' => 'created',
                'properties' => ['product_name' => 'New Product'],
                'created_at' => now()->subDays(4),
            ]);

            ActivityLog::create([
                'log_name' => 'payment',
                'description' => 'Admin verified a payment',
                'subject_type' => Payment::class,
                'subject_id' => Payment::first()?->id,
                'causer_type' => Admin::class,
                'causer_id' => $admin->id,
                'event' => 'verified',
                'properties' => ['amount' => 50000, 'status' => 'paid'],
                'created_at' => now()->subDays(3),
            ]);
        }

        // Sample officer activities
        if ($officers->isNotEmpty()) {
            $officer = $officers->first();
            
            ActivityLog::create([
                'log_name' => 'officer',
                'description' => 'Officer logged into the system',
                'causer_type' => Officer::class,
                'causer_id' => $officer->id,
                'event' => 'login',
                'properties' => ['ip' => '127.0.0.1'],
                'created_at' => now()->subDays(2),
            ]);

            ActivityLog::create([
                'log_name' => 'booking',
                'description' => 'Officer processed a booking',
                'subject_type' => Book::class,
                'subject_id' => Book::first()?->id,
                'causer_type' => Officer::class,
                'causer_id' => $officer->id,
                'event' => 'processed',
                'properties' => ['booking_code' => Book::first()?->book_code],
                'created_at' => now()->subDays(1),
            ]);
        }

        // Sample user activities
        if ($users->isNotEmpty()) {
            $user = $users->first();
            
            ActivityLog::create([
                'log_name' => 'user',
                'description' => 'User registered to the system',
                'causer_type' => User::class,
                'causer_id' => $user->id,
                'event' => 'registered',
                'properties' => ['email' => $user->email],
                'created_at' => now()->subDays(6),
            ]);

            ActivityLog::create([
                'log_name' => 'booking',
                'description' => 'User created a new booking',
                'subject_type' => Book::class,
                'subject_id' => Book::first()?->id,
                'causer_type' => User::class,
                'causer_id' => $user->id,
                'event' => 'created',
                'properties' => ['booking_code' => Book::first()?->book_code],
                'created_at' => now(),
            ]);
        }

        // Add more recent activities
        for ($i = 0; $i < 20; $i++) {
            $events = ['created', 'updated', 'deleted', 'viewed', 'processed'];
            $logNames = ['admin', 'user', 'booking', 'payment', 'product', 'officer'];
            
            ActivityLog::create([
                'log_name' => $logNames[array_rand($logNames)],
                'description' => 'System activity: ' . fake()->sentence(),
                'event' => $events[array_rand($events)],
                'properties' => ['detail' => fake()->text(100)],
                'created_at' => now()->subHours(rand(1, 48)),
            ]);
        }
    }
}
