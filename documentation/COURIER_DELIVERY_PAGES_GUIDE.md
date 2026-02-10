# Courier Delivery Pages Implementation Guide

## Overview

Sistem Courier Delivery Pages adalah implementasi lengkap untuk mengelola pengiriman dan pengembalian barang rental dari perspektif kurir. Sistem ini terdiri dari tiga halaman utama:

1. **Dashboard** - Ringkasan statistik dan akses cepat ke pengiriman aktif
2. **Delivery Management** - Daftar lengkap pengiriman dan pengembalian yang aktif
3. **Delivery Detail** - Detail komprehensif dengan aksi pengiriman/pengembalian
4. **Delivery History** - Riwayat pengiriman yang sudah selesai dengan filter

---

## 1. Dashboard Courier (`resources/views/courier/index.blade.php`)

### Purpose
Halaman landing untuk kurir yang menampilkan ringkasan statistik dan akses cepat ke pengiriman aktif.

### Key Features
- **Stats Cards**: 4 statistik utama
  - Siap Diambil (Ready for Pickup)
  - Dalam Pengiriman (Out for Delivery)
  - Pengembalian Dijadwal (Return Scheduled)
  - Dalam Pengembalian (On Process Return)

- **Quick Actions**: 2 section untuk akses cepat
  - Pengiriman Aktif (Active Deliveries)
  - Pengembalian Aktif (Active Returns)

- **Recent Completed**: Tabel 5 pengiriman terakhir yang selesai

### View Variables Required
```php
[
    'stats' => [
        'readyForPickup' => int,      // Jumlah pengiriman siap diambil
        'outForDelivery' => int,      // Jumlah pengiriman dalam perjalanan
        'returnScheduled' => int,     // Jumlah pengembalian dijadwalkan
        'onProcessReturn' => int,     // Jumlah dalam proses pengembalian
    ],
    'activeDeliveries' => Collection, // 5 pengiriman aktif terakhir
    'activeReturns' => Collection,    // 5 pengembalian aktif terakhir
    'recentCompleted' => Collection,  // 5 pengiriman yang baru selesai
    'readyForPickupCount' => int,    // Total siap diambil untuk nav badge
]
```

### Route
```php
Route::get('/courier/dashboard', [CourierDeliveryController::class, 'index'])
    ->name('courier.dashboard');
```

---

## 2. Delivery Management (`resources/views/courier/delivery-management.blade.php`)

### Purpose
Halaman untuk menampilkan semua pengiriman dan pengembalian aktif yang ditugaskan kepada kurir.

### Key Features
- **Statistics Cards**: 4 kartu statistik di atas
- **Pengiriman Section**: 
  - List semua pengiriman dengan status READY_FOR_PICKUP atau OUT_FOR_DELIVERY
  - Aksi button untuk pickup dan complete delivery dengan foto upload
  
- **Pengembalian Section**: 
  - List semua pengembalian dengan status PICKUP_SCHEDULED atau ON_PROCESS_RETURN
  - Aksi button untuk pickup return dan complete return dengan foto upload

### Interactive Features
- **Photo Upload Dialog**: SweetAlert2 dialog untuk upload foto bukti
  - Foto pengambilan (pickup)
  - Foto pengiriman (delivery)
  - Foto pengambilan kembali (return pickup)
  - Foto pengembalian lengkap (return complete)

- **Status Management**: Tombol aksi untuk mengubah status dengan foto evidence

### View Variables Required
```php
[
    'deliveryBookings' => Collection, // Semua pengiriman aktif
    'returnBookings' => Collection,   // Semua pengembalian aktif
    'readyForPickup' => int,
    'outForDelivery' => int,
    'pickupScheduled' => int,
    'onProcessReturn' => int,
]
```

### Route
```php
Route::get('/courier/deliveries', [CourierDeliveryController::class, 'index'])
    ->name('courier.deliveries.index');
```

---

## 3. Delivery Detail (`resources/views/courier/delivery-detail.blade.php`)

### Purpose
Halaman detail komprehensif untuk satu pengiriman dengan timeline dan aksi status.

### Key Sections

#### Breadcrumb Navigation
- Link kembali ke delivery management
- Menunjukkan path: Courier > Deliveries > [Code]

#### Product Information
- Foto produk
- Nama, brand, kategori produk
- Jumlah yang dipinjam

#### Receiver Information
- Nama, email, nomor telepon penerima
- Jumlah barang yang diterima
- Lokasi pengiriman

#### Rental Schedule
- Tanggal pengambilan dengan jam (format: "dd Mon YYYY HH:mm")
- Tanggal pengembalian dengan jam
- Durasi pinjam

#### Status Timeline
Visual timeline yang menunjukkan:
1. Booking Dibuat (created_at)
2. Dikonfirmasi (confirmed)
3. Siap Diambil (ready_for_pickup)
4. Dalam Pengiriman (delivery)
5. Selesai (completed)

