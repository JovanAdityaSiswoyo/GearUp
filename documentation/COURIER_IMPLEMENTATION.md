# Courier Role Implementation Guide

## Overview
Implementasi role baru "Courier" telah selesai dilakukan. Role ini didesain untuk mengelola pengiriman dan pengembalian barang dalam sistem AplikasiPinjam.

## Fitur yang Telah Dibuat

### 1. **Courier Model** (`app/Models/Courier.php`)
- Model untuk user Courier dengan UUID primary key
- Supports role-based permissions dari Spatie Permission
- Fields: name, email, password, phone, address

### 2. **Database Migration** (`database/migrations/2026_01_22_create_couriers_table.php`)
- Membuat table `couriers` dengan struktur:
  - id (UUID)
  - name
  - email (unique)
  - password
  - phone (nullable)
  - address (nullable)
  - timestamps

### 3. **Courier Dashboard** (`resources/views/courier/dashboard.blade.php`)
Dashboard dengan sidebar biru-hijau yang menampilkan:
- **Stats Cards:**
  - Active Deliveries (pengiriman aktif)
  - Pending Pickups (pengambilan tertunda)
  - Completed Today (selesai hari ini)
  - Returns (pengembalian)

- **Menu Navigasi:**
  - Dashboard
  - Deliveries (Pengiriman)
  - Pickups (Pengambilan)
  - Returns (Pengembalian)
  - Tracking (Pelacakan)

- **Quick Actions:** Tombol akses cepat ke semua fitur

### 4. **Courier Routes** (`routes/web.php`)
```php
Route::prefix('courier')->middleware(['auth:web,courier'])->name('courier.')->group(function () {
    Route::get('/dashboard', ...)->name('dashboard');
    Route::get('/deliveries', ...)->name('deliveries.index');
    Route::get('/pickups', ...)->name('pickups.index');
    Route::get('/returns', ...)->name('returns.index');
    Route::get('/tracking', ...)->name('tracking.index');
});
```

### 5. **Courier Factory** (`database/factories/CourierFactory.php`)
Factory untuk generate test data Courier

### 6. **Courier Seeder** (`database/seeders/CourierSeeder.php`)
Seeder yang membuat 3 courier sample:
- Ade Kurir (ade.kurir@aplikasipinjam.com)
- Budi Pengiriman (budi.pengiriman@aplikasipinjam.com)
- Citra Antar (citra.antar@aplikasipinjam.com)

Password default: `password123`

### 7. **Role & Permissions** (Updated `RolePermissionSeeder.php`)
Courier role memiliki permissions:
- `read-user`, `list-users`
- `read-book`, `list-books`
- `read-product`, `list-products`
- `read-category`, `list-categories`
- `read-package`, `list-packages`
- `read-payment`, `list-payments`
- `read-loan`, `list-loans`
- `view-dashboard`

### 8. **Authentication Config** (Updated `config/auth.php`)
Menambahkan courier guard dan provider:
```php
'guards' => [
    ...
    'courier' => [
        'driver' => 'session',
        'provider' => 'couriers',
    ],
],
'providers' => [
    ...
    'couriers' => [
        'driver' => 'eloquent',
        'model' => App\Models\Courier::class,
    ],
],
```

## Login Credentials

Gunakan salah satu credential di bawah untuk testing:

| Name | Email | Password |
|------|-------|----------|
| Ade Kurir | ade.kurir@aplikasipinjam.com | password123 |
| Budi Pengiriman | budi.pengiriman@aplikasipinjam.com | password123 |
| Citra Antar | citra.antar@aplikasipinjam.com | password123 |

## Accessing Courier Features

1. Login dengan salah satu courier credentials
2. Navigasi ke `/courier/dashboard` untuk masuk ke Courier Panel
3. Gunakan sidebar untuk akses berbagai fitur:
   - **Deliveries**: Manajemen pengiriman barang
   - **Pickups**: Manajemen pengambilan barang
   - **Returns**: Proses pengembalian barang
   - **Tracking**: Pelacakan pengiriman

## File yang Dimodifikasi/Dibuat

### New Files:
- `app/Models/Courier.php`
- `database/migrations/2026_01_22_create_couriers_table.php`
- `database/factories/CourierFactory.php`
- `database/seeders/CourierSeeder.php`
- `resources/views/courier/dashboard.blade.php`

### Modified Files:
- `config/auth.php` - Added courier guard dan provider
- `database/seeders/RolePermissionSeeder.php` - Added courier role
- `database/seeders/DatabaseSeeder.php` - Added CourierSeeder call
- `routes/web.php` - Added courier routes

## Next Steps (Optional)

Untuk melengkapi implementasi Courier, Anda dapat:

1. **Create Controllers:**
   - `CourierDeliveryController` - Manage deliveries
   - `CourierPickupController` - Manage pickups
   - `CourierReturnController` - Manage returns
   - `CourierTrackingController` - Track deliveries

2. **Create Views:**
   - Deliveries list/form
   - Pickups list/form
   - Returns list/form
   - Tracking page

3. **Create Models (if needed):**
   - `Delivery` model
   - `Pickup` model
   - `CourierRoute` model

4. **Add Relationships:**
   - Link Delivery/Pickup/Returns to Courier
   - Link to Book/Product loans

5. **Add Validations & Business Logic:**
   - Delivery status tracking
   - Route optimization
   - Proof of delivery features

## Testing

Run migrations and seeders:
```bash
php artisan migrate
php artisan db:seed
```

Or specific seeders:
```bash
php artisan db:seed --class=RolePermissionSeeder
php artisan db:seed --class=CourierSeeder
```

## Notes

- Courier uses UUID for primary key (konsisten dengan Officer dan Admin)
- Middleware untuk routes: `auth:web,courier` untuk multi-guard authentication
- Dashboard styling menggunakan Tailwind CSS dan Heroicons
- Spatie Permission package digunakan untuk role & permissions management
