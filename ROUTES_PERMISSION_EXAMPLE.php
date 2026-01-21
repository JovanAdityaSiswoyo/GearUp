<?php

/**
 * EXAMPLE ROUTES WITH SPATIE PERMISSION
 * 
 * Contoh implementasi routes dengan Spatie Laravel Permission
 * Gunakan sebagai referensi untuk melindungi endpoint dengan roles dan permissions
 */

use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    
    // ==================== BOOK ROUTES ====================
    Route::prefix('books')->group(function () {
        // List books - Requires: list-books permission
        Route::get('/', [BookController::class, 'index'])
            ->middleware('permission:list-books')
            ->name('books.index');

        // Show book - Requires: read-book permission
        Route::get('/{id}', [BookController::class, 'show'])
            ->middleware('permission:read-book')
            ->name('books.show');

        // Create book - Requires: create-book permission (Admin/Super-admin only)
        Route::post('/', [BookController::class, 'store'])
            ->middleware('permission:create-book')
            ->name('books.store');

        // Update book - Requires: update-book permission (Admin/Super-admin only)
        Route::put('/{id}', [BookController::class, 'update'])
            ->middleware('permission:update-book')
            ->name('books.update');

        // Delete book - Requires: delete-book permission (Super-admin only)
        Route::delete('/{id}', [BookController::class, 'destroy'])
            ->middleware('permission:delete-book')
            ->name('books.destroy');

        // Bulk delete - Requires: delete-book permission and super-admin role
        Route::post('/bulk-delete', [BookController::class, 'bulkDelete'])
            ->middleware(['permission:delete-book', 'role:super-admin'])
            ->name('books.bulkDelete');
    });

    // ==================== PAYMENT ROUTES ====================
    Route::prefix('payments')->group(function () {
        // List payments
        Route::get('/', [PaymentController::class, 'index'])
            ->middleware('permission:list-payments')
            ->name('payments.index');

        // Show payment
        Route::get('/{id}', [PaymentController::class, 'show'])
            ->middleware('permission:read-payment')
            ->name('payments.show');

        // Create payment - Officer can create
        Route::post('/', [PaymentController::class, 'store'])
            ->middleware('permission:create-payment')
            ->name('payments.store');

        // Update payment - Admin only
        Route::put('/{id}', [PaymentController::class, 'update'])
            ->middleware('permission:update-payment')
            ->name('payments.update');

        // Delete payment - Super-admin only
        Route::delete('/{id}', [PaymentController::class, 'destroy'])
            ->middleware(['permission:delete-payment', 'role:super-admin'])
            ->name('payments.destroy');

        // Approve payment - Officer role
        Route::post('/{id}/approve', [PaymentController::class, 'approve'])
            ->middleware(['permission:approve-payment', 'role:officer'])
            ->name('payments.approve');
    });

    // ==================== USER MANAGEMENT ROUTES ====================
    Route::prefix('users')->middleware('role:admin,super-admin')->group(function () {
        // List users - Admin only
        Route::get('/', [UserController::class, 'index'])
            ->middleware('permission:list-users')
            ->name('users.index');

        // Show user - Admin only
        Route::get('/{id}', [UserController::class, 'show'])
            ->middleware('permission:read-user')
            ->name('users.show');

        // Create user - Admin/Super-admin only
        Route::post('/', [UserController::class, 'store'])
            ->middleware('permission:create-user')
            ->name('users.store');

        // Update user - Admin/Super-admin only
        Route::put('/{id}', [UserController::class, 'update'])
            ->middleware('permission:update-user')
            ->name('users.update');

        // Delete user - Super-admin only
        Route::delete('/{id}', [UserController::class, 'destroy'])
            ->middleware('permission:delete-user')
            ->name('users.destroy');

        // Assign role to user - Super-admin only
        Route::post('/{id}/assign-role', [UserController::class, 'assignRole'])
            ->middleware(['permission:manage-roles', 'role:super-admin'])
            ->name('users.assignRole');

        // Remove role from user - Super-admin only
        Route::post('/{id}/remove-role', [UserController::class, 'removeRole'])
            ->middleware(['permission:manage-roles', 'role:super-admin'])
            ->name('users.removeRole');
    });

    // ==================== DASHBOARD & REPORTS ====================
    
    // Dashboard - All authenticated users
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('permission:view-dashboard')
        ->name('dashboard');

    // Analytics - Admin/Officer
    Route::get('/analytics', [AnalyticsController::class, 'index'])
        ->middleware('permission:view-analytics')
        ->name('analytics');

    // Reports - View reports
    Route::get('/reports', [ReportController::class, 'index'])
        ->middleware('permission:view-reports')
        ->name('reports.index');

    // Export reports - Admin only
    Route::get('/reports/export', [ReportController::class, 'export'])
        ->middleware(['permission:export-reports', 'role:admin'])
        ->name('reports.export');

    // ==================== ROLE MANAGEMENT ====================
    Route::prefix('roles')->middleware(['permission:manage-roles', 'role:super-admin'])->group(function () {
        // List all roles
        Route::get('/', [RoleController::class, 'index'])
            ->name('roles.index');

        // Get role with permissions
        Route::get('/{id}', [RoleController::class, 'show'])
            ->name('roles.show');

        // Create new role
        Route::post('/', [RoleController::class, 'store'])
            ->name('roles.store');

        // Update role
        Route::put('/{id}', [RoleController::class, 'update'])
            ->name('roles.update');

        // Delete role
        Route::delete('/{id}', [RoleController::class, 'destroy'])
            ->name('roles.destroy');

        // Assign permission to role
        Route::post('/{id}/assign-permission', [RoleController::class, 'assignPermission'])
            ->name('roles.assignPermission');

        // Revoke permission from role
        Route::post('/{id}/revoke-permission', [RoleController::class, 'revokePermission'])
            ->name('roles.revokePermission');
    });

    // ==================== PERMISSION MANAGEMENT ====================
    Route::prefix('permissions')->middleware(['permission:manage-permissions', 'role:super-admin'])->group(function () {
        // List all permissions
        Route::get('/', [PermissionController::class, 'index'])
            ->name('permissions.index');

        // Get permission details
        Route::get('/{id}', [PermissionController::class, 'show'])
            ->name('permissions.show');
    });

    // ==================== USER PROFILE ====================
    
    // Get current user data
    Route::get('/me', [UserController::class, 'getCurrentUser'])
        ->name('user.profile');

    // Get current user permissions and roles
    Route::get('/me/permissions', [UserController::class, 'getUserPermissions'])
        ->name('user.permissions');

    // Update own profile
    Route::put('/me', [UserController::class, 'updateProfile'])
        ->name('user.updateProfile');

    // Change password
    Route::post('/me/change-password', [UserController::class, 'changePassword'])
        ->name('user.changePassword');
});