Timeline dengan:
- Icons untuk setiap tahap
- Timestamps
- Status indicator (done/pending)
- Color coding

#### Sidebar Status Card
- Order Status (colored badge)
- Item Status
- Courier Info (name, phone)
- Timeline ringkas
- Sticky ketika scroll

#### Action Buttons
Contextual buttons berdasarkan order_status:
- Pickup untuk Delivery: `courierPickupDelivery()`
- Complete Delivery: `courierCompleteDelivery()`
- Pickup untuk Return: `courierPickupReturn()`
- Complete Return: `courierCompleteReturn()`

Setiap aksi membuka SweetAlert2 dialog dengan:
- Konfirmasi pesan
- Photo upload dengan preview
- Submit button

#### Information Box
Tips dan panduan untuk kurir tentang:
- Cara mengambil barang
- Cara mengantarkan dengan aman
- Cara mengambil barang kembali
- Apa yang harus dilakukan jika ada kerusakan

### View Variables Required
```php
[
    'booking' => BookProduct|Book, // Model dengan relasi product/package
    'type' => 'BookProduct'|'Book', // Tipe booking
]
```

### Route
```php
Route::get('/courier/deliveries/{type}/{id}', 
    [CourierDeliveryController::class, 'show'])
    ->name('courier.deliveries.show');
```

### Model Casting
Models harus memiliki:
```php
protected $casts = [
    'item_status' => ItemStatus::class,
    'order_status' => OrderStatus::class,
    'delivery_at' => 'datetime',
    'returned_at' => 'datetime',
];
```

---

## 4. Delivery History (`resources/views/courier/delivery-history.blade.php`)

### Purpose
Halaman untuk melihat riwayat pengiriman yang sudah selesai dengan filter status.

### Key Features

#### Filter Tabs
- **All**: Semua pengiriman selesai
- **Delivered**: Order status DELIVERED
- **Returned**: Order status PENDING_REVIEW (pengembalian sudah diambil)
- **Completed**: Order status COMPLETED
- **Issue**: Order status ISSUE_DETECTED

#### Table Display
Menampilkan:
- Kode Booking
- Nama Item (product/package)
- Tipe Booking
- Status Pengiriman (badge)
- Tanggal Selesai (delivered_at atau returned_at)

#### Pagination
- 15 item per halaman
- Links untuk navigasi halaman

#### Detail Link
Setiap row memiliki link ke delivery detail view dengan icon chevron-right

### Active Filters
Kombinasi BookProduct dan Book difilter berdasarkan:
```php
whereIn('order_status', [
    OrderStatus::DELIVERED,
    OrderStatus::PENDING_REVIEW,
    OrderStatus::COMPLETED,
    OrderStatus::ISSUE_DETECTED
])
```

### View Variables Required
```php
[
    'bookings' => Paginator, // Hasil query dengan pagination
]
```

### Route
```php
Route::get('/courier/deliveries/history', 
    [CourierDeliveryController::class, 'history'])
    ->name('courier.deliveries.history');
```

---

## Status Transitions in Courier Pages

### Delivery Flow
```
READY_FOR_PICKUP
    ↓ [courierPickupDelivery - photo]
PICKED_UP_FOR_DELIVERY
    ↓ [courierCompleteDelivery - photo]
DELIVERED
    ↓ [System automatically]
OUT_FOR_DELIVERY (item_status)
```

### Return Flow
```
PICKUP_SCHEDULED
    ↓ [courierPickupReturn - photo]
PICKED_UP_FOR_RETURN
    ↓ [courierCompleteReturn - photo]
PENDING_REVIEW (item_status: RETURNING)
    ↓ [Officer inspection]
COMPLETED
```

---

## JavaScript Functions in Actions Component

### Photo Upload Dialog
```javascript
openPhotoUploadDialog(action, bookingId, type)
```
- Membuka SweetAlert2 dialog
- Input file untuk foto
- Preview image
- Submit dengan AJAX

### Courier Actions
```javascript
courierPickupDelivery()      // POST /booking-status/[type]/courier/pickup-delivery
courierCompleteDelivery()    // POST /booking-status/[type]/courier/complete-delivery
courierPickupReturn()        // POST /booking-status/[type]/courier/pickup-return
courierCompleteReturn()      // POST /booking-status/[type]/courier/complete-return
```

Setiap aksi:
1. Kirim AJAX request dengan foto ke endpoint
2. Tampilkan success/error message
3. Reload page atau update status display

---

## Controller Implementation

### CourierDeliveryController

#### `index()` Method
```php
public function index(): View
{
    // Get courier from authenticated user
    $courier = auth()->user()->courier;
    
    // Query active deliveries and returns
    // Calculate statistics
    // Get recent completed bookings
    
    return view('courier.index', [
        'stats' => [...],
        'activeDeliveries' => $collection,
        'activeReturns' => $collection,
        'recentCompleted' => $collection,
    ]);
}
```

