# 🚀 SPATIE PERMISSION - 5 MINUTE QUICK START

**Panduan Super Cepat untuk Mulai Menggunakan Spatie Laravel Permission**

---

## ⚡ Dalam 5 Menit

### 1️⃣ Update .env (1 menit)
```env
DB_HOST=127.0.0.1
DB_DATABASE=your_db
DB_USERNAME=root
DB_PASSWORD=password
```

### 2️⃣ Run Migration (1 menit)
```bash
php artisan migrate
```

### 3️⃣ Seed Data (1 menit)
```bash
php artisan db:seed --class=RolePermissionSeeder
```

### 4️⃣ Test It (1 menit)
```bash
php artisan tinker
```

```php
$user = App\Models\User::first();
$user->assignRole('admin');
$user->hasRole('admin'); // true
exit
```

### 5️⃣ Protect Route (1 menit)
```php
// routes/api.php
Route::post('/books', [BookController::class, 'store'])
    ->middleware('permission:create-book');
```

---

## 📚 Documentation

| File | Purpose | Time |
|------|---------|------|
| [README_SPATIE_PERMISSION.md](README_SPATIE_PERMISSION.md) | Overview & Setup | 10 min |
| [SPATIE_PERMISSION_QUICK_REF.md](SPATIE_PERMISSION_QUICK_REF.md) | Cheat Sheet | 5 min |
| [SPATIE_PERMISSION_GUIDE.md](SPATIE_PERMISSION_GUIDE.md) | Complete Guide | 40 min |
| [ROUTES_PERMISSION_EXAMPLE.php](ROUTES_PERMISSION_EXAMPLE.php) | Copy-Paste Routes | 10 min |

---

## 🎯 4 Roles Explained

```
SUPER-ADMIN  → All permissions
ADMIN        → Read & Update
OFFICER      → Manage transactions
USER         → Read-only
```

---

## ✅ Done!

You now have enterprise-grade access control! 🎉

Next: Protect your routes + write tests
