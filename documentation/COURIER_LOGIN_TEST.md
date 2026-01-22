# 🚚 Courier Login Test Guide

## ✅ Login Sudah Fixed!

Login component telah di-update untuk support Courier authentication. Sekarang Anda bisa login menggunakan Courier credentials.

## 🔐 Test Credentials

Gunakan salah satu credentials di bawah:

### Courier 1: Ade Kurir
- **Email:** `ade.kurir@aplikasipinjam.com`
- **Password:** `password123`

### Courier 2: Budi Pengiriman
- **Email:** `budi.pengiriman@aplikasipinjam.com`
- **Password:** `password123`

### Courier 3: Citra Antar
- **Email:** `citra.antar@aplikasipinjam.com`
- **Password:** `password123`

## 🚀 Cara Login

### Step 1: Buka Login Page
1. Navigasi ke login page aplikasi
2. Atau buka: `/login`

### Step 2: Masukkan Credentials
1. **Email Field:** Paste email courier dari tabel di atas
   - Contoh: `ade.kurir@aplikasipinjam.com`

2. **Password Field:** Masukkan password
   - Default: `password123`

### Step 3: Click Login Button
1. Klik tombol "Log in"
2. Sistem akan memproses autentikasi

### Step 4: Redirect ke Dashboard
Jika berhasil:
- ✅ Sistem redirect ke `/courier/dashboard`
- ✅ Dashboard menampilkan "Courier Panel"
- ✅ Sidebar navigation terlihat dengan menu Deliveries, Pickups, Returns, Tracking

## 🎯 Expected Results

Setelah login dengan courier credentials, Anda akan:

1. **Redirect otomatis** ke `/courier/dashboard`
2. **Melihat Courier Dashboard** dengan:
   - Sidebar berwarna hijau (green gradient)
   - Judul "Courier Panel"
   - Menu navigasi: Dashboard, Deliveries, Pickups, Returns, Tracking
   - Statistics cards
   - Quick action buttons
3. **Bisa logout** menggunakan tombol di header

## ❌ Troubleshooting

### Jika Still Error "credentials do not match"

**Kemungkinan penyebab:**
1. ❌ Email tidak tepat
2. ❌ Password salah
3. ❌ Cache tidak ter-clear

**Solusi:**
```bash
# Clear cache
php artisan cache:clear
php artisan config:cache

# Re-seed data (jika perlu)
php artisan db:seed --class=CourierSeeder

# Restart development server
```

### Jika Login Redirect ke Home Page
- Artinya login menggunakan guard 'web' (User biasa)
- Check email untuk memastikan sesuai courier email
- Pastikan password benar: `password123`

### Jika Dashboard Blank/Error
- Clear browser cache: Ctrl+Shift+Delete
- Hard refresh: Ctrl+F5
- Check Laravel logs: `storage/logs/`

## 📊 Database Verification

Untuk verify data courier di database:

```bash
php artisan tinker
```

Kemudian jalankan:
```php
# Check total couriers
Courier::count()

# List semua couriers
Courier::all(['name', 'email'])

# Check specific courier
Courier::where('email', 'ade.kurir@aplikasipinjam.com')->first()

# Check courier role
Courier::first()->roles
```

## 🔄 Login Flow

```
User Input Email & Password
        ↓
Try Login as User (web guard)
        ↓
Try Login as Admin (admin guard)
        ↓
Try Login as Officer (officer guard)
        ↓
Try Login as Courier (courier guard) ← NEW! ✅
        ↓
Success → Redirect to /courier/dashboard
        OR
        ↓
Failure → Show "credentials do not match" error
```

## ✨ Features Available After Login

### Dashboard:
- 📊 Statistics display (4 stat cards)
- 🗂️ Sidebar navigation
- 👤 User profile display
- 🔔 Notification system
- 🚪 Logout button

### Navigation Menu:
- 🏠 Dashboard
- 🚚 Deliveries
- 📦 Pickups
- ↩️ Returns
- 📍 Tracking

## 💡 Tips

1. **Remember Me:** Check "Remember me" untuk otomatis login next time
2. **Session:** Login session akan tetap sampai logout atau session expired
3. **Multiple Guards:** Bisa login sebagai different roles dari satu account (jika ada)
4. **Logout:** Gunakan logout button di header, akan clear session

## 🎉 Next Steps

Setelah berhasil login:

1. **Explore Dashboard:**
   - Lihat statistics cards
   - Check sidebar menu items
   - Click quick action buttons

2. **Develop More Features:**
   - Create controllers untuk Deliveries, Pickups, Returns, Tracking
   - Create views untuk setiap fitur
   - Integrate dengan database models

3. **Add More Couriers:**
   - Via database seeder
   - Via admin panel (jika sudah dibuat)
   - Via Tinker

---

**Last Updated:** January 22, 2026  
**Status:** ✅ Login Fixed & Ready to Use

For more info, check: [COURIER_LOGIN_FIX.md](COURIER_LOGIN_FIX.md)
