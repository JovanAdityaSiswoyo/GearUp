# 🚚 Courier Role - Quick Start

## 🎯 Apa Itu?
Role **Courier** adalah role baru untuk mengelola pengiriman dan pengembalian barang dalam sistem AplikasiPinjam.

## 🔐 Login Test Data

Gunakan salah satu akun berikut untuk testing:

| No | Email | Password |
|----|-------|----------|
| 1 | `ade.kurir@aplikasipinjam.com` | password123 |
| 2 | `budi.pengiriman@aplikasipinjam.com` | password123 |
| 3 | `citra.antar@aplikasipinjam.com` | password123 |

## 🚀 Cara Mengakses

1. Buka aplikasi dan login dengan email courier di atas
2. Sistem akan redirect ke `/courier/dashboard`
3. Gunakan sidebar untuk navigasi ke berbagai fitur

## 📊 Dashboard Features

### Sidebar Menu
- 🏠 **Dashboard** - Halaman utama dengan statistik
- 🚚 **Deliveries** - Kelola pengiriman barang
- 📦 **Pickups** - Kelola pengambilan barang
- ↩️ **Returns** - Proses pengembalian barang
- 📍 **Tracking** - Lacak status pengiriman

### Statistics Cards
- **Active Deliveries** - Jumlah pengiriman yang sedang berlangsung
- **Pending Pickups** - Barang yang menunggu untuk diambil
- **Completed Today** - Pengiriman yang sudah selesai hari ini
- **Returns** - Barang yang harus dikembalikan

### Quick Actions
Akses cepat ke semua fitur utama dari dashboard

## 📁 File Structure

### Files Baru:
```
app/Models/Courier.php
database/migrations/2026_01_22_create_couriers_table.php
database/factories/CourierFactory.php
database/seeders/CourierSeeder.php
resources/views/courier/dashboard.blade.php
documentation/COURIER_IMPLEMENTATION.md
documentation/COURIER_SETUP_SUMMARY.md
```

### Files Dimodifikasi:
```
config/auth.php
database/seeders/RolePermissionSeeder.php
database/seeders/DatabaseSeeder.php
routes/web.php
```

## 🔑 Permissions

Courier role memiliki akses:
- ✅ View dashboard
- ✅ Read user data
- ✅ Read book & product data
- ✅ Read category & package data
- ✅ Read payment & loan data
- ✅ View reports

## 🛠️ Useful Commands

### Check Courier Routes
```bash
php artisan route:list | grep courier
```

### Access Database
```bash
php artisan tinker
>>> Courier::all()
>>> Role::where('name', 'courier')->get()
```

### Reset Database
```bash
php artisan migrate:fresh --seed
```

## 🎨 Design Colors

- **Primary:** Green (#10b981)
- **Secondary:** Emerald (#059669)
- **Gradient:** from-green-600 to-emerald-500

## 💡 Tips

1. Dashboard belum terhubung ke database (placeholder stats)
2. Menu items untuk deliveries/pickups/returns/tracking masih perlu controller
3. Untuk development lengkap, buat controller & views untuk setiap fitur
4. Gunakan existing pattern dari admin/officer untuk consistency

## 🔗 Related Documentation

- [COURIER_IMPLEMENTATION.md](COURIER_IMPLEMENTATION.md) - Detailed implementation guide
- [COURIER_SETUP_SUMMARY.md](COURIER_SETUP_SUMMARY.md) - Complete setup summary
- [SPATIE_PERMISSION_GUIDE.md](SPATIE_PERMISSION_GUIDE.md) - Permission system documentation

## ❓ Frequently Used Routes

| Route | Purpose |
|-------|---------|
| `/courier/dashboard` | Main dashboard |
| `/courier/deliveries` | View all deliveries |
| `/courier/pickups` | View all pickups |
| `/courier/returns` | View returns |
| `/courier/tracking` | Track shipments |

---

**Created:** January 22, 2026  
**Version:** 1.0  
**Status:** ✅ Ready to Use
