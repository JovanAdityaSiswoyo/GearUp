# 🔐 Spatie Laravel Permission - Architecture Overview

## 📊 System Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                      API REQUEST                             │
└────────────┬────────────────────────────────────────────────┘
             │
             ▼
┌─────────────────────────────────────────────────────────────┐
│                   Authentication                             │
│             (sanctum:api-token middleware)                   │
└────────────┬────────────────────────────────────────────────┘
             │
             ▼
┌─────────────────────────────────────────────────────────────┐
│              Role/Permission Middleware                      │
│                                                              │
│  middleware('role:admin,officer')  ← Check Role            │
│  middleware('permission:create-book')  ← Check Permission   │
└────────────┬────────────────────────────────────────────────┘
             │
             ▼
   ┌─────────────────────┐
   │   200 OK - Allowed  │  (Has required role/permission)
   │   403 Forbidden     │  (Lacks required access)
   │   401 Unauthorized  │  (Not authenticated)
   └─────────────────────┘
```

---

## 🗂️ Project Structure

### Models Layer
```
app/Models/
├── User.php
│   └── Uses: HasRoles trait
│       Methods: assignRole(), hasRole(), hasPermissionTo()
│
├── Admin.php
│   └── Uses: HasRoles trait
│
└── Officer.php
    └── Uses: HasRoles trait
```

### Middleware Layer
```
app/Http/Middleware/
├── CheckRole.php
│   └── Validates: User has required role(s)
│       Usage: middleware('role:admin,officer')
│
└── CheckPermission.php
    └── Validates: User has required permission(s)
        Usage: middleware('permission:create-book')
```

### Routes Layer
```
routes/
├── api.php (Protected with middleware)
│   ├── POST /books - middleware('permission:create-book')
│   ├── GET /books - middleware('permission:list-books')
│   ├── PUT /books/{id} - middleware('permission:update-book')
│   └── DELETE /books/{id} - middleware('role:super-admin')
│
└── Example: ROUTES_PERMISSION_EXAMPLE.php
```

### Controllers Layer
```
app/Http/Controllers/Api/
├── BookController.php
│   └── Methods check permissions before operations
│       Double check: if (!$request->user()->hasPermissionTo(...))
│
└── BookControllerWithPermission.php (Full example)
```

### Database Layer
```
database/
├── migrations/
│   └── 2026_01_21_034521_create_permission_tables.php
│       Creates: permissions, roles, role_has_permissions,
│                model_has_roles, model_has_permissions
│
└── seeders/
    └── RolePermissionSeeder.php
        Seeds: 4 roles, 40+ permissions, associations
```

---

## 👥 Roles Hierarchy

```
┌───────────────────────────────────────────────────────────┐
│                   ROLE HIERARCHY                          │
└───────────────────────────────────────────────────────────┘

SUPER-ADMIN (Full Control)
├── All 40+ permissions
├── Can create, read, update, delete any resource
├── Can manage roles and permissions
└── Can approve/reject all transactions

    │
    ▼

ADMIN (Administrative Access)
├── Read + Update all resources
├── View dashboard, reports, analytics
├── Approve/reject limited transactions
└── Cannot: Create/Delete resources, Delete users

    │
    ▼

OFFICER (Transaction Manager)
├── Read all resources
├── Create payments, loans
├── Approve/reject transactions
├── View dashboard, reports
└── Cannot: Modify user data, Delete anything

    │
    ▼

USER (Read-Only Access)
├── Read books, products, packages, categories
├── View dashboard
└── Cannot: Create/Edit/Delete, Approve, Manage

```

---

## 🔐 Permission Categories

```
┌─────────────────────────────────────────────────────────┐
│              PERMISSION STRUCTURE                        │
└─────────────────────────────────────────────────────────┘

RESOURCE PERMISSIONS (Pattern: action-resource)

Books (5 perms)        Users (5 perms)       Products (5 perms)
├── create-book       ├── create-user       ├── create-product
├── read-book         ├── read-user         ├── read-product
├── update-book       ├── update-user       ├── update-product
├── delete-book       ├── delete-user       ├── delete-product
└── list-books        └── list-users        └── list-products

Categories (5)        Packages (5)          Payments (6)
├── create-category   ├── create-package    ├── create-payment
├── read-category     ├── read-package      ├── read-payment
├── update-category   ├── update-package    ├── update-payment
├── delete-category   ├── delete-package    ├── delete-payment
└── list-categories   └── list-packages     ├── list-payments
                                            └── approve-payment

Loans (7)             Reports (4)           System (2)
├── create-loan       ├── view-reports      ├── manage-roles
├── read-loan         ├── export-reports    └── manage-permissions
├── update-loan       └── view-analytics
├── delete-loan
├── list-loans
├── approve-loan
└── reject-loan

TOTAL: 40+ Permissions
```

---

## 🔄 Request Flow Diagram

```
1. CLIENT SENDS REQUEST
   ↓
   POST /api/books
   Headers: {Authorization: Bearer token}
   Body: {book_code: "B001", ...}
   
2. LARAVEL ROUTING
   ↓
   Middleware stack: ['auth:sanctum', 'permission:create-book']
   
