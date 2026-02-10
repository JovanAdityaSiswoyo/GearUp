# Quick Reference - Critical Check System

## 🔒 1. Transition Gate

**Mencegah status transition yang tidak valid**

### Command Check
```php
$transitionService->isValidTransition($from, $to); // true/false
$transitionService->getValidNextStatuses($currentStatus); // array of allowed statuses
```

### Critical Rule
```
RETURNING → IN_INSPECTION (WAJIB)
RETURNING ✗ AVAILABLE (FORBIDDEN)
```

### Error Message
```
"Invalid status transition: Cannot change from 'Returning' to 'Available'. 
Valid transitions: In-Inspection"
```

---

## ⏱️ 2. Courier Handover

**Auto-record timestamp & courier ID**

### Tracked Timestamps
- `picked_up_at` - Saat courier ambil barang
- `return_started_at` - Saat mulai returning
- `inspection_started_at` - Saat mulai inspection
- `overdue_since` - Saat mulai overdue

### Auto-Recorded When
```php
Status → PICKED_UP: picked_up_at + id_courier
Status → DEPLOYED: delivery_at
Status → RETURNING: return_started_at
Status → IN_INSPECTION: inspection_started_at
```

---

## 📅 3. Automatic Overdue

### Manual Check
```bash
php artisan deployment:check-overdue
```

### Auto Schedule (Hourly)
```bash
# Start scheduler (development)
php artisan schedule:work

# Production (crontab)
* * * * * php artisan schedule:run
```

### Logic
```sql
WHERE item_status = 'Deployed'
  AND order_status != 'Overdue'
  AND checkout_appointment_end < NOW()
```

### Actions
1. ✅ Update order_status → OVERDUE
2. ✅ Set overdue_since timestamp
3. ✅ Log notification for officers
4. ⏳ TODO: Send email/SMS notification

---

## 🧪 Quick Test

### Test 1: Valid Transition
```php
$booking->item_status = ItemStatus::RETURNING;
$transitionService->transitionItemStatus($booking, ItemStatus::IN_INSPECTION);
// ✓ Success
```

### Test 2: Invalid Transition (should fail)
```php
$booking->item_status = ItemStatus::RETURNING;
$transitionService->transitionItemStatus($booking, ItemStatus::AVAILABLE);
// ✗ Exception thrown
```

### Test 3: Overdue Check
```bash
# Run command
php artisan deployment:check-overdue

# Check logs
tail -f storage/logs/laravel.log | grep OVERDUE
```

---

## 📊 Database Queries

### Check Handover Timestamps
```sql
SELECT book_code, 
       picked_up_at, 
       delivery_at, 
       return_started_at,
       inspection_started_at
FROM book_products 
WHERE id_courier IS NOT NULL
ORDER BY picked_up_at DESC;
```

### Find Overdue Items
```sql
SELECT * FROM book_products 
WHERE order_status = 'Overdue'
ORDER BY overdue_since DESC;
```

### Courier Accountability
```sql
SELECT c.nama AS courier_name,
       COUNT(*) AS total_deliveries,
       bp.picked_up_at,
       bp.delivery_at,
       TIMESTAMPDIFF(HOUR, bp.picked_up_at, bp.delivery_at) AS delivery_hours
FROM book_products bp
JOIN couriers c ON bp.id_courier = c.id
WHERE bp.picked_up_at IS NOT NULL
GROUP BY c.id, bp.id;
```

---

## 🚨 Common Issues

### Scheduler Not Running
```bash
# Check if running
ps aux | grep schedule

# Start manually
php artisan schedule:work
```

### Column Not Found
```bash
# Run migrations
php artisan migrate

# Check columns
php artisan db:table book_products
```

### Transition Validation Failed
- Check current status
- Review VALID_TRANSITIONS in ItemStatusTransitionService
- Ensure proper sequence followed

---

## 📱 Integration Points

### Controller Usage
```php
use App\Services\ItemStatusTransitionService;

public function __construct(ItemStatusTransitionService $service)
{
    $this->transitionService = $service;
}

public function updateStatus($id, $newStatus)
{
    $booking = BookProduct::findOrFail($id);
    
    try {
        $this->transitionService->transitionItemStatus(
            $booking,
            $newStatus,
            ['courier_id' => auth()->id()]
        );
        
        return response()->json(['success' => true]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 422);
    }
}
```

### Blade View
```blade
@if($booking->picked_up_at)
    <p>Diambil oleh: {{ $booking->courier->nama }}</p>
    <p>Waktu: {{ $booking->picked_up_at->format('d M Y H:i') }}</p>
@endif

@if($booking->order_status === OrderStatus::OVERDUE)
    <span class="badge badge-danger">
        Overdue sejak {{ $booking->overdue_since->diffForHumans() }}
    </span>
@endif
```

---

## 🎯 Key Benefits

✅ **Prevent Data Corruption** - Invalid transitions blocked
✅ **Quality Assurance** - Forced inspection after use
✅ **Full Accountability** - Know who handled what & when
✅ **Proactive Alerts** - Auto-detect & notify overdue
✅ **Complete Audit Trail** - Timestamp every handover

---

## 📞 Quick Commands

```bash
# Check overdue
php artisan deployment:check-overdue

# Start scheduler
php artisan schedule:work

# List scheduled tasks
php artisan schedule:list

# Test transition service
php artisan tinker
> $service = app(\App\Services\ItemStatusTransitionService::class);
> $service->getValidNextStatuses(\App\Enums\ItemStatus::RETURNING);

# Check logs
tail -f storage/logs/laravel.log
```
