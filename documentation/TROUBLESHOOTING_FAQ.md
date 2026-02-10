# ❓ FAQ & Troubleshooting Guide

## 🚨 Common Issues & Solutions

### 1. "View [officer.packing.index] not found"

**Symptoms:**
```
Symfony\Component\Debug\Exception\FatalThrowableError
View [officer.packing.index] not found.
```

**Causes:**
- View files not created
- Wrong file path
- Cache not cleared

**Solutions:**
```bash
# Step 1: Check if files exist
ls -la resources/views/officer/packing/

# Step 2: Clear cache
php artisan view:clear

# Step 3: Verify routes
php artisan route:list | grep packing

# Step 4: If missing, create files manually
# Files should be in: resources/views/officer/packing/
# - index.blade.php
# - show.blade.php
```

---

### 2. "Insufficient units available"

**Symptoms:**
```json
{
  "success": false,
  "message": "Insufficient units available",
  "failures": [{"product": "Tenda Consina", "reason": "No available units"}]
}
```

**Causes:**
- No available units in database
- All units already assigned/booked
- Wrong product ID

**Solutions:**
```bash
# Step 1: Check available units
php artisan tinker
>>> Unit::where('status', 'available')->count()

# Step 2: Check units for specific product
>>> Unit::where('id_product', 'product-uuid')->where('status', 'available')->count()

# Step 3: Reset units (for testing)
>>> Unit::query()->update(['status' => 'available'])

# Step 4: Run seeder again
php artisan db:seed --class=UnitSeeder
```

---

### 3. "Serial number doesn't match"

**Symptoms:**
```json
{
  "success": false,
  "message": "❌ Serial tidak sesuai! Expected: TEN-005-WXYZ, Got: KMP-012-QRST"
}
```

**Causes:**
- Wrong unit scanned
- Unit assigned to different booking
- Typo in serial number

**Solutions:**
```bash
# Step 1: Verify correct unit
php artisan tinker
>>> $bookPackage = BookPackageProduct::find('uuid')
>>> $bookPackage->unit->serial_number

# Step 2: Check what unit was assigned
>>> dd($bookPackage->unit)

# Step 3: Manual re-assignment
>>> $bookPackage->update(['id_unit' => 'correct-unit-id'])
```

---

### 4. "Database transaction failed"

**Symptoms:**
```
PDOException: SQLSTATE[HY000]: General error
Database transaction failed / rolled back
```

**Causes:**
- Concurrent access conflicts
- Missing database constraints
- Connection timeout

**Solutions:**
```bash
# Step 1: Check database connection
php artisan tinker
>>> DB::connection()->getPdo()

# Step 2: Verify migrations ran
php artisan migrate:status

# Step 3: Re-run migrations
php artisan migrate:rollback
php artisan migrate

# Step 4: Check database logs
tail -f storage/logs/laravel.log
```

---

### 5. "Unit already packed"

**Symptoms:**
```json
{
  "success": false,
  "message": "This unit is already packed"
}
```

**Causes:**
- Unit already scanned/marked as packed
- Duplicate scan attempt

**Solutions:**
```bash
# Step 1: Check unit status
php artisan tinker
>>> $bp = BookPackageProduct::find('uuid')
>>> dd($bp->is_packed) // true = already packed

# Step 2: Reset for testing (admin only)
>>> $bp->update(['is_packed' => false, 'packed_at' => null, 'packed_by' => null])

# Step 3: Verify in UI - check for ✅ checkmark
```

---

### 6. "CSRF token mismatch"

**Symptoms:**
```
The CSRF token could not be verified
419 Page Expired
```

**Causes:**
- Missing CSRF token in form
- Session expired
- Token cache issue

**Solutions:**
```html
<!-- Add to forms -->
@csrf

<!-- Or in AJAX -->
headers: {
  'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
}

<!-- Or clear session -->
php artisan tinker
>>> \Illuminate\Support\Facades\Cache::flush()
```

---

### 7. "Officer role not found"

**Symptoms:**
```
SQLSTATE[42S02]: Table or view not found: 1146 Table 'role_has_permissions' doesn't exist
```

**Causes:**
- Spatie Permission not installed
- Migrations not run
- Role not created