#### `show()` Method
```php
public function show(string $type, BookProduct|Book $booking): View
{
    // Validate courier is assigned to this booking
    if ($booking->id_courier !== auth()->user()->courier?->id) {
        abort(403, 'Unauthorized');
    }
    
    return view('courier.delivery-detail', [
        'booking' => $booking,
        'type' => $type,
    ]);
}
```

#### `history()` Method
```php
public function history(): View
{
    // Get filter from request (all, delivered, returned, completed, issue)
    // Query completed bookings based on filter
    // Return paginated results
    
    return view('courier.delivery-history', [
        'bookings' => $paginator,
    ]);
}
```

---

## Database Schema Requirements

### book_products & books Tables

Columns Required:
```sql
- id (UUID, primary)
- id_courier (UUID, foreign key, nullable) -- Assigned courier
- order_status (VARCHAR, enum-backed) -- Current status
- item_status (VARCHAR, enum-backed) -- Physical state
- delivery_at (TIMESTAMP, nullable) -- When delivered
- returned_at (TIMESTAMP, nullable) -- When returned
- created_at (TIMESTAMP)
- updated_at (TIMESTAMP)
```

Indexes:
```sql
- INDEX (id_courier) -- Fast courier lookup
- INDEX (order_status) -- Fast status filtering
- INDEX (item_status) -- Fast status filtering
```

---

## Enums Used

### OrderStatus Enum
```php
DRAFT, AWAITING_VALIDATION, CONFIRMED, 
READY_FOR_PICKUP, PICKED_UP_FOR_DELIVERY,
OUT_FOR_DELIVERY, DELIVERED,
PICKUP_SCHEDULED, PICKED_UP_FOR_RETURN,
PENDING_REVIEW, COMPLETED, ISSUE_DETECTED
```

Methods:
- `label()` - Indonesian label
- `description()` - Detailed description
- `phase()` - Which phase (Pengajuan, Pengiriman, Pengembalian, Penyelesaian)
- `responsibleRole()` - Who manages this status

### ItemStatus Enum
```php
AVAILABLE, BOOKED, PACKING, PICKED_UP,
DEPLOYED, RETURNING, IN_INSPECTION,
MAINTENANCE, LOST_OR_SCRAPPED
```

Methods:
- `label()` - Display label
- `description()` - Status description
- `responsibleRole()` - Responsible party

---

## Service Layer Integration

### BookingStatusService
Called internally by BookingStatusController when courier actions are performed:
- `updateOrderStatus()` - Change order status
- `updateItemStatus()` - Change item status
- `assignCourier()` - Assign courier to booking

### CourierStatusService
Direct methods for courier operations:
- `pickupForDelivery()` - Start delivery
- `completeDelivery()` - Complete delivery
- `pickupForReturn()` - Start return
- `completeReturn()` - Complete return

Each method:
- Validates authorization
- Updates status fields
- Stores photo URLs
- Returns boolean success

---

## Security & Authorization

### Middleware
```php
Route::middleware(['auth:web,courier'])->group(...)
```
Only authenticated couriers can access these routes.

### Model Authorization
```php
// In show() method
if ($booking->id_courier !== auth()->user()->courier?->id) {
    abort(403, 'Unauthorized');
}
```
Courier dapat hanya melihat pengiriman yang ditugaskan kepada mereka.

### Role-Based Actions
- Hanya courier yang ditugaskan dapat mengupdate status
- Officer/Admin tidak dapat melakukan courier actions
- User tidak dapat melihat pages ini

---

## Navigation Integration

### Courier Navigation Component
```blade
<x-courier-nav :readyForPickupCount="$readyForPickupCount" />
```

Shows:
- Link ke Pengiriman Aktif (delivery.index)
- Badge dengan jumlah siap diambil
- Link ke History Pengiriman (delivery.history)

---

## Testing Checklist

- [ ] Courier dapat melihat dashboard
- [ ] Stats menampilkan jumlah yang benar
- [ ] Pengiriman aktif ditampilkan dengan benar
- [ ] Pengembalian aktif ditampilkan dengan benar
- [ ] Klik pengiriman membuka delivery detail
- [ ] Photo upload dialog berfungsi
- [ ] Status berubah setelah submit aksi
- [ ] History page menampilkan pengiriman selesai
- [ ] Filter pada history page bekerja
- [ ] Pagination pada history page bekerja
- [ ] Courier tidak bisa akses delivery bukan miliknya
- [ ] Timeline menampilkan dengan benar
- [ ] Breadcrumb navigation bekerja

---

## Future Enhancements

1. **Real-time Updates**: WebSocket untuk live status updates
2. **GPS Tracking**: Integrasi lokasi GPS untuk pengiriman
3. **Photo Evidence**: Gallery lengkap dengan timestamp GPS
4. **Customer Notifications**: SMS/Email otomatis ke customer
5. **Analytics**: Report pengiriman per kurir per periode
6. **Mobile App**: Native mobile app untuk kurir
7. **Signature Verification**: Digital signature dari penerima
8. **Weather Integration**: Alert cuaca ekstrem