3. AUTHENTICATION CHECK
   ↓
   Does user have valid token? → NO → 401 Unauthorized
   → YES (continue)
   
4. PERMISSION CHECK (Middleware)
   ↓
   Does user have 'create-book' permission?
   → NO → 403 Forbidden (Middleware stops request)
   → YES (continue to controller)
   
5. CONTROLLER METHOD
   ↓
   Additional permission check: if (!$user->hasPermissionTo(...))
   → NO → Return 403 error response
   → YES (continue)
   
6. BUSINESS LOGIC
   ↓
   Execute: Create book in database
   
7. RESPONSE
   ↓
   Return: {success: true, data: {...}} (200 OK)
```

---

## 💾 Database Schema

```
┌──────────────────┐      ┌──────────────────┐
│   users          │      │    permissions   │
├──────────────────┤      ├──────────────────┤
│ id (PK)          │      │ id (PK)          │
│ name             │      │ name             │
│ email            │      │ guard_name       │
│ password         │      │ created_at       │
└──────┬───────────┘      └────────┬─────────┘
       │                           │
       │                           │
       │  ┌───────────────────────┐│
       │  │ role_has_permissions  ││
       │  ├───────────────────────┤│
       │  │ role_id (FK)          ││
       │  │ permission_id (FK) ───┼┘
       │  └───────────────────────┘
       │           ▲
       │           │
       │  ┌────────┴──────────────┐
       │  │  model_has_roles      │
       │  ├───────────────────────┤
       │  │ model_id (FK) ────────┼─→ users.id
       │  │ role_id (FK)
       │  │ model_type            │
       │  └───────────────────────┘
       │           ▲
       │           │
       │  ┌────────┴──────────────────┐
       │  │ model_has_permissions     │
       │  ├───────────────────────────┤
       │  │ model_id (FK) ────────────┼─→ users.id
       │  │ permission_id (FK)
       │  │ model_type                │
       │  └───────────────────────────┘
       │
       └──→ (Direct permission assignment or via roles)
```

---

## 🎯 Usage Examples by Layer

### 1. MODELS LAYER - Assign Roles
```php
// app/Models/User.php (uses HasRoles trait)

$user = User::find(1);

// Assign role
$user->assignRole('admin');

// Get roles
$user->getRoleNames(); // ['admin']

// Check role
$user->hasRole('admin'); // true

// Get permissions (via role)
$user->getPermissionNames(); 
// ['read-book', 'list-books', 'update-book', ...]
```

### 2. MIDDLEWARE LAYER - Protect Routes
```php
// routes/api.php

Route::post('/books', [BookController::class, 'store'])
    ->middleware('permission:create-book');

Route::delete('/users/{id}', [UserController::class, 'destroy'])
    ->middleware('role:super-admin');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware('permission:view-dashboard');
```

### 3. CONTROLLER LAYER - Additional Checks
```php
// app/Http/Controllers/Api/BookController.php

public function destroy(Request $request, Book $book)
{
    // Middleware checked permission, but double-check in controller
    if (!$request->user()->hasPermissionTo('delete-book')) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    // Delete logic
    $book->delete();
    
    return response()->json(['success' => true]);
}
```

### 4. BLADE TEMPLATES - Conditional UI
```blade
<!-- resources/views/books/index.blade.php -->

@can('create-book')
    <button class="btn btn-primary" onclick="newBook()">
        Add New Book
    </button>
@endcan

@role('admin|super-admin')
    <div class="admin-section">
        <a href="/admin/reports">View Reports</a>
    </div>
@endrole

@cannot('delete-book')
    <p class="text-muted">You cannot delete books</p>
@endcannot
```

---

## 📦 Database Tables Created

```
permissions
├── id: bigint
├── name: varchar (e.g., 'create-book')
├── guard_name: varchar (default: 'api')
└── created_at, updated_at: timestamp

roles
├── id: bigint
├── name: varchar (e.g., 'admin')
├── guard_name: varchar (default: 'api')
└── created_at, updated_at: timestamp

role_has_permissions
├── permission_id: bigint (FK → permissions.id)
├── role_id: bigint (FK → roles.id)
└── Primary key: (permission_id, role_id)

model_has_roles
├── role_id: bigint (FK → roles.id)
├── model_type: varchar (e.g., 'App\Models\User')
├── model_id: uuid (FK → users.id)
└── Primary key: (role_id, model_type, model_id)

model_has_permissions
├── permission_id: bigint (FK → permissions.id)
├── model_type: varchar (e.g., 'App\Models\User')
├── model_id: uuid (FK → users.id)
└── Primary key: (permission_id, model_type, model_id)
```

---

## 🚀 Implementation Sequence

```
STEP 1: INSTALLATION
├── composer require spatie/laravel-permission ✅
└── php artisan vendor:publish --provider="..." ✅

STEP 2: DATABASE SETUP
├── php artisan migrate
└── php artisan db:seed --class=RolePermissionSeeder

STEP 3: MODEL UPDATES
├── Add HasRoles trait to User ✅
├── Add HasRoles trait to Admin ✅
└── Add HasRoles trait to Officer ✅

