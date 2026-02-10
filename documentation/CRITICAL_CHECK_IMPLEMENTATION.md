# Critical Check Implementation - Logistik Prevention System

## Overview
Implementasi 3 mekanisme "Critical Check" untuk mencegah kebocoran dalam sistem logistik dan memastikan alur operasional yang benar.

---

## 1. Transition Gate (Status Validation)

### Tujuan
Mencegah transisi status yang tidak valid dan memastikan barang yang kembali dari deployment harus melalui inspeksi.

### Implementasi
**File:** `app/Services/ItemStatusTransitionService.php`

### Aturan Transisi
```php
AVAILABLE → BOOKED
BOOKED → PACKING (atau kembali ke AVAILABLE jika dibatalkan)
PACKING → PICKED_UP
PICKED_UP → DEPLOYED
DEPLOYED → RETURNING
RETURNING → IN_INSPECTION (WAJIB, tidak boleh langsung ke AVAILABLE)
IN_INSPECTION → AVAILABLE | MAINTENANCE | LOST_SCRAPPED
MAINTENANCE → IN_INSPECTION (setelah diperbaiki)
```

### Critical Rule
**RETURNING TIDAK BOLEH langsung ke AVAILABLE**
- Harus lewat `IN_INSPECTION` terlebih dahulu
- Mencegah user berikutnya mendapat barang yang kotor/rusak
- System akan throw Exception jika ada attempt untuk skip inspection

### Usage Example
```php
use App\Services\ItemStatusTransitionService;

$transitionService = app(ItemStatusTransitionService::class);

try {
    // Valid transition
    $transitionService->transitionItemStatus(
        $booking,
        ItemStatus::IN_INSPECTION
    );
    
    // ✓ Success: Transition from RETURNING to IN_INSPECTION allowed
} catch (Exception $e) {
    // ✗ Error: "Cannot change from 'Returning' to 'Available'"
    // Must go through In-Inspection first
}
```

### Validation Methods
- `isValidTransition($from, $to)` - Check if transition is allowed
- `getValidNextStatuses($currentStatus)` - Get list of valid next statuses
- `transitionItemStatus($booking, $newStatus, $additionalData)` - Execute transition with validation

---

## 2. Courier Handover (Timestamp & Accountability)

### Tujuan
Mencatat waktu dan ID courier saat barang dipindahkan ke status PICKED_UP untuk akuntabilitas.

### Database Columns Added
**Tables:** `book_products`, `books`

```sql
- picked_up_at (timestamp) - Waktu barang diambil courier
- return_started_at (timestamp) - Waktu mulai pengembalian
- inspection_started_at (timestamp) - Waktu mulai inspeksi
- overdue_since (timestamp) - Waktu mulai overdue
```

### Automatic Recording
Saat status berubah ke `PICKED_UP`:
- `picked_up_at` otomatis di-set ke `now()`
- `id_courier` dicatat (jika belum ada)
- Timestamp ini digunakan untuk tracking dan accountability

### Implementation in Controller
```php
public function pickupDelivery($id)
{
    $booking = BookProduct::findOrFail($id);
    
    // Transition dengan automatic timestamp recording
    $this->transitionService->transitionItemStatus(
        $booking,
        ItemStatus::PICKED_UP,
        ['courier_id' => auth()->user()->courier->id]
    );
    
    // picked_up_at dan id_courier otomatis tercatat
}
```

### Benefit
- **Accountability**: Tahu siapa yang bertanggung jawab jika barang hilang
- **Timeline Tracking**: Bisa analisa berapa lama setiap fase
- **Audit Trail**: Bukti handover untuk dispute resolution

---

## 3. Automatic Overdue (Scheduled Check)

### Tujuan
Otomatis mendeteksi dan mengubah status order menjadi OVERDUE jika waktu sewa sudah habis.

### Command
**File:** `app/Console/Commands/CheckOverdueDeployments.php`
**Signature:** `deployment:check-overdue`

### Logic
1. Cari semua booking dengan:
   - `item_status = DEPLOYED`
   - `order_status != OVERDUE`
   - `checkout_appointment_end < NOW()`

2. Untuk setiap overdue booking:
   - Update `order_status` ke `OVERDUE`
   - Set `overdue_since` ke waktu sekarang
   - Send notification ke Officer untuk follow-up

### Schedule
**File:** `routes/console.php`

```php
// Runs every hour
Schedule::command('deployment:check-overdue')
    ->hourly()
    ->withoutOverlapping()
    ->description('Check for overdue deployments');

// Alternative: Run more frequently during business hours
Schedule::command('deployment:check-overdue')
    ->everyThirtyMinutes()
    ->between('8:00', '20:00')
    ->weekdays();
```

