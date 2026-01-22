# 🔧 Courier Role - Troubleshooting & FAQ

## ❓ FAQ

### Q: Bagaimana cara login sebagai Courier?
**A:** 
1. Akses halaman login aplikasi
2. Gunakan salah satu email berikut:
   - `ade.kurir@aplikasipinjam.com`
   - `budi.pengiriman@aplikasipinjam.com`
   - `citra.antar@aplikasipinjam.com`
3. Password: `password123`
4. Sistem akan redirect ke `/courier/dashboard`

### Q: Di mana dashboard Courier?
**A:** Dashboard Courier tersedia di `/courier/dashboard` setelah login

### Q: Apa bedanya Courier dengan Officer?
**A:**
- **Officer**: Mengelola peminjaman buku & produk, pembayaran, pengembalian
- **Courier**: Mengelola pengiriman, pengambilan, dan pelacakan barang

### Q: Bisakah Courier mengakses Admin panel?
**A:** Tidak, setiap role memiliki dashboard dan akses sendiri

### Q: Bagaimana cara menambah Courier baru?
**A:** 
```php
// Via tinker
Courier::create([
    'name' => 'Nama Kurir',
    'email' => 'email@example.com',
    'password' => bcrypt('password'),
    'phone' => '08xxxxxxxxx',
    'address' => 'Jl. ...'
]);

// Assign role
$courier->assignRole('courier');
```

### Q: Bagaimana cara reset password Courier?
**A:** Buat Courier password reset feature atau gunakan:
```php
Courier::find($id)->update(['password' => bcrypt('new_password')]);
```

## 🐛 Troubleshooting

### Problem: Login gagal dengan Courier credentials
**Solution:**
1. Verify database sudah ter-seed: `Courier::count()`
2. Check credentials benar: `Courier::where('email', 'ade.kurir@aplikasipinjam.com')->first()`
3. Run seeder lagi: `php artisan db:seed --class=CourierSeeder`
4. Clear cache: `php artisan cache:clear`

### Problem: Route `/courier/dashboard` not found
**Solution:**
1. Check routes: `php artisan route:list | grep courier`
2. Clear route cache: `php artisan route:cache` → `php artisan route:clear`
3. Verify `routes/web.php` sudah ter-update
4. Restart development server

### Problem: "Unauthorized" error
**Solution:**
1. Verify user punya courier role: `auth()->user()->hasRole('courier')`
2. Check authentication guard: `auth()->guard('courier')->check()`
3. Verify middleware: `['auth:web,courier']` di routes
4. Clear sessions: `php artisan session:clear`

### Problem: Dashboard statistics tidak muncul
**Solution:**
1. Stats di dashboard masih placeholder
2. Untuk show real data, update controller dan model relationships
3. Saat ini hanya menampilkan hardcoded values

### Problem: Role tidak ter-assign ke Courier
**Solution:**
1. Check role exists: `Role::where('name', 'courier')->get()`
2. Assign manually: `$courier->assignRole('courier')`
3. Run RolePermissionSeeder: `php artisan db:seed --class=RolePermissionSeeder`

### Problem: Database error saat migrate
**Solution:**
1. Check migration file: `database/migrations/2026_01_22_create_couriers_table.php`
2. Run fresh: `php artisan migrate:fresh --seed`
3. Check database connection di `.env`

## 🔍 Debugging Tips

### Check User Authentication
```php
// In tinker or controller
auth()->user() // Check current user
auth()->guard('courier')->user() // Check courier guard
auth()->guard('courier')->check() // Check if logged in
```

### Check User Role
```php
auth()->user()->roles // Get all roles
auth()->user()->hasRole('courier') // Check if has role
auth()->user()->getAllPermissions() // Get all permissions
```

### Check Database Data
```php
// In tinker
Courier::all() // List all couriers
Courier::with('roles')->get() // Couriers with roles
Role::where('name', 'courier')->first() // Get courier role
```

### Check Routes
```bash
php artisan route:list --name=courier
php artisan route:list | grep courier
```

### Check Configuration
```bash
php artisan config:show auth
```

## 🛠️ Common Commands

```bash
# Database
php artisan migrate
php artisan migrate:fresh
php artisan migrate:fresh --seed
php artisan migrate:rollback

# Seeding
php artisan db:seed
php artisan db:seed --class=CourierSeeder
php artisan db:seed --class=RolePermissionSeeder

# Cache
php artisan cache:clear
php artisan config:cache
php artisan route:cache

# Other
php artisan tinker
php artisan route:list
php artisan model:show Courier
```

## 📊 Database Queries

### Get all Couriers
```sql
SELECT * FROM couriers;
```

### Get Couriers with their roles
```sql
SELECT c.*, r.name as role_name 
FROM couriers c
LEFT JOIN model_has_roles mhr ON c.id = mhr.model_id
LEFT JOIN roles r ON mhr.role_id = r.id
WHERE mhr.model_type = 'App\Models\Courier';
```

### Check Courier Permissions
```sql
SELECT p.name, p.guard_name
FROM permissions p
LEFT JOIN role_has_permissions rhp ON p.id = rhp.permission_id
LEFT JOIN roles r ON rhp.role_id = r.id
WHERE r.name = 'courier'
ORDER BY p.guard_name, p.name;
```

## 📚 Related Resources

- [COURIER_QUICK_START.md](COURIER_QUICK_START.md) - Quick reference
- [COURIER_IMPLEMENTATION.md](COURIER_IMPLEMENTATION.md) - Implementation details
- [COURIER_SETUP_SUMMARY.md](COURIER_SETUP_SUMMARY.md) - Setup summary
- [COURIER_CHECKLIST.md](COURIER_CHECKLIST.md) - Completion checklist

## 🚨 Emergency Fixes

### Reset Everything
```bash
# This will reset entire database
php artisan migrate:fresh --seed
```

### Re-seed Couriers
```bash
# Delete all couriers
php artisan tinker
>>> Courier::truncate()

# Re-seed
php artisan db:seed --class=CourierSeeder
```

### Reset Authentication
```bash
# Clear all sessions
php artisan session:clear

# Clear authentication caches
php artisan cache:clear
php artisan config:cache
```

## 📞 Support

Jika mengalami masalah:

1. Check dokumentasi: `documentation/COURIER_*.md`
2. Review file yang berubah
3. Check error logs: `storage/logs/`
4. Try seeding ulang
5. Clear cache dan restart server

---

**Last Updated:** January 22, 2026  
**Version:** 1.0
