# Courier Delivery Pages - Quick Reference

## 🎯 Quick Links

### Views
- **Dashboard**: `resources/views/courier/index.blade.php`
- **Management**: `resources/views/courier/delivery-management.blade.php`
- **Detail**: `resources/views/courier/delivery-detail.blade.php`
- **History**: `resources/views/courier/delivery-history.blade.php`

### Controllers
- **Main Controller**: `app/Http/Controllers/CourierDeliveryController.php`
- **Status Changes**: `app/Http/Controllers/BookingStatusController.php` (existing)

### Routes
- **Dashboard**: `GET /courier/dashboard` → `courier.dashboard`
- **Management**: `GET /courier/deliveries` → `courier.deliveries.index`
- **Detail**: `GET /courier/deliveries/{type}/{id}` → `courier.deliveries.show`
- **History**: `GET /courier/deliveries/history` → `courier.deliveries.history`

### Status Endpoints (AJAX)
- **Pickup Delivery**: `POST /book-{type}/{id}/courier/pickup-delivery`
- **Complete Delivery**: `POST /book-{type}/{id}/courier/complete-delivery`
- **Pickup Return**: `POST /book-{type}/{id}/courier/pickup-return`
- **Complete Return**: `POST /book-{type}/{id}/courier/complete-return`

---

## 📊 Status Enum Reference

### OrderStatus untuk Courier
```
READY_FOR_PICKUP      → Barang siap diambil
PICKED_UP_FOR_DELIVERY → Kurir sudah ambil untuk pengiriman
OUT_FOR_DELIVERY      → Dalam perjalanan pengiriman
DELIVERED            → Sudah sampai tujuan

PICKUP_SCHEDULED     → Jadwal ambil kembali
PICKED_UP_FOR_RETURN → Kurir sudah ambil untuk dikembalikan
PENDING_REVIEW       → Dalam proses pengembalian/inspeksi
COMPLETED           → Selesai
ISSUE_DETECTED      → Ada masalah
```

### ItemStatus untuk Courier
```
BOOKED              → Sedang dipinjam
PACKING             → Sedang dikemas
PICKED_UP           → Kurir sudah ambil
DEPLOYED            → Sudah di tangan penerima
RETURNING           → Sedang dalam perjalanan balik
IN_INSPECTION       → Dalam inspeksi
```

---

## 🔍 Common Queries

### Get Courier Active Deliveries
```php
$courier = auth()->user()->courier;

$deliveries = BookProduct::where('id_courier', $courier->id)
    ->whereIn('order_status', [
        OrderStatus::READY_FOR_PICKUP,
        OrderStatus::OUT_FOR_DELIVERY
    ])
    ->latest()
    ->get();
```

### Get Courier Active Returns
```php
$returns = BookProduct::where('id_courier', $courier->id)
    ->whereIn('order_status', [
        OrderStatus::PICKUP_SCHEDULED,
        OrderStatus::ON_PROCESS_RETURN
    ])
    ->latest()
    ->get();
```

### Get Courier History
```php
$history = BookProduct::where('id_courier', $courier->id)
    ->whereIn('order_status', [
        OrderStatus::DELIVERED,
        OrderStatus::PENDING_REVIEW,
        OrderStatus::COMPLETED,
        OrderStatus::ISSUE_DETECTED
    ])
    ->latest()
    ->paginate(15);
```

---

## 🎨 UI Components Usage

### Status Card
```blade
<x-booking-status-card :booking="$booking" />
```
Menampilkan:
- Order status dengan badge
- Item status
- Courier info
- Timeline

### Action Buttons
```blade
<x-booking-status-actions :booking="$booking" type="BookProduct|Book" />
```
Menampilkan:
- Contextual action buttons
- Photo upload dialog
- AJAX handlers

### Courier Navigation
```blade
<x-courier-nav :readyForPickupCount="$count" />
```
Menampilkan:
- Link ke Dashboard
- Link ke Deliveries
- Link ke History

---

## 🔐 Authorization Checks

### Middleware Protection
```php
Route::middleware(['auth:web,courier'])->group(...)
```

### Model Check
```php
if ($booking->id_courier !== auth()->user()->courier?->id) {
    abort(403, 'Unauthorized');
}
```

### Role Check
```php
if (!auth()->user()->hasRole('courier')) {
    abort(403);
}
```

---

## 📸 Photo Upload Integration

### In View
```blade
<button onclick="openPhotoUploadDialog('courierPickupDelivery', {{ $booking->id }}, 'BookProduct')">
    Upload Foto
</button>
```