### Running the Command

**Manual Execution:**
```bash
php artisan deployment:check-overdue
```

**Start Laravel Scheduler (Required for automatic execution):**
```bash
# Windows (run continuously)
php artisan schedule:work

# Or add to Windows Task Scheduler
php artisan schedule:run
```

**Production Setup (Linux):**
```bash
# Add to crontab
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

### Notification System
Current: Logs to file
Future: Can implement email/SMS notifications

```php
// In CheckOverdueDeployments.php
private function notifyOfficers($booking)
{
    $officers = Officer::all();
    
    foreach ($officers as $officer) {
        // Log untuk sekarang
        \Log::warning("OVERDUE ALERT: {$booking->book_code}");
        
        // TODO: Implement actual notification
        // $officer->notify(new OverdueDeploymentNotification($booking));
    }
}
```

---

## Testing Guide

### 1. Test Transition Gate
```php
// Test valid transition
$booking->item_status = ItemStatus::RETURNING;
$transitionService->transitionItemStatus($booking, ItemStatus::IN_INSPECTION);
// ✓ Should succeed

// Test invalid transition (should throw exception)
$booking->item_status = ItemStatus::RETURNING;
$transitionService->transitionItemStatus($booking, ItemStatus::AVAILABLE);
// ✗ Should throw: "Cannot change from 'Returning' to 'Available'"
```

### 2. Test Courier Handover
```php
// Pickup delivery
$response = $this->postJson("/book-products/{$id}/courier/pickup-delivery", [
    'photo' => $uploadedFile
]);

// Check timestamps recorded
$booking->refresh();
assert($booking->picked_up_at !== null);
assert($booking->id_courier === $courier->id);
```

### 3. Test Automatic Overdue
```php
// Create overdue booking
$booking = BookProduct::create([
    'item_status' => ItemStatus::DEPLOYED,
    'order_status' => OrderStatus::DELIVERED,
    'checkout_appointment_end' => now()->subDays(1), // Yesterday
]);

// Run command
Artisan::call('deployment:check-overdue');

// Verify status changed
$booking->refresh();
assert($booking->order_status === OrderStatus::OVERDUE);
assert($booking->overdue_since !== null);
```

---

## Key Files Modified/Created

### New Files
1. `app/Services/ItemStatusTransitionService.php` - Status transition validation
2. `app/Console/Commands/CheckOverdueDeployments.php` - Overdue checker
3. `database/migrations/*_add_tracking_timestamps_to_bookings_tables.php`

### Modified Files
1. `app/Enums/OrderStatus.php` - Added OVERDUE status
2. `app/Http/Controllers/CourierDeliveryController.php` - Integrated transition service
3. `routes/console.php` - Scheduled task registration

---

## Benefits

### ✅ Data Integrity
- Prevent invalid status transitions
- Ensure proper workflow sequence

### ✅ Quality Control
- Force inspection after deployment
- Prevent dirty/damaged items going to next user

### ✅ Accountability
- Track who handled each item
- Timestamp every handover

### ✅ Proactive Management
- Auto-detect overdue deployments
- Alert officers for follow-up

### ✅ Audit Trail
- Complete timeline of item movement
- Evidence for dispute resolution

---

## Next Steps / Enhancements

1. **Notification System**
   - Implement email notifications for overdue
   - SMS alerts for critical issues
   - Dashboard notifications for officers

2. **Advanced Analytics**
   - Average time per status phase
   - Courier performance metrics
   - Overdue frequency analysis

3. **Automated Actions**
   - Auto-escalate overdue after X days
   - Auto-charge late fees
   - Auto-schedule return pickups

4. **Mobile App Integration**
   - Push notifications to couriers
   - Real-time status updates
   - Photo upload from mobile

---

## Monitoring & Logs

### Check Scheduler Logs
```bash
tail -f storage/logs/laravel.log | grep OVERDUE
```

### View Command Output
```bash
php artisan deployment:check-overdue -v
```

### Database Queries
```sql
-- Check overdue bookings
SELECT * FROM book_products 
WHERE order_status = 'Overdue' 
ORDER BY overdue_since DESC;

-- Check timestamp tracking
SELECT book_code, picked_up_at, delivery_at, return_started_at, overdue_since
FROM book_products 
WHERE picked_up_at IS NOT NULL;
```

---

## Support

Jika ada pertanyaan atau issue:
1. Check logs: `storage/logs/laravel.log`
2. Test command manually: `php artisan deployment:check-overdue`
3. Verify database columns exist
4. Ensure scheduler is running: `php artisan schedule:work`
