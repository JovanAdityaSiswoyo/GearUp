# Informasi Login Aplikasi

## Password Default
Semua user, admin, dan officer menggunakan password yang sama:
```
password
```

## Kredensial Login

### 🔵 User Login
**URL**: `http://localhost:8000/login` (guard: web)

Email random yang di-generate oleh faker, contoh:
- Email: Cek di database tabel `users` untuk melihat email yang tersedia
- Password: `password`

**Cara cek email user:**
```bash
php artisan tinker
```
Lalu jalankan:
```php
\App\Models\User::all()->pluck('email', 'name');
```

### 🟣 Admin Login
**URL**: `http://localhost:8000/admin/login` (guard: admin)

Email random yang di-generate oleh faker, contoh:
- Email: Cek di database tabel `admins` untuk melihat email yang tersedia
- Password: `password`

**Cara cek email admin:**
```bash
php artisan tinker
```
Lalu jalankan:
```php
\App\Models\Admin::all()->pluck('email', 'name');
```

### 🟢 Officer Login
**URL**: `http://localhost:8000/officer/login` (guard: officer)

Email random yang di-generate oleh faker, contoh:
- Email: Cek di database tabel `officers` untuk melihat email yang tersedia
- Password: `password`

**Cara cek email officer:**
```bash
php artisan tinker
```
Lalu jalankan:
```php
\App\Models\Officer::all()->pluck('email', 'name');
```

## Quick Access - Cek Semua Email

Untuk melihat semua email sekaligus, jalankan di terminal:

```bash
php artisan tinker
```

Lalu:
```php
// User emails
echo "=== USERS ===\n";
\App\Models\User::all()->each(function($u) { echo $u->name . ' => ' . $u->email . "\n"; });

echo "\n=== ADMINS ===\n";
\App\Models\Admin::all()->each(function($a) { echo $a->name . ' => ' . $a->email . "\n"; });

echo "\n=== OFFICERS ===\n";
\App\Models\Officer::all()->each(function($o) { echo $o->name . ' => ' . $o->email . "\n"; });
```

## Atau Buat User Khusus untuk Testing

Jalankan di tinker:
```php
// Buat admin dengan email yang mudah diingat
\App\Models\Admin::create([
    'name' => 'Super Admin',
    'email' => 'admin@test.com',
    'password' => bcrypt('password')
])->assignRole('admin');

// Buat officer dengan email yang mudah diingat
\App\Models\Officer::create([
    'name' => 'Test Officer',
    'email' => 'officer@test.com',
    'password' => bcrypt('password')
])->assignRole('officer');

// Buat user dengan email yang mudah diingat
\App\Models\User::create([
    'name' => 'Test User',
    'email' => 'user@test.com',
    'password' => bcrypt('password')
])->assignRole('user');
```

Setelah itu bisa login dengan:
- Admin: `admin@test.com` / `password`
- Officer: `officer@test.com` / `password`
- User: `user@test.com` / `password`

## Notes
- Semua password di-hash dengan bcrypt
- Setiap kali `php artisan migrate:fresh --seed`, email akan berubah (random dari Faker)
- Untuk email yang konsisten, buat manual seperti contoh di atas