### In JavaScript
```javascript
async function openPhotoUploadDialog(action, bookingId, type) {
    const result = await Swal.fire({
        title: 'Upload Foto Bukti',
        input: 'file',
        inputAttributes: { accept: 'image/*' },
        showCancelButton: true,
    });
    
    if (result.isConfirmed) {
        // Submit via FormData
        const formData = new FormData();
        formData.append('photo', result.value);
        
        // POST to endpoint
        const response = await fetch(`/booking-status/${type}/${bookingId}/${action}`, {
            method: 'POST',
            body: formData,
        });
    }
}
```

---

## 🚀 Common Operations

### Pickup untuk Delivery
```php
// Controller action
public function courierPickupDelivery($booking)
{
    $this->courierStatusService->pickupForDelivery($booking, $photoUrl);
    return response()->json(['success' => true]);
}
```

### Complete Delivery
```php
// Service
public function completeDelivery($booking, $photoUrl = null)
{
    return $this->bookingStatusService->updateOrderStatus(
        $booking,
        OrderStatus::DELIVERED,
        ['delivery_at' => now(), 'item_status' => ItemStatus::DEPLOYED]
    );
}
```

### Pickup untuk Return
```php
public function pickupForReturn($booking, $photoUrl = null)
{
    return $this->bookingStatusService->updateOrderStatus(
        $booking,
        OrderStatus::PICKED_UP_FOR_RETURN,
        ['item_status' => ItemStatus::RETURNING]
    );
}
```

### Complete Return
```php
public function completeReturn($booking, $photoUrl = null)
{
    return $this->bookingStatusService->updateOrderStatus(
        $booking,
        OrderStatus::PENDING_REVIEW,
        ['returned_at' => now()]
    );
}
```

---

## 📱 Responsive Design Breakpoints

```tailwind
sm: 640px   → 1 column
md: 768px   → 2 columns
lg: 1024px  → 4 columns
```

Stats cards di dashboard:
```html
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
```

---

## 🔗 Related Documentation

- Full Guide: [COURIER_DELIVERY_PAGES_GUIDE.md](COURIER_DELIVERY_PAGES_GUIDE.md)
- Booking Status: [BOOKING_STATUS_GUIDE.md](BOOKING_STATUS_GUIDE.md)
- Status Enums: Lihat `app/Enums/` directory

---

## 💡 Tips & Tricks

### Preload Relations
```php
// Instead of N+1 queries
$bookings = BookProduct::with(['product', 'user', 'courier'])->get();
```

### Cache Statistics
```php
// For better performance
Cache::remember('courier_stats_' . $courier->id, 3600, function() {
    return [
        'readyForPickup' => ...,
        'outForDelivery' => ...,
    ];
});
```

### Eager Loading Timeline
```php
// Pre-load status history
$booking->load('statusHistory');
```

### Filter Helper
```php
// Create scope untuk common filters
public function scopeReadyForDelivery($query, $courierId) {
    return $query->where('id_courier', $courierId)
        ->where('order_status', OrderStatus::READY_FOR_PICKUP);
}

// Usage
$deliveries = BookProduct::readyForDelivery($courier->id)->get();
```

---

## 🐛 Debugging

### Check Courier Assignment
```php
dd($booking->id_courier, auth()->user()->courier->id);
```

### Check Status Value
```php
dd($booking->order_status->value, $booking->order_status->label());
```

### Check Photo URL
```php
dd($booking->photo_delivery, $booking->photo_return);
```

### Check Relations
```php
dd($booking->courier, $booking->product, $booking->user);
```

---

## 🎯 Development Checklist

- [ ] Update `id_courier` field di database
- [ ] Ensure `order_status` enum values match
- [ ] Ensure `item_status` enum values match
- [ ] Test photo upload functionality
- [ ] Test authorization checks
- [ ] Test status transitions
- [ ] Test pagination in history
- [ ] Test filter functionality
- [ ] Test responsive layout
- [ ] Test AJAX endpoints

---

## 📊 Database Verification

```sql
-- Check columns
DESC book_products;

-- Check data
SELECT id, id_courier, order_status, item_status, delivery_at, returned_at 
FROM book_products LIMIT 5;

-- Check courier assignment
SELECT COUNT(*) FROM book_products WHERE id_courier IS NOT NULL;

-- Check status distribution
SELECT order_status, COUNT(*) FROM book_products GROUP BY order_status;
```

---

**Last Updated**: 2024  
**Version**: 1.0  
**Status**: Active
