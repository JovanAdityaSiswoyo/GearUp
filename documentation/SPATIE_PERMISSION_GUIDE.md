# Spatie Laravel Permission Implementation Guide

Dokumentasi lengkap untuk implementasi Spatie Laravel Permission di aplikasi AplikasiPinjam.

## Daftar Isi
1. [Overview](#overview)
2. [Instalasi](#instalasi)
3. [Struktur Permissions](#struktur-permissions)
4. [Struktur Roles](#struktur-roles)
5. [Penggunaan di Model](#penggunaan-di-model)
6. [Penggunaan di Routes](#penggunaan-di-routes)
7. [Penggunaan di Controllers](#penggunaan-di-controllers)
8. [Penggunaan di Views](#penggunaan-di-views)
9. [Managemen Roles & Permissions](#managemen-roles--permissions)
10. [Best Practices](#best-practices)

## Overview

Spatie Laravel Permission adalah library standar industri untuk mengelola roles dan permissions di Laravel. Dengan package ini, Anda dapat:

- **Mendefinisikan Permissions**: Izin spesifik yang dapat dilakukan (create, read, update, delete)
- **Membuat Roles**: Peran yang mengumpulkan beberapa permissions
- **Assign ke Users**: Memberikan roles/permissions kepada users
- **Middleware Protection**: Melindungi routes dengan middleware

### Package Details
- **Package**: `spatie/laravel-permission`
- **Version**: ^6.24.0
- **Website**: https://spatie.be/docs/laravel-permission/v6/introduction

## Instalasi

### Step 1: Install Package via Composer
```bash
composer require spatie/laravel-permission
```

### Step 2: Publish Configuration & Migrations
```bash
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
```

### Step 3: Run Migrations
```bash
php artisan migrate
```

Ini akan membuat tabel:
- `permissions` - Menyimpan daftar semua permissions
- `roles` - Menyimpan daftar semua roles
- `role_has_permissions` - Relasi antara roles dan permissions
- `model_has_roles` - Relasi antara users dan roles
- `model_has_permissions` - Relasi antara users dan permissions (untuk direct permission)

### Step 4: Jalankan Seeder untuk Inisialisasi Roles & Permissions
```bash
php artisan db:seed --class=RolePermissionSeeder
```

## Struktur Permissions

Permissions didefinisikan dalam format `action-resource`:

### User Management
- `create-user` - Membuat user baru
- `read-user` - Melihat detail user
- `update-user` - Mengupdate data user
- `delete-user` - Menghapus user
- `list-users` - Melihat daftar users

### Book Management
- `create-book` - Membuat buku baru
- `read-book` - Melihat detail buku
- `update-book` - Mengupdate data buku
- `delete-book` - Menghapus buku
- `list-books` - Melihat daftar buku

### Product Management
- `create-product` - Membuat produk baru
- `read-product` - Melihat detail produk
- `update-product` - Mengupdate data produk
- `delete-product` - Menghapus produk
- `list-products` - Melihat daftar produk

### Category Management
- `create-category` - Membuat kategori baru
- `read-category` - Melihat detail kategori
- `update-category` - Mengupdate kategori
- `delete-category` - Menghapus kategori
- `list-categories` - Melihat daftar kategori

### Package Management
- `create-package` - Membuat paket baru
- `read-package` - Melihat detail paket
- `update-package` - Mengupdate paket
- `delete-package` - Menghapus paket
- `list-packages` - Melihat daftar paket

### Payment Management
- `create-payment` - Membuat pembayaran
- `read-payment` - Melihat detail pembayaran
- `update-payment` - Mengupdate pembayaran
- `delete-payment` - Menghapus pembayaran
- `list-payments` - Melihat daftar pembayaran
- `approve-payment` - Menyetujui pembayaran

### Loan Management
- `create-loan` - Membuat pinjaman baru
- `read-loan` - Melihat detail pinjaman
- `update-loan` - Mengupdate pinjaman
- `delete-loan` - Menghapus pinjaman
- `list-loans` - Melihat daftar pinjaman
- `approve-loan` - Menyetujui pinjaman
- `reject-loan` - Menolak pinjaman

### Report & System
- `view-reports` - Melihat laporan
- `export-reports` - Export laporan
- `manage-roles` - Mengelola roles
- `manage-permissions` - Mengelola permissions
- `view-dashboard` - Melihat dashboard
- `view-analytics` - Melihat analytics

## Struktur Roles

Ada 4 role utama dalam sistem:

### 1. Super Admin
**Permissions**: Semua permissions

**Gunakan untuk**: Owner/pengembang aplikasi

```php
$user->assignRole('super-admin');
```

### 2. Admin
**Permissions**:
- Semua `read-*` dan `update-*` (tidak bisa create/delete)
- `list-*` untuk semua resources
- `view-reports`, `export-reports`
- `view-dashboard`, `view-analytics`

**Gunakan untuk**: Administrator sistem

```php
$user->assignRole('admin');
```

### 3. Officer
**Permissions**:
- `read-*` dan `list-*` untuk semua resources
- `create-payment`, `create-loan`
- `approve-loan`, `reject-loan`
- `view-reports`, `view-dashboard`

**Gunakan untuk**: Petugas/Officer yang mengelola transaksi

```php
$user->assignRole('officer');
```

### 4. User
**Permissions**:
- `read-book`, `list-books`
- `read-product`, `list-products`
- `read-category`, `list-categories`
- `read-package`, `list-packages`
- `view-dashboard`

**Gunakan untuk**: User biasa/member

```php
$user->assignRole('user');
```

## Penggunaan di Model

Model yang memiliki roles/permissions harus menggunakan trait `HasRoles`:

```php
<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles;

    // ... model properties
}
```

Model `User`, `Admin`, dan `Officer` sudah dilengkapi dengan trait ini.

## Penggunaan di Routes

### Protect Route dengan Role
```php
// routes/api.php

Route::post('/users', [UserController::class, 'store'])
    ->middleware('role:super-admin');

Route::get('/users', [UserController::class, 'index'])
    ->middleware('role:admin,super-admin');
```

### Protect Route dengan Permission
```php
Route::post('/payments', [PaymentController::class, 'store'])
    ->middleware('permission:create-payment');

Route::put('/payments/{id}', [PaymentController::class, 'update'])
    ->middleware('permission:update-payment');
```

### Protect Route dengan Role OR Permission
```php
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware('permission:view-dashboard');
```

### Multiple Middleware
```php
Route::delete('/users/{id}', [UserController::class, 'destroy'])
    ->middleware(['auth', 'permission:delete-user']);
```

## Penggunaan di Controllers

### Check Permission di Controller
```php
<?php

namespace App\Http\Controllers;

class BookController extends Controller
{
    public function store(Request $request)
    {
        // Check if user has permission
        if ($request->user()->cannot('create-book')) {
            return response()->json([
                'message' => 'You do not have permission to create books'
            ], 403);
        }

        // Create book logic here
    }

    public function destroy(Book $book)
    {
        // Check if user has specific permission
        if (!auth()->user()->hasPermissionTo('delete-book')) {
            abort(403, 'Unauthorized action.');
        }

        // Delete book logic here
    }
}
```

### Check Role di Controller
```php
public function approvePayment(Payment $payment)
{
    // Check if user has role
    if (!auth()->user()->hasRole('officer')) {
        abort(403, 'Only officers can approve payments.');
    }

    // Approve payment logic here
}
```

### Multiple Role/Permission Check
```php
public function update(Book $book, Request $request)
{
    // Check multiple roles
    if (!auth()->user()->hasAnyRole(['admin', 'super-admin'])) {
        abort(403, 'Unauthorized');
    }

    // Check all permissions required
    if (!auth()->user()->hasAllPermissions(['update-book', 'read-book'])) {
        abort(403, 'Missing required permissions');
    }

    // Update logic here
}
```

## Penggunaan di Views

### Check Permission di Blade Template
```blade
@can('create-book')
    <button class="btn btn-primary">Add New Book</button>
@endcan

@cannot('delete-book')
    <p>You don't have permission to delete books</p>
@endcannot
```

### Check Role di Blade Template
```blade
@role('admin|super-admin')
    <div class="admin-panel">
        <!-- Admin only content -->
    </div>
@endrole

@role('officer')
    <div class="officer-panel">
        <!-- Officer only content -->
    </div>
@endrole
```

### Conditional Content
```blade
@if(auth()->user()->hasPermissionTo('view-reports'))
    <a href="{{ route('reports.index') }}">View Reports</a>
@endif

@if(auth()->user()->hasRole('super-admin'))
    <a href="{{ route('roles.manage') }}">Manage Roles</a>
@endif
```

## Managemen Roles & Permissions

### Via Tinker Console
```bash
php artisan tinker
```

```php
// Get user
$user = App\Models\User::find(1);

// Assign role
$user->assignRole('admin');

// Assign multiple roles
$user->assignRole(['admin', 'officer']);

// Sync roles (replace existing)
$user->syncRoles(['user']);

// Remove role
$user->removeRole('officer');

// Check role
$user->hasRole('admin');
$user->hasAnyRole(['admin', 'officer']);
$user->hasAllRoles(['admin', 'officer']);

// Give direct permission (tanpa role)
$user->givePermissionTo('create-book');

// Check permission
$user->hasPermissionTo('create-book');
$user->hasAnyPermission(['create-book', 'update-book']);

// Get all roles
$user->getRoleNames();

// Get all permissions
$user->getPermissionNames();
```

### Via Artisan Commands
```bash
# Create role
php artisan permission:create-role super-admin

# Create permission
php artisan permission:create-permission create-book

# Grant permission to role
php artisan permission:add-permission-to-role create-book admin
```

### Via Seeder (Recommended)
Gunakan `RolePermissionSeeder` yang sudah dibuat:

```bash
php artisan db:seed --class=RolePermissionSeeder
```

File: `database/seeders/RolePermissionSeeder.php`

## Best Practices

### 1. Selalu Cache Permissions
Setelah membuat/mengubah role/permission, clear cache:

```php
// Di code
app()['cache']->forget('spatie.permission.cache');

// Via terminal
php artisan permission:cache-reset
```

### 2. Use Gates & Policies
Gabungkan dengan Laravel Gates untuk logika yang lebih kompleks:

```php
// app/Providers/AuthServiceProvider.php

use Illuminate\Support\Facades\Gate;

public function boot(): void
{
    Gate::define('approve-payment', function ($user, $payment) {
        return $user->hasPermissionTo('approve-payment') 
            && $payment->status === 'pending';
    });
}
```

Gunakan di controller:
```php
if (auth()->user()->cannot('approve-payment', $payment)) {
    abort(403);
}
```

### 3. Gunakan Policy Class
```php
// app/Policies/PaymentPolicy.php

public function approve(User $user, Payment $payment)
{
    return $user->hasPermissionTo('approve-payment');
}
```

### 4. Consistent Naming Convention
- Gunakan format `action-resource`
- Action: `create`, `read`, `update`, `delete`, `list`, `approve`, `reject`
- Resource: `user`, `book`, `payment`, `loan`, dll

### 5. Document Your Permissions
Selalu dokumentasikan permission baru:

```php
// app/Permissions/PermissionList.php

class PermissionList
{
    const CREATE_USER = 'create-user'; // Membuat user baru
    const READ_USER = 'read-user';     // Melihat detail user
    const UPDATE_USER = 'update-user'; // Mengupdate user
    const DELETE_USER = 'delete-user'; // Menghapus user
}
```

### 6. Monitor Permission Checks
Selalu gunakan proper error handling:

```php
public function destroy($id)
{
    try {
        if (!auth()->user()->hasPermissionTo('delete-book')) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki izin menghapus buku'
            ], 403);
        }

        // Delete logic
        Book::destroy($id);

        return response()->json([
            'success' => true,
            'message' => 'Buku berhasil dihapus'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan: ' . $e->getMessage()
        ], 500);
    }
}
```

## Contoh Implementation Lengkap

### 1. Protect API Route
```php
// routes/api.php
Route::middleware('auth:sanctum')->group(function () {
    // Books endpoints
    Route::post('/books', [BookController::class, 'store'])
        ->middleware('permission:create-book');
    
    Route::get('/books', [BookController::class, 'index'])
        ->middleware('permission:list-books');
    
    Route::get('/books/{id}', [BookController::class, 'show'])
        ->middleware('permission:read-book');
    
    Route::put('/books/{id}', [BookController::class, 'update'])
        ->middleware('permission:update-book');
    
    Route::delete('/books/{id}', [BookController::class, 'destroy'])
        ->middleware('permission:delete-book');

    // Payments endpoints (Officer only)
    Route::post('/payments/{id}/approve', [PaymentController::class, 'approve'])
        ->middleware('role:officer');
});
```

### 2. Controller dengan Permission Check
```php
<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function store(Request $request)
    {
        // Middleware sudah mengecek permission
        $validated = $request->validate([
            'title' => 'required|string',
            'author' => 'required|string',
        ]);

        $book = Book::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Book created successfully',
            'data' => $book
        ], 201);
    }

    public function destroy(Book $book)
    {
        // Double check permission di controller
        $this->authorize('delete', $book);

        $book->delete();

        return response()->json([
            'success' => true,
            'message' => 'Book deleted successfully'
        ]);
    }
}
```

## Testing Permissions

```php
// tests/Feature/BookControllerTest.php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_book_if_has_permission()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('create-book');

        $response = $this->actingAs($user)->post('/api/books', [
            'title' => 'Test Book',
            'author' => 'John Doe',
        ]);

        $response->assertStatus(201);
    }

    public function test_user_cannot_create_book_without_permission()
    {
        $user = User::factory()->create();
        // User tidak memiliki permission

        $response = $this->actingAs($user)->post('/api/books', [
            'title' => 'Test Book',
            'author' => 'John Doe',
        ]);

        $response->assertStatus(403);
    }

    public function test_officer_can_approve_payment()
    {
        $officer = User::factory()->create();
        $officer->assignRole('officer');

        $response = $this->actingAs($officer)->post('/api/payments/1/approve');

        $response->assertStatus(200);
    }
}
```

## Troubleshooting

### 1. Permission Cache Issue
Jika permission masih belum muncul setelah dibuat:
```bash
php artisan permission:cache-reset
```

### 2. User Tidak Mewarisi Permission dari Role
Pastikan tabel `role_has_permissions` sudah tersync:
```bash
php artisan db:seed --class=RolePermissionSeeder
```

### 3. Middleware Not Working
Pastikan middleware sudah didaftarkan di `bootstrap/app.php`:
```php
$middleware->alias([
    'role' => \App\Http\Middleware\CheckRole::class,
    'permission' => \App\Http\Middleware\CheckPermission::class,
]);
```

## Resources Tambahan

- **Official Documentation**: https://spatie.be/docs/laravel-permission/v6/introduction
- **GitHub Repository**: https://github.com/spatie/laravel-permission
- **Laravel Authorization**: https://laravel.com/docs/authorization

---

**Last Updated**: January 21, 2026