**Solutions:**
```bash
# Step 1: Install Spatie Permission
composer require spatie/laravel-permission

# Step 2: Run migrations
php artisan migrate

# Step 3: Create officer role
php artisan tinker
>>> \Spatie\Permission\Models\Role::create(['name' => 'officer', 'guard_name' => 'web'])

# Step 4: Assign role to user
>>> $user->assignRole('officer')
```

---

### 8. "Map not loading"

**Symptoms:**
- Blank map area
- No tiles showing
- Map controls not visible

**Causes:**
- Leaflet.js not loaded
- CDN blocked
- JavaScript error

**Solutions:**
```bash
# Step 1: Check browser console (F12)
# Look for 404 errors on JS/CSS

# Step 2: Verify Leaflet CDN available
# Try opening in browser:
# https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.css

# Step 3: Check view source
view-source:http://localhost:8000/courier/route-map
# Should see Leaflet script tags

# Step 4: Clear browser cache
# Ctrl+Shift+Delete, clear all
```

---

### 9. "Permission denied" on packing page

**Symptoms:**
```
403 Forbidden - You are not authorized to perform this action
```

**Causes:**
- User doesn't have 'officer' role
- Middleware check failed
- User logged in as wrong role

**Solutions:**
```bash
# Step 1: Check user role
php artisan tinker
>>> $user = User::find('user-id')
>>> $user->roles

# Step 2: Assign officer role
>>> $user->assignRole('officer')

# Step 3: Check middleware in routes
# Should have: ->middleware(['auth:web', 'role:officer'])

# Step 4: Clear authentication cache
>>> Auth::logout()
# Then login again
```

---

### 10. "Paginate without numbers"

**Symptoms:**
```
Call to undefined method Illuminate\Pagination\Paginator::links()
```

**Causes:**
- Using simplePaginate instead of paginate
- Wrong pagination view configured

**Solutions:**
```php
// In controller, use:
$bookings = Book::paginate(10); // ← Use paginate, not simplePaginate

// In view:
{{ $bookings->links('pagination::tailwind') }}

// Or check config/app.php:
'pagination' => 'tailwind',
```

---

## 🔧 Diagnostic Commands

### Check System Status

```bash
# Database connection
php artisan tinker
>>> DB::connection()->getPdo()

# View all routes
php artisan route:list | grep packing

# Check migrations
php artisan migrate:status

# View logs
tail -f storage/logs/laravel.log

# Check permissions
php artisan tinker
>>> \Spatie\Permission\Models\Role::all()

# Count units
>>> Unit::count()
>>> Unit::where('status', 'available')->count()
```

### Debug Information

```php
// In controller or route closure:
\Illuminate\Support\Facades\DB::enableQueryLog();

// ... your code ...

dd(\Illuminate\Support\Facades\DB::getQueryLog());

// Output: All SQL queries executed
```

### Test Atomic Transaction

```bash
php artisan tinker

# Step 1: Create booking with products
>>> $book = Book::with('bookPackageProducts')->find('booking-id')

# Step 2: Check current units
>>> $book->bookPackageProducts->each(fn($bp) => dd($bp->id_product))

# Step 3: Test assignment
>>> $service = new \App\Services\AtomicAssignmentService()
>>> $result = $service->assignUnitsForPackage($book)
>>> dd($result)

# Step 4: Verify units locked
>>> Unit::where('status', 'booked')->count()
```

---

## ✅ Verification Checklist

Before going to production, verify:

- [ ] Database migrations ran: `php artisan migrate:status`
- [ ] Units seeded: `Unit::count()` returns 538
- [ ] Officer role exists: `Role::where('name', 'officer')->exists()`
- [ ] Routes configured: `php artisan route:list | grep packing`
- [ ] Views created: `ls resources/views/officer/packing/`
- [ ] Service working: `AtomicAssignmentService` imported
- [ ] Controller working: `OfficerPackingController` imported
- [ ] Navigation updated: Packing link in sidebar
- [ ] Tests passing: `php artisan test`

---

## 📊 Performance Tips

### Optimize Queries

