# Panduan Status Booking System

## Overview
Sistem booking memiliki dua status utama:
1. **Item Status** - Status fisik barang di inventory
2. **Order Status** - Status transaksi/proses order

## Item Status (Status Barang Fisik)

Melacak kondisi dan lokasi barang di gudang.

| Status | Label | Deskripsi | Tanggung Jawab |
|--------|-------|-----------|----------------|
| `Available` | Tersedia | Barang bersih, lengkap, dan siap di rak gudang | Admin |
| `Booked` | Dipesan | Barang sudah dipesan untuk tanggal tertentu | Officer |
| `Packing` | Sedang Dikemas | Barang sedang diambil dari rak dan dimasukkan ke wadah | Officer |
| `Picked-Up` | Diambil Kurir | Barang sudah diserahkan ke kurir untuk dikirim | Courier |
| `Deployed` | Sedang Digunakan | Barang sudah di tangan User dan sedang digunakan | Courier/User |
| `Returning` | Dalam Perjalanan Kembali | Barang sedang dalam perjalanan kembali | Courier |
| `In-Inspection` | Dalam Pemeriksaan | Barang sudah di gudang tapi belum boleh disewa | Officer |
| `Maintenance` | Perawatan | Barang rusak ringan (dijahit/dicuci/servis) | Officer |
| `Lost/Scrapped` | Hilang/Scrap | Barang hilang atau rusak total | Admin |

## Order Status (Status Transaksi)

Melacak alur kerja dari sisi aplikasi dan koordinasi antar role.

### Fase Pengajuan (Application Phase)

| Status | Label | Deskripsi | Tanggung Jawab |
|--------|-------|-----------|----------------|
| `Draft` | Draft | User baru membuat pesanan, menunggu pembayaran | User |
| `Awaiting Validation` | Menunggu Validasi | Pembayaran masuk, Officer cek ketersediaan | Officer |
| `Confirmed` | Terkonfirmasi | Order sah, stok dipotong otomatis | Officer |

**Alur:**
```
Draft → Awaiting Validation → Confirmed → Ready for Pickup
  ↓                               ↓
  Cancelled                   Cancelled
```

### Fase Pengiriman (Delivery Phase)

| Status | Label | Deskripsi | Tanggung Jawab |
|--------|-------|-----------|----------------|
| `Ready for Pickup` | Siap Diambil | Barang sudah dipacking, siap diambil Courier | Courier |
| `Out for Delivery` | Dalam Pengiriman | Courier sedang menuju lokasi User | Courier |
| `Delivered` | Terkirim | Barang sampai ke tangan User | Courier |

**Alur:**
```
Confirmed → Ready for Pickup → Out for Delivery → Delivered → Pickup Scheduled
```

### Fase Pengembalian (Return Phase)

| Status | Label | Deskripsi | Tanggung Jawab |
|--------|-------|-----------|----------------|
| `Pickup Scheduled` | Penjemputan Dijadwalkan | Penjemputan barang dijadwalkan untuk hari terakhir | Officer |
| `On Process Return` | Sedang Dikembalikan | Courier sudah mengambil barang dari User | Courier |
| `Pending Review` | Menunggu Review | Barang di gudang, menunggu QC Officer | Officer |

**Alur:**
```
Delivered → Pickup Scheduled → On Process Return → Pending Review → Completed/Issue Detected
```

### Fase Penyelesaian (Completion Phase)

| Status | Label | Deskripsi | Tanggung Jawab |
|--------|-------|-----------|----------------|
| `Completed` | Selesai | Barang kembali lengkap, deposit dikembalikan | Officer |
| `Issue Detected` | Ada Masalah | Ada barang rusak/kurang, tagihan denda | Officer |
| `Cancelled` | Dibatalkan | Order dibatalkan sebelum pengiriman | Officer/Admin |

## Alur Status Otomatis

Saat `order_status` berubah, `item_status` akan otomatis diupdate:

```
order_status                    →  item_status
──────────────────────────────────────────────
Confirmed                       →  Booked
Ready for Pickup                →  Packing
Out for Delivery                →  Picked-Up
Delivered                       →  Deployed
On Process Return               →  Returning
Pending Review                  →  In-Inspection
Completed                       →  Available
```

## Courier-Specific Operations

Hanya courier yang assign yang bisa melakukan operasi delivery/return:

### 1. Pickup for Delivery
```
Dari: Ready for Pickup
Ke: Out for Delivery
Aksi: Courier mengambil barang (bisa attach foto)
```