STEP 4: MIDDLEWARE
├── Create CheckRole.php ✅
├── Create CheckPermission.php ✅
└── Register aliases in bootstrap/app.php ✅

STEP 5: ROUTES PROTECTION
├── Identify which routes need protection
├── Add middleware to routes
└── Update route handlers

STEP 6: CONTROLLER ENHANCEMENT
├── Add permission checks in methods
├── Return proper error responses (403)
└── Log permission denials

STEP 7: TESTING
├── Write unit tests
├── Test each role (super-admin, admin, officer, user)
└── Test error cases (401, 403)

STEP 8: DEPLOYMENT
├── Run migrations in production
├── Seed roles & permissions
├── Monitor permission denials
└── Update logs/monitoring
```

---

## 📊 Files Created Summary

```
MIDDLEWARE (2 files)
├── app/Http/Middleware/CheckRole.php (45 lines)
└── app/Http/Middleware/CheckPermission.php (45 lines)

SEEDER (1 file)
└── database/seeders/RolePermissionSeeder.php (180 lines)

DOCUMENTATION (5 files)
├── SPATIE_PERMISSION_GUIDE.md (400+ lines)
├── SPATIE_PERMISSION_QUICK_REF.md (250+ lines)
├── IMPLEMENTATION_SUMMARY.md (300+ lines)
├── IMPLEMENTATION_CHECKLIST.md (400+ lines)
└── ARCHITECTURE_OVERVIEW.md (this file)

EXAMPLES (3 files)
├── app/Http/Controllers/Api/BookControllerWithPermission.php (200+ lines)
├── ROUTES_PERMISSION_EXAMPLE.php (200+ lines)
└── PERMISSION_TESTS_EXAMPLE.php (400+ lines)

MODELS UPDATED (3 files)
├── app/Models/User.php ✅
├── app/Models/Admin.php ✅
└── app/Models/Officer.php ✅

CONFIG & MIGRATIONS (2 files - auto-published)
├── config/permission.php
└── database/migrations/2026_01_21_*.php

TOTAL: 16 files (11 created, 3 modified, 2 auto-published)
TOTAL DOCUMENTATION: 1000+ lines
```

---

## ✅ Verification Checklist

```
Installation
├── [✅] Package installed (spatie/laravel-permission)
├── [✅] Config published
├── [✅] Middleware created
├── [✅] Seeder created
└── [✅] Models updated

Configuration
├── [✅] Middleware registered in bootstrap/app.php
├── [✅] 4 roles configured
├── [✅] 40+ permissions defined
└── [✅] Role-permission assignments created

Documentation
├── [✅] Full guide (SPATIE_PERMISSION_GUIDE.md)
├── [✅] Quick reference (SPATIE_PERMISSION_QUICK_REF.md)
├── [✅] Implementation checklist
├── [✅] Example routes
├── [✅] Example controller
├── [✅] Example tests
└── [✅] Architecture overview

Ready for Next Step
├── [ ] Database connection established
├── [ ] php artisan migrate executed
├── [ ] php artisan db:seed --class=RolePermissionSeeder executed
├── [ ] Routes protected with middleware
├── [ ] Controllers updated with permission checks
└── [ ] Tests written and passing
```

---

## 🎓 Key Concepts

### 1. Roles vs Permissions
- **Role**: Collection of permissions (e.g., 'admin')
- **Permission**: Specific action allowed (e.g., 'create-book')
- **User has Role → User gets all Role's Permissions**

### 2. Middleware Protection
- Checks happen before request reaches controller
- Returns 403 if permission denied
- Stops request execution immediately

### 3. Defense in Depth
- Check 1: Route middleware
- Check 2: Controller method
- Check 3: Blade templates (UI only)

### 4. Cache Strategy
- Permissions cached for performance
- Clear cache after role/permission changes
- Command: `php artisan permission:cache-reset`

### 5. Testing Requirements
- Test each role
- Test positive cases (allowed)
- Test negative cases (denied)
- Test edge cases

---

## 🔗 Relationships

```
User (1) ──┐
           │
           ├─→ (M) model_has_roles ─→ (1) Role
           │
           └─→ (M) model_has_permissions ─→ (1) Permission

Role (1) ──┐
           └─→ (M) role_has_permissions ─→ (1) Permission

Permission can be assigned:
├── Directly to User (via model_has_permissions)
└── Via Role (User → Role → Permission)
```

---

## 📈 Performance Considerations

### Caching
- Permissions cached automatically by Spatie
- Improves performance for repeated checks
- Clear cache when roles/permissions change

### Database Queries
- First check: 1 query (get user with roles)
- Subsequent checks: Use cache (no queries)
- Optimize with eager loading: `with('roles', 'permissions')`

### Best Practices
```php
// GOOD: Load all at once
$user->load('roles.permissions');
$user->hasPermissionTo('create-book');

// AVOID: Multiple queries
if ($user->hasRole('admin')) { // Query 1
    if ($user->hasPermissionTo('create-book')) { // Query 2
        // ...
    }
}
```

---

**Architecture Overview v1.0**  
Created: January 21, 2026  
Status: Complete & Ready to Use
