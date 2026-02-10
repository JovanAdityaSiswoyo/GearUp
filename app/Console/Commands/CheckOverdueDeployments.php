<?php

namespace App\Console\Commands;

use App\Enums\ItemStatus;
use App\Enums\OrderStatus;
use App\Models\BookProduct;
use App\Models\Book;
use App\Notifications\OverdueDeploymentNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class CheckOverdueDeployments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deployment:check-overdue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for overdue deployments and automatically update status to Overdue';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for overdue deployments...');

        $now = now();
        $overdueCount = 0;

        // Check BookProducts
        $overdueProducts = BookProduct::where('item_status', ItemStatus::DEPLOYED)
            ->where('order_status', '!=', OrderStatus::OVERDUE)
            ->where('checkout_appointment_end', '<', $now)
            ->get();

        foreach ($overdueProducts as $booking) {
            $this->warn("Overdue found: BookProduct #{$booking->id} - {$booking->book_code}");
            
            // Update status to Overdue
            $booking->order_status = OrderStatus::OVERDUE;
            $booking->overdue_since = $now;
            $booking->save();

            // Send notification to officer
            $this->notifyOfficers($booking);
            
            $overdueCount++;
        }

        // Check Books (Packages)
        $overdueBooks = Book::where('item_status', ItemStatus::DEPLOYED)
            ->where('order_status', '!=', OrderStatus::OVERDUE)
            ->where('checkout_appointment_end', '<', $now)
            ->get();

        foreach ($overdueBooks as $booking) {
            $this->warn("Overdue found: Book #{$booking->id} - {$booking->book_code}");
            
            // Update status to Overdue
            $booking->order_status = OrderStatus::OVERDUE;
            $booking->overdue_since = $now;
            $booking->save();

            // Send notification to officer
            $this->notifyOfficers($booking);
            
            $overdueCount++;
        }

        if ($overdueCount > 0) {
            $this->info("✓ Found and processed {$overdueCount} overdue deployment(s)");
        } else {
            $this->info('✓ No overdue deployments found');
        }

        return Command::SUCCESS;
    }

    /**
     * Send notification to officers about overdue deployment
     */
    private function notifyOfficers($booking)
    {
        // Get all officers (you can adjust the query based on your needs)
        $officers = \App\Models\Officer::all();

        foreach ($officers as $officer) {
            try {
                // You can use email, database, or other notification channels
                // For now, we'll log it (you can implement actual notification later)
                \Log::warning("OVERDUE ALERT: Booking {$booking->book_code} is overdue. Officer {$officer->nama} should follow up.");
                
                // Example: If you have notification system
                // $officer->notify(new OverdueDeploymentNotification($booking));
            } catch (\Exception $e) {
                $this->error("Failed to notify officer {$officer->nama}: {$e->getMessage()}");
            }
        }
    }
}