### 2. Complete Delivery
```
Dari: Out for Delivery
Ke: Delivered
Aksi: Courier mengantarkan barang (bisa attach foto)
```

### 3. Pickup for Return
```
Dari: Pickup Scheduled
Ke: On Process Return
Aksi: Courier mengambil barang kembali (bisa attach foto)
```

### 4. Complete Return
```
Dari: On Process Return
Ke: Pending Review
Aksi: Courier mengembalikan barang ke gudang (bisa attach foto)
```

## Usage Examples

### Dalam Controller

```php
use App\Services\BookingStatusService;
use App\Services\CourierStatusService;

class BookingController extends Controller
{
    public function __construct(
        protected BookingStatusService $statusService,
        protected CourierStatusService $courierService
    ) {}

    // Officer mengkonfirmasi order
    public function confirmOrder(BookProduct $booking)
    {
        $this->statusService->updateOrderStatus(
            $booking,
            OrderStatus::CONFIRMED
        );
    }

    // Officer siapkan barang untuk pengambilan kurir
    public function prepareForPickup(BookProduct $booking)
    {
        $this->statusService->updateOrderStatus(
            $booking,
            OrderStatus::READY_FOR_PICKUP
        );
    }

    // Courier ambil barang untuk dikirim
    public function courierPickupDelivery(BookProduct $booking)
    {
        $this->courierService->pickupForDelivery(
            $booking,
            $request->file('photo')->store('delivery')
        );
    }

    // Get status timeline
    public function getTimeline(BookProduct $booking)
    {
        return $this->statusService->getStatusTimeline($booking);
    }
}
```

### Dalam Blade

```blade
<!-- Status Badge -->
<span class="badge bg-{{ $booking->order_status->color() }}">
    {{ $booking->order_status->label() }}
</span>

<!-- Item Status -->
<span class="text-muted">
    Barang: {{ $booking->item_status->label() }}
</span>

<!-- Courier Actions -->
@if(auth()->user()->hasRole('courier'))
    @if($booking->order_status == OrderStatus::READY_FOR_PICKUP)
        <button class="btn btn-primary" onclick="pickupDelivery({{ $booking->id }})">
            Ambil Barang
        </button>
    @endif
    
    @if($booking->order_status == OrderStatus::OUT_FOR_DELIVERY)
        <button class="btn btn-success" onclick="completeDelivery({{ $booking->id }})">
            Konfirmasi Pengiriman
        </button>
    @endif
@endif
```

## Database Fields

### book_products & books tables

```sql
-- Status columns
item_status VARCHAR(255) DEFAULT 'Available'
order_status VARCHAR(255) DEFAULT 'Draft'

-- Courier assignment
id_courier CHAR(36) NULLABLE FOREIGN KEY

-- Timestamps
delivery_at TIMESTAMP NULLABLE
returned_at TIMESTAMP NULLABLE

-- Indexes
INDEX idx_item_status (item_status)
INDEX idx_order_status (order_status)
```

## Validasi Status Transition

Sistem akan mencegah status transitions yang tidak valid:

```php
// INVALID - Will throw Exception
$booking->order_status = OrderStatus::PENDING_REVIEW; // dari Draft? Error!

// VALID - Using service
$statusService->updateOrderStatus(
    $booking,
    OrderStatus::AWAITING_VALIDATION // Draft → Awaiting Validation OK
);
```

## Permissions

Setiap status change mungkin memerlukan permission check:

```
Draft → Awaiting Validation    : Officer (validate.order)
Awaiting Validation → Confirmed : Officer (confirm.order)
Confirmed → Ready for Pickup    : Officer (prepare.delivery)
Ready for Pickup → Out for Delivery : Courier (pickup.delivery)
Out for Delivery → Delivered    : Courier (complete.delivery)
Delivered → Pickup Scheduled    : Officer (schedule.return)
Pickup Scheduled → On Process Return : Courier (pickup.return)
On Process Return → Pending Review : Courier (complete.return)
Pending Review → Completed      : Officer (complete.order)
Pending Review → Issue Detected : Officer (detect.issue)
* → Cancelled                   : Officer/Admin (cancel.order)
```

## Best Practices

1. **Selalu gunakan service** untuk update status, jangan update langsung model
2. **Validasi permission** sebelum mengizinkan status change
3. **Attach foto** saat courier melakukan delivery/return (untuk audit trail)
4. **Catat log** setiap status change dengan user dan timestamp
5. **Notifikasi user** saat status berubah (email/push)
6. **Timeline** untuk tracking history semua status changes
