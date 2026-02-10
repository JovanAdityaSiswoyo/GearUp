<?php

namespace Database\Seeders;

use App\Enums\ItemStatus;
use App\Enums\OrderStatus;
use App\Models\Book;
use App\Models\BookProduct;
use App\Models\Courier;
use App\Models\Package;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserReturnDummySeeder extends Seeder
{
    public function run(): void
    {
        $user = User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('password'),
            ]
        );

        $courier = Courier::first() ?? Courier::factory()->create();

        $products = Product::query()->take(3)->get();
        if ($products->count() < 3) {
            $products = $products->concat(Product::factory(3 - $products->count())->create());
        }

        $package = Package::first() ?? Package::factory()->create();

        $now = now();

        $deliveredProduct = BookProduct::create([
            'id_user' => $user->id,
            'id_product' => $products[0]->id,
            'book_code' => 'RET-DELIVERED-001',
            'status' => 'active',
            'item_status' => ItemStatus::DEPLOYED,
            'order_status' => OrderStatus::DELIVERED,
            'id_courier' => $courier->id,
            'delivery_at' => $now->copy()->subDays(2),
            'checkin_appointment_start' => $now->copy()->subDays(5),
            'checkout_appointment_end' => $now->copy()->addDays(2),
            'amount' => 1,
            'booker_name' => 'Test User',
            'booker_email' => $user->email,
            'booker_telp' => '081234567890',
        ]);

        $deliveredProduct->detailBookProduct()->create([
            'full_name' => 'Test User',
            'phone_number' => '081234567890',
            'emergency_phone_number' => '081234567891',
            'shipping_method' => 'delivery',
            'renter_address' => 'Jl. Contoh No. 123, Jakarta',
            'shipping_date' => $now->copy()->subDays(4),
            'rental_start_at' => $now->copy()->subDays(4),
            'rental_end_at' => $now->copy()->addDays(2),
            'identity_document_path' => 'identity_docs/dummy-1.jpg',
        ]);

        $pickupScheduledProduct = BookProduct::create([
            'id_user' => $user->id,
            'id_product' => $products[1]->id,
            'book_code' => 'RET-PICKUP-001',
            'status' => 'active',
            'item_status' => ItemStatus::DEPLOYED,
            'order_status' => OrderStatus::PICKUP_SCHEDULED,
            'id_courier' => $courier->id,
            'delivery_at' => $now->copy()->subDays(5),
            'checkin_appointment_start' => $now->copy()->subDays(10),
            'checkout_appointment_end' => $now->copy()->subDays(2),
            'amount' => 1,
            'booker_name' => 'Test User',
            'booker_email' => $user->email,
            'booker_telp' => '081234567892',
        ]);

        $pickupScheduledProduct->detailBookProduct()->create([
            'full_name' => 'Test User',
            'phone_number' => '081234567892',
            'emergency_phone_number' => '081234567893',
            'shipping_method' => 'pickup',
            'renter_address' => 'Jl. Contoh No. 123, Jakarta',
            'shipping_date' => $now->copy()->subDays(9),
            'rental_start_at' => $now->copy()->subDays(9),
            'rental_end_at' => $now->copy()->subDays(2),
            'identity_document_path' => 'identity_docs/dummy-2.jpg',
        ]);

        $onProcessProduct = BookProduct::create([
            'id_user' => $user->id,
            'id_product' => $products[2]->id,
            'book_code' => 'RET-RETURNING-001',
            'status' => 'active',
            'item_status' => ItemStatus::RETURNING,
            'order_status' => OrderStatus::ON_PROCESS_RETURN,
            'id_courier' => $courier->id,
            'delivery_at' => $now->copy()->subDays(6),
            'checkin_appointment_start' => $now->copy()->subDays(12),
            'checkout_appointment_end' => $now->copy()->subDays(3),
            'amount' => 1,
            'booker_name' => 'Test User',
            'booker_email' => $user->email,
            'booker_telp' => '081234567894',
        ]);

        $onProcessProduct->detailBookProduct()->create([
            'full_name' => 'Test User',
            'phone_number' => '081234567894',
            'emergency_phone_number' => '081234567895',
            'shipping_method' => 'delivery',
            'renter_address' => 'Jl. Contoh No. 123, Jakarta',
            'shipping_date' => $now->copy()->subDays(11),
            'rental_start_at' => $now->copy()->subDays(11),
            'rental_end_at' => $now->copy()->subDays(3),
            'identity_document_path' => 'identity_docs/dummy-3.jpg',
        ]);

        $completedProduct = BookProduct::create([
            'id_user' => $user->id,
            'id_product' => $products[0]->id,
            'book_code' => 'RET-COMPLETE-001',
            'status' => 'completed',
            'item_status' => ItemStatus::AVAILABLE,
            'order_status' => OrderStatus::COMPLETED,
            'id_courier' => $courier->id,
            'delivery_at' => $now->copy()->subDays(15),
            'returned_at' => $now->copy()->subDays(1),
            'checkin_appointment_start' => $now->copy()->subDays(20),
            'checkout_appointment_end' => $now->copy()->subDays(10),
            'amount' => 1,
            'booker_name' => 'Test User',
            'booker_email' => $user->email,
            'booker_telp' => '081234567896',
        ]);

        $completedProduct->detailBookProduct()->create([
            'full_name' => 'Test User',
            'phone_number' => '081234567896',
            'emergency_phone_number' => '081234567897',
            'shipping_method' => 'pickup',
            'renter_address' => 'Jl. Contoh No. 123, Jakarta',
            'shipping_date' => $now->copy()->subDays(19),
            'rental_start_at' => $now->copy()->subDays(19),
            'rental_end_at' => $now->copy()->subDays(10),
            'identity_document_path' => 'identity_docs/dummy-4.jpg',
        ]);

        $pendingReviewPackage = Book::create([
            'id_package' => $package->id,
            'id_user' => $user->id,
            'book_code' => 'RET-PENDING-001',
            'status' => 'active',
            'item_status' => ItemStatus::IN_INSPECTION,
            'order_status' => OrderStatus::PENDING_REVIEW,
            'id_courier' => $courier->id,
            'delivery_at' => $now->copy()->subDays(7),
            'returned_at' => $now->copy()->subHours(6),
            'checkin_appointment_start' => $now->copy()->subDays(14),
            'checkout_appointment_end' => $now->copy()->subDays(5),
            'amount' => 1,
            'booker_name' => 'Test User',
            'booker_email' => $user->email,
            'booker_telp' => '081234567898',
        ]);

        $pendingReviewPackage->detailBooks()->create([
            'full_name' => 'Test User',
            'phone_number' => '081234567898',
            'emergency_phone_number' => '081234567899',
            'shipping_method' => 'delivery',
            'renter_address' => 'Jl. Contoh No. 123, Jakarta',
            'shipping_date' => $now->copy()->subDays(13),
            'rental_start_at' => $now->copy()->subDays(13),
            'rental_end_at' => $now->copy()->subDays(5),
            'identity_document_path' => 'identity_docs/dummy-5.jpg',
        ]);

        $issuePackage = Book::create([
            'id_package' => $package->id,
            'id_user' => $user->id,
            'book_code' => 'RET-ISSUE-001',
            'status' => 'completed',
            'item_status' => ItemStatus::IN_INSPECTION,
            'order_status' => OrderStatus::ISSUE_DETECTED,
            'id_courier' => $courier->id,
            'delivery_at' => $now->copy()->subDays(18),
            'returned_at' => $now->copy()->subDays(4),
            'checkin_appointment_start' => $now->copy()->subDays(22),
            'checkout_appointment_end' => $now->copy()->subDays(12),
            'amount' => 1,
            'booker_name' => 'Test User',
            'booker_email' => $user->email,
            'booker_telp' => '081234567800',
        ]);

        $issuePackage->detailBooks()->create([
            'full_name' => 'Test User',
            'phone_number' => '081234567800',
            'emergency_phone_number' => '081234567801',
            'shipping_method' => 'pickup',
            'renter_address' => 'Jl. Contoh No. 123, Jakarta',
            'shipping_date' => $now->copy()->subDays(21),
            'rental_start_at' => $now->copy()->subDays(21),
            'rental_end_at' => $now->copy()->subDays(12),
            'identity_document_path' => 'identity_docs/dummy-6.jpg',
        ]);
    }
}
