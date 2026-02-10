<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BookProduct;
use App\Models\Book;
use App\Models\Courier;
use App\Enums\ItemStatus;
use App\Enums\OrderStatus;

/**
 * Seeder untuk testing booking status workflow
 */
class BookingStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get a courier untuk di-assign
        $courier = Courier::first();

        // 1. Awaiting Validation status (user baru booking, menunggu validasi)
        BookProduct::create([
            'id_user' => \App\Models\User::where('email', 'user@example.com')->first()->id ?? null,
            'id_product' => \App\Models\Product::first()->id ?? null,
            'book_code' => 'BP-VALIDATE-001',
            'status' => 'awaiting_validation',
            'item_status' => ItemStatus::AVAILABLE,
            'order_status' => OrderStatus::AWAITING_VALIDATION,
            'checkin_appointment_start' => now()->addDay(),
            'checkout_appointment_end' => now()->addDays(7),
            'amount' => 1,
            'booker_name' => 'Jane Doe',
            'booker_email' => 'jane@example.com',
            'booker_telp' => '08123456790',
        ]);

        // 3. Confirmed status
        BookProduct::create([
            'id_user' => \App\Models\User::where('email', 'user@example.com')->first()->id ?? null,
            'id_product' => \App\Models\Product::skip(1)->first()->id ?? null,
            'book_code' => 'BP-CONFIRM-001',
            'status' => 'confirmed',
            'item_status' => ItemStatus::BOOKED,
            'order_status' => OrderStatus::CONFIRMED,
            'checkin_appointment_start' => now()->addDay(),
            'checkout_appointment_end' => now()->addDays(7),
            'amount' => 2,
            'booker_name' => 'Bob Smith',
            'booker_email' => 'bob@example.com',
            'booker_telp' => '08123456791',
        ]);

        // 4. Ready for Pickup
        BookProduct::create([
            'id_user' => \App\Models\User::where('email', 'user@example.com')->first()->id ?? null,
            'id_product' => \App\Models\Product::skip(2)->first()->id ?? null,
            'book_code' => 'BP-PICKUP-001',
            'status' => 'ready_for_pickup',
            'item_status' => ItemStatus::PACKING,
            'order_status' => OrderStatus::READY_FOR_PICKUP,
            'id_courier' => $courier?->id,
            'checkin_appointment_start' => now()->addDay(),
            'checkout_appointment_end' => now()->addDays(7),
            'amount' => 1,
            'booker_name' => 'Alice Johnson',
            'booker_email' => 'alice@example.com',
            'booker_telp' => '08123456792',
        ]);

        // 5. Out for Delivery
        BookProduct::create([
            'id_user' => \App\Models\User::where('email', 'user@example.com')->first()->id ?? null,
            'id_product' => \App\Models\Product::skip(3)->first()->id ?? null,
            'book_code' => 'BP-DELIVERY-001',
            'status' => 'out_for_delivery',
            'item_status' => ItemStatus::PICKED_UP,
            'order_status' => OrderStatus::OUT_FOR_DELIVERY,
            'id_courier' => $courier?->id,
            'delivery_at' => now(),
            'checkin_appointment_start' => now()->addDay(),
            'checkout_appointment_end' => now()->addDays(7),
            'amount' => 1,
            'booker_name' => 'Charlie Brown',
            'booker_email' => 'charlie@example.com',
            'booker_telp' => '08123456793',
        ]);

        // 6. Delivered
        BookProduct::create([
            'id_user' => \App\Models\User::where('email', 'user@example.com')->first()->id ?? null,
            'id_product' => \App\Models\Product::skip(4)->first()->id ?? null,
            'book_code' => 'BP-DELIVERED-001',
            'status' => 'delivered',
            'item_status' => ItemStatus::DEPLOYED,
            'order_status' => OrderStatus::DELIVERED,
            'id_courier' => $courier?->id,
            'delivery_at' => now()->subHour(),
            'checkin_appointment_start' => now()->addDay(),
            'checkout_appointment_end' => now()->addDays(7),
            'amount' => 1,
            'booker_name' => 'Diana Prince',
            'booker_email' => 'diana@example.com',
            'booker_telp' => '08123456794',
        ]);

        // 7. Pickup Scheduled
        BookProduct::create([
            'id_user' => \App\Models\User::where('email', 'user@example.com')->first()->id ?? null,
            'id_product' => \App\Models\Product::skip(5)->first()->id ?? null,
            'book_code' => 'BP-RETURN-001',
            'status' => 'pickup_scheduled',
            'item_status' => ItemStatus::DEPLOYED,
            'order_status' => OrderStatus::PICKUP_SCHEDULED,
            'id_courier' => $courier?->id,
            'delivery_at' => now()->subDay(),
            'checkin_appointment_start' => now()->subDays(7),
            'checkout_appointment_end' => now(),
            'amount' => 1,
            'booker_name' => 'Eve Wilson',
            'booker_email' => 'eve@example.com',
            'booker_telp' => '08123456795',
        ]);

        // 8. On Process Return
        BookProduct::create([
            'id_user' => \App\Models\User::where('email', 'user@example.com')->first()->id ?? null,
            'id_product' => \App\Models\Product::skip(6)->first()->id ?? null,
            'book_code' => 'BP-RETURNING-001',
            'status' => 'on_process_return',
            'item_status' => ItemStatus::RETURNING,
            'order_status' => OrderStatus::ON_PROCESS_RETURN,
            'id_courier' => $courier?->id,
            'delivery_at' => now()->subDay(),
            'checkin_appointment_start' => now()->subDays(8),
            'checkout_appointment_end' => now()->subDay(),
            'amount' => 1,
            'booker_name' => 'Frank Miller',
            'booker_email' => 'frank@example.com',
            'booker_telp' => '08123456796',
        ]);

        // 9. Pending Review
        BookProduct::create([
            'id_user' => \App\Models\User::where('email', 'user@example.com')->first()->id ?? null,
            'id_product' => \App\Models\Product::skip(7)->first()->id ?? null,
            'book_code' => 'BP-REVIEW-001',
            'status' => 'pending_review',
            'item_status' => ItemStatus::IN_INSPECTION,
            'order_status' => OrderStatus::PENDING_REVIEW,
            'id_courier' => $courier?->id,
            'delivery_at' => now()->subDay(),
            'returned_at' => now(),
            'checkin_appointment_start' => now()->subDays(9),
            'checkout_appointment_end' => now()->subDay(),
            'amount' => 1,
            'booker_name' => 'Grace Lee',
            'booker_email' => 'grace@example.com',
            'booker_telp' => '08123456797',
        ]);

        // 10. Completed
        BookProduct::create([
            'id_user' => \App\Models\User::where('email', 'user@example.com')->first()->id ?? null,
            'id_product' => \App\Models\Product::skip(8)->first()->id ?? null,
            'book_code' => 'BP-COMPLETE-001',
            'status' => 'completed',
            'item_status' => ItemStatus::AVAILABLE,
            'order_status' => OrderStatus::COMPLETED,
            'id_courier' => $courier?->id,
            'delivery_at' => now()->subDays(10),
            'returned_at' => now()->subHour(),
            'checkin_appointment_start' => now()->subDays(10),
            'checkout_appointment_end' => now()->subDay(),
            'amount' => 1,
            'booker_name' => 'Henry Davis',
            'booker_email' => 'henry@example.com',
            'booker_telp' => '08123456798',
        ]);

        // 11. Issue Detected
        BookProduct::create([
            'id_user' => \App\Models\User::where('email', 'user@example.com')->first()->id ?? null,
            'id_product' => \App\Models\Product::skip(9)->first()->id ?? null,
            'book_code' => 'BP-ISSUE-001',
            'status' => 'issue_detected',
            'item_status' => ItemStatus::IN_INSPECTION,
            'order_status' => OrderStatus::ISSUE_DETECTED,
            'id_courier' => $courier?->id,
            'delivery_at' => now()->subDays(10),
            'returned_at' => now()->subDays(2),
            'checkin_appointment_start' => now()->subDays(10),
            'checkout_appointment_end' => now()->subDays(3),
            'amount' => 1,
            'booker_name' => 'Ivy Martinez',
            'booker_email' => 'ivy@example.com',
            'booker_telp' => '08123456799',
        ]);

        // 12. Cancelled
        BookProduct::create([
            'id_user' => \App\Models\User::where('email', 'user@example.com')->first()->id ?? null,
            'id_product' => \App\Models\Product::first()->id ?? null,
            'book_code' => 'BP-CANCEL-001',
            'status' => 'cancelled',
            'item_status' => ItemStatus::AVAILABLE,
            'order_status' => OrderStatus::CANCELLED,
            'checkin_appointment_start' => now()->addDays(30),
            'checkout_appointment_end' => now()->addDays(37),
            'amount' => 1,
            'booker_name' => 'Jack Wilson',
            'booker_email' => 'jack@example.com',
            'booker_telp' => '08123456800',
        ]);

        // Seeding untuk books (packages) - contoh beberapa status
        $package = \App\Models\Package::first();

        if ($package) {
            // Draft package booking
            Book::create([
                'id_package' => $package->id,
                'id_user' => \App\Models\User::where('email', 'user@example.com')->first()->id ?? null,
                'book_code' => 'PB-DRAFT-001',
                'status' => 'draft',
                'item_status' => ItemStatus::AVAILABLE,
                'order_status' => OrderStatus::DRAFT,
                'checkin_appointment_start' => now()->addDay(),
                'checkout_appointment_end' => now()->addDays(14),
                'amount' => 1,
                'booker_name' => 'Kevin Johnson',
                'booker_email' => 'kevin@example.com',
                'booker_telp' => '08123456801',
            ]);

            // Confirmed package booking
            Book::create([
                'id_package' => $package->id,
                'id_user' => \App\Models\User::where('email', 'user@example.com')->first()->id ?? null,
                'book_code' => 'PB-CONFIRM-001',
                'status' => 'confirmed',
                'item_status' => ItemStatus::BOOKED,
                'order_status' => OrderStatus::CONFIRMED,
                'checkin_appointment_start' => now()->addDay(),
                'checkout_appointment_end' => now()->addDays(14),
                'amount' => 1,
                'booker_name' => 'Laura Davis',
                'booker_email' => 'laura@example.com',
                'booker_telp' => '08123456802',
            ]);

            // Delivered package booking
            Book::create([
                'id_package' => $package->id,
                'id_user' => \App\Models\User::where('email', 'user@example.com')->first()->id ?? null,
                'book_code' => 'PB-DELIVERED-001',
                'status' => 'delivered',
                'item_status' => ItemStatus::DEPLOYED,
                'order_status' => OrderStatus::DELIVERED,
                'id_courier' => $courier?->id,
                'delivery_at' => now()->subHour(),
                'checkin_appointment_start' => now()->addDay(),
                'checkout_appointment_end' => now()->addDays(14),
                'amount' => 1,
                'booker_name' => 'Michael Brown',
                'booker_email' => 'michael@example.com',
                'booker_telp' => '08123456803',
            ]);

            // Completed package booking
            Book::create([
                'id_package' => $package->id,
                'id_user' => \App\Models\User::where('email', 'user@example.com')->first()->id ?? null,
                'book_code' => 'PB-COMPLETE-001',
                'status' => 'completed',
                'item_status' => ItemStatus::AVAILABLE,
                'order_status' => OrderStatus::COMPLETED,
                'id_courier' => $courier?->id,
                'delivery_at' => now()->subDays(14),
                'returned_at' => now()->subHour(),
                'checkin_appointment_start' => now()->subDays(14),
                'checkout_appointment_end' => now()->subDay(),
                'amount' => 1,
                'booker_name' => 'Nina Garcia',
                'booker_email' => 'nina@example.com',
                'booker_telp' => '08123456804',
            ]);
        }
    }
}