```php
// ❌ Bad: N+1 query problem
foreach ($bookings as $booking) {
    $booking->bookPackageProducts; // Query in loop!
}

// ✅ Good: Use eager loading
$bookings = Book::with('bookPackageProducts.product', 'bookPackageProducts.unit')->get();

// ❌ Bad: Load all units
$units = Unit::all(); // Memory heavy if many units

// ✅ Good: Pagination
$units = Unit::paginate(50);

// ✅ Good: Specific columns
$units = Unit::select('id', 'serial_number', 'status')->get();
```

### Database Indexes

```php
// Verify indexes exist
>>> DB::select("SHOW INDEXES FROM units")
>>> DB::select("SHOW INDEXES FROM book_package_products")

// Should see:
// - Unique index on units.serial_number
// - Index on units(id_product, status)
// - Index on book_package_products.id_unit
```

### Cache Configuration

```bash
# In .env
CACHE_DRIVER=redis
SESSION_DRIVER=redis

# Or use database
CACHE_DRIVER=database
SESSION_DRIVER=database

# Create cache table
php artisan cache:table
php artisan migrate
```

---

## 🐛 Enable Debug Mode

### Development Environment

```bash
# .env
APP_ENV=local
APP_DEBUG=true

# See detailed errors in browser
```

### Enable Query Logging

```php
// In routes/web.php or controller
\DB::enableQueryLog();

// At end of request:
if (config('app.debug')) {
    dd(\DB::getQueryLog());
}
```

### Enable Blade Debugging

```php
// In config/app.php
'debug' => env('APP_DEBUG', true),

// Then use in blade:
@dump($variable)
@dd($variable)
```

---

## 📞 Getting Help

### Resources
1. Check [TECHNICAL_DEVELOPER_GUIDE.md](TECHNICAL_DEVELOPER_GUIDE.md)
2. Check [API_DOCUMENTATION.md](API_DOCUMENTATION.md)
3. Check Laravel docs: https://laravel.com/docs/11
4. Check Spatie Permission: https://spatie.be/docs/laravel-permission

### Common Searches

```
Laravel database transaction:
https://laravel.com/docs/11.x/database#database-transactions

Eloquent locking:
https://laravel.com/docs/11.x/eloquent#pessimistic-locking

Blade templating:
https://laravel.com/docs/11.x/blade
```

---

## 🎓 Learning Path

### For New Developers

1. **Understand Database Schema**
   - Read: `database/migrations/2026_02_09_*.php`
   - Try: `php artisan tinker` + query units

2. **Understand Models & Relationships**
   - Read: `app/Models/Unit.php`
   - Read: `app/Models/BookPackageProduct.php`
   - Try: Load with relationships

3. **Understand Service Layer**
   - Read: `app/Services/AtomicAssignmentService.php`
   - Understand: Atomic transactions & locking
   - Try: Test assignment logic

4. **Understand Controller & Routes**
   - Read: `app/Http/Controllers/OfficerPackingController.php`
   - Read: `routes/web.php` packing routes
   - Try: Call endpoints manually

5. **Understand Views**
   - Read: `resources/views/officer/packing/index.blade.php`
   - Read: `resources/views/officer/packing/show.blade.php`
   - Try: Modify UI elements

---

## 🚀 Quick Fixes Cheatsheet

| Issue | Quick Fix |
|-------|-----------|
| View not found | `php artisan view:clear` |
| Migrations failed | `php artisan migrate:rollback && php artisan migrate` |
| Seeding failed | `php artisan db:seed --class=UnitSeeder` |
| Auth failed | Assign role: `$user->assignRole('officer')` |
| Cache stale | `php artisan cache:clear` |
| Routes cached | `php artisan route:clear` |
| Config cached | `php artisan config:clear` |
| Logs full | `rm storage/logs/*.log` |
| Unit test failed | Check database: `php artisan test --env=testing` |

---

## 📋 Maintenance Schedule

### Daily
- [ ] Check error logs: `tail storage/logs/laravel.log`
- [ ] Monitor database size

### Weekly
- [ ] Backup database
- [ ] Check unit inventory
- [ ] Review packing stats

### Monthly
- [ ] Cleanup old logs
- [ ] Update dependencies: `composer update`
- [ ] Run full test suite: `php artisan test`

### Yearly
- [ ] Major security audit
- [ ] Performance optimization review
- [ ] Backup infrastructure review

---

**Last Updated:** February 9, 2026
**Version:** 1.0
**Status:** ✅ Complete & Helpful
