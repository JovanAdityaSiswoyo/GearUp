# 🔐 Login Credentials

Sistem login telah dikonfigurasi untuk 3 role berbeda menggunakan 1 endpoint URL yang sama: `/login`

## 📋 Test Accounts

### 👑 Super Admin
- **Email:** `admin@example.com`
- **Password:** `password123`
- **Dashboard:** `/admin/dashboard`
- **Permissions:** Full access ke semua fitur sistem
- **Features:**
  - Kelola Users, Books, Products, Categories, Packages
  - View & Export Reports
  - View Analytics
  - Full CRUD operations

### 👮 Officer  
- **Email:** `officer@example.com`
- **Password:** `password123`
- **Dashboard:** `/officer/dashboard`
- **Permissions:** Mengelola peminjaman dan pembayaran
- **Features:**
  - Proses peminjaman buku & produk
  - Verifikasi pembayaran
  - Proses pengembalian
  - View laporan

### 👤 Regular User
- **Email:** `user@example.com`
- **Password:** `password123`
- **Dashboard:** `/dashboard`
- **Permissions:** Browse dan pinjam items
- **Features:**
  - Browse books & products
  - Borrow items
  - View personal loan history

## 🚀 Cara Menggunakan

1. **Buka browser** dan akses: `http://localhost:8000/login`

2. **Login dengan salah satu akun di atas**

3. **Sistem otomatis redirect** ke dashboard sesuai role:
   - Admin → `/admin/dashboard` (Purple/Pink theme)
   - Officer → `/officer/dashboard` (Blue/Cyan theme)  
   - User → `/dashboard` (Default theme)

## 🎨 Dashboard Features

### Admin Dashboard
- Total Users, Books, Products statistics
- Revenue tracking
- Recent users list
- Quick actions (Add User, Add Book, Add Product, View Payments)
- Purple-Pink gradient sidebar

### Officer Dashboard  
- Active loan tracking (Books & Products)
- Pending returns counter
- Pending payments list
- Recent book loans
- Quick actions (New Loan, Process Return, Verify Payment)
- Blue-Cyan gradient sidebar

### User Dashboard
- Personal loan statistics
- Browse categories
- Available products showcase
- Borrow functionality

## 🔒 Security Features

✅ **Multi-Guard Authentication** - Separate authentication untuk User, Admin, dan Officer
✅ **Role-Based Access Control (RBAC)** - Menggunakan Spatie Laravel Permission
✅ **Protected Routes** - Middleware auth untuk setiap guard
✅ **Permission System** - 40+ permissions across 8 categories
✅ **Password Hashing** - Bcrypt encryption

## 📱 Routes Overview

### Guest Routes
- `GET /login` - Login page
- `GET /register` - Registration page

### User Routes (auth:web)
- `GET /dashboard` - User dashboard

### Admin Routes (auth:web,admin)
- `GET /admin/dashboard` - Admin dashboard
- `GET /admin/users` - Users management
- `GET /admin/books` - Books management
- `GET /admin/products` - Products management
- `GET /admin/categories` - Categories management
- `GET /admin/packages` - Packages management
- `GET /admin/payments` - Payments management

### Officer Routes (auth:web,officer)
- `GET /officer/dashboard` - Officer dashboard
- `GET /officer/books` - Book loans management
- `GET /officer/products` - Product loans management
- `GET /officer/payments` - Payments management
- `GET /officer/returns` - Returns management

### Common Routes
- `POST /logout` - Logout (all guards)

## 🎯 Next Steps

1. Start development server:
   ```bash
   php artisan serve
   ```

2. Access login page:
   ```
   http://localhost:8000/login
   ```

3. Try logging in with different roles to see different dashboards

4. Start building CRUD functionality for each module

## 📝 Notes

- Semua password adalah `password123` untuk kemudahan testing
- Untuk production, gunakan password yang lebih kuat
- Role permissions dapat di-customize di `RolePermissionSeeder.php`
- Guard configuration ada di `config/auth.php`