/**
 * NOTES:
 * 
 * 1. MIDDLEWARE USAGE:
 *    - middleware('permission:action-resource') - Check specific permission
 *    - middleware('role:admin') - Check specific role
 *    - middleware('role:admin,officer') - Check multiple roles (OR)
 *    - middleware(['permission:create-book', 'role:admin']) - Multiple checks
 * 
 * 2. PROTECTION LEVELS:
 *    - Read operations: Required basic permission (list-books, read-book)
 *    - Create operations: Typically admin or specific role
 *    - Update operations: Admin role or specific permission
 *    - Delete operations: Usually super-admin only
 * 
 * 3. PERMISSION HIERARCHY:
 *    - Super Admin: All permissions
 *    - Admin: Manage resources, view reports
 *    - Officer: Manage transactions, approve payments
 *    - User: Read-only access
 * 
 * 4. BEST PRACTICES:
 *    - Always check permission in controller too (defense in depth)
 *    - Log permission denials for security audit
 *    - Clear cache after role/permission changes
 *    - Test permissions thoroughly before deployment
 * 
 * 5. ERROR RESPONSES:
 *    - 401 Unauthorized: User not authenticated
 *    - 403 Forbidden: User lacks required permission/role
 *    - 422 Unprocessable Entity: Validation failed
 *    - 500 Server Error: Unexpected error
 */
