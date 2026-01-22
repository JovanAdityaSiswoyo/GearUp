# Spatie Permission - Quick Reference

Panduan cepat menggunakan Spatie Laravel Permission dalam project AplikasiPinjam.

## Installation ✅
```bash
composer require spatie/laravel-permission
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan migrate
php artisan db:seed --class=RolePermissionSeeder
```

## Quick Commands

### Assign Role to User
```php
$user->assignRole('admin');
$user->assignRole(['admin', 'officer']);
```

### Check Role
```php
$user->hasRole('admin');
$user->hasAnyRole(['admin', 'officer']);
$user->hasAllRoles(['admin', 'officer']);
```

### Give Direct Permission
```php
$user->givePermissionTo('create-book');
$user->givePermissionTo(['create-book', 'delete-book']);
```

### Check Permission
```php
$user->hasPermissionTo('create-book');
$user->hasAnyPermission(['create-book', 'update-book']);
$user->hasAllPermissions(['create-book', 'update-book']);
```

### Remove Role/Permission
```php
$user->removeRole('officer');
$user->revokePermissionTo('create-book');
$user->syncRoles(['user']); // Replace all roles
$user->syncPermissions(['read-book']); // Replace all permissions
```

## Middleware Protection

### Routes File Example
```php
Route::post('/books', [BookController::class, 'store'])
    ->middleware('permission:create-book');

Route::delete('/books/{id}', [BookController::class, 'destroy'])
    ->middleware('role:admin,super-admin');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware('permission:view-dashboard');
```

### Multiple Middleware
```php
Route::post('/users', [UserController::class, 'store'])
    ->middleware(['auth:sanctum', 'permission:create-user']);
```

## Controller Examples

### Check Permission in Controller
```php
public function store(Request $request)
{
    if ($request->user()->cannot('create-book')) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }
    
    // Create logic
}
```

### Check Role in Controller
```php
public function destroy($id)
{
    if (!auth()->user()->hasRole('admin')) {
        abort(403, 'Admin only');
    }
    
    // Delete logic
}
```

## Blade Template Examples

### Permission Directives
```blade
@can('create-book')
    <button class="btn btn-primary">Create Book</button>
@endcan

@cannot('delete-book')
    <p>You cannot delete books</p>
@endcannot
```

### Role Directives
```blade
@role('admin')
    <div class="admin-panel">Admin Panel</div>
@endrole

@role('officer|admin')
    <p>Officer or Admin Content</p>
@endrole
```

## Available Roles

| Role | Description |
|------|-------------|
| `super-admin` | Full access to everything |
| `admin` | Can manage users, read/update all resources |
| `officer` | Can manage payments and loans, approve transactions |
| `user` | Can only read books, products, packages |

## Available Permissions

### User Permissions
- `create-user`, `read-user`, `update-user`, `delete-user`, `list-users`

### Book Permissions
- `create-book`, `read-book`, `update-book`, `delete-book`, `list-books`

### Product Permissions
- `create-product`, `read-product`, `update-product`, `delete-product`, `list-products`

### Category Permissions
- `create-category`, `read-category`, `update-category`, `delete-category`, `list-categories`

### Package Permissions
- `create-package`, `read-package`, `update-package`, `delete-package`, `list-packages`

### Payment Permissions
- `create-payment`, `read-payment`, `update-payment`, `delete-payment`, `list-payments`, `approve-payment`

### Loan Permissions
- `create-loan`, `read-loan`, `update-loan`, `delete-loan`, `list-loans`, `approve-loan`, `reject-loan`

### Other Permissions
- `view-reports`, `export-reports`, `manage-roles`, `manage-permissions`, `view-dashboard`, `view-analytics`

## Using Tinker

```bash
php artisan tinker
```

```php
// Find user
$user = App\Models\User::first();

// Assign role
$user->assignRole('admin');

// Give permission
$user->givePermissionTo('create-book');

// Check
$user->hasRole('admin');
$user->hasPermissionTo('create-book');

// Get all
$user->getRoleNames();
$user->getPermissionNames();
```

## Cache Issues

If permissions not updating:
```bash
php artisan permission:cache-reset
```

Or in code:
```php
app()['cache']->forget('spatie.permission.cache');
```

## API Response Examples

### Success Response
```json
{
    "success": true,
    "message": "Book created successfully",
    "data": { ... }
}
```

### Permission Denied Response
```json
{
    "message": "You do not have permission to perform this action"
}
// Status Code: 403
```

### Unauthorized Response
```json
{
    "message": "Unauthorized"
}
// Status Code: 401
```

## Files Modified/Created

- `app/Models/User.php` - Added `HasRoles` trait
- `app/Models/Admin.php` - Added `HasRoles` trait
- `app/Models/Officer.php` - Added `HasRoles` trait
- `app/Http/Middleware/CheckRole.php` - New middleware
- `app/Http/Middleware/CheckPermission.php` - New middleware
- `bootstrap/app.php` - Registered middleware aliases
- `database/seeders/RolePermissionSeeder.php` - Seeder for roles & permissions
- `config/permission.php` - Spatie configuration file
- `database/migrations/2026_01_21_034521_create_permission_tables.php` - Spatie migrations

## Documentation

Full documentation: [SPATIE_PERMISSION_GUIDE.md](SPATIE_PERMISSION_GUIDE.md)

---
**Quick Reference v1.0** - January 21, 2026
