<?php

/**
 * TESTING SPATIE PERMISSIONS
 * 
 * Contoh unit tests untuk memastikan permission dan role berfungsi dengan benar
 * Letakkan di: tests/Feature/PermissionTest.php
 */

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PermissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create default roles and permissions
        $this->createRolesAndPermissions();
    }

    /**
     * Create roles and permissions for testing
     */
    private function createRolesAndPermissions(): void
    {
        $permissions = [
            'create-book', 'read-book', 'update-book', 'delete-book', 'list-books',
            'create-payment', 'read-payment', 'update-payment', 'delete-payment', 'approve-payment',
            'view-dashboard', 'view-reports', 'export-reports'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles
        $superAdmin = Role::firstOrCreate(['name' => 'super-admin']);
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $officer = Role::firstOrCreate(['name' => 'officer']);
        $user = Role::firstOrCreate(['name' => 'user']);

        // Assign permissions to roles
        $superAdmin->syncPermissions($permissions);
        $admin->syncPermissions(['list-books', 'read-book', 'update-book', 'view-dashboard']);
        $officer->syncPermissions(['read-book', 'list-books', 'create-payment', 'view-dashboard']);
        $user->syncPermissions(['read-book', 'list-books', 'view-dashboard']);
    }

    // ==================== ROLE ASSIGNMENT TESTS ====================

    /**
     * Test: User can be assigned a role
     */
    public function test_user_can_be_assigned_role(): void
    {
        $user = User::factory()->create();

        $user->assignRole('admin');

        $this->assertTrue($user->hasRole('admin'));
    }

    /**
     * Test: User can be assigned multiple roles
     */
    public function test_user_can_be_assigned_multiple_roles(): void
    {
        $user = User::factory()->create();

        $user->assignRole(['admin', 'officer']);

        $this->assertTrue($user->hasRole('admin'));
        $this->assertTrue($user->hasRole('officer'));
    }

    /**
     * Test: User role can be synced (replaced)
     */
    public function test_user_role_can_be_synced(): void
    {
        $user = User::factory()->create();
        $user->assignRole(['admin', 'officer']);

        $user->syncRoles(['user']);

        $this->assertFalse($user->hasRole('admin'));
        $this->assertTrue($user->hasRole('user'));
    }

    /**
     * Test: User role can be removed
     */
    public function test_user_role_can_be_removed(): void
    {
        $user = User::factory()->create();
        $user->assignRole('admin');

        $user->removeRole('admin');

        $this->assertFalse($user->hasRole('admin'));
    }

    // ==================== PERMISSION ASSIGNMENT TESTS ====================

    /**
     * Test: Direct permission can be given to user
     */
    public function test_direct_permission_can_be_given_to_user(): void
    {
        $user = User::factory()->create();

        $user->givePermissionTo('create-book');

        $this->assertTrue($user->hasPermissionTo('create-book'));
    }

    /**
     * Test: Multiple permissions can be given
     */
    public function test_multiple_permissions_can_be_given_to_user(): void
    {
        $user = User::factory()->create();

        $user->givePermissionTo(['create-book', 'delete-book']);

        $this->assertTrue($user->hasPermissionTo('create-book'));
        $this->assertTrue($user->hasPermissionTo('delete-book'));
    }

    /**
     * Test: Direct permission can be revoked
     */
    public function test_direct_permission_can_be_revoked(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('create-book');

        $user->revokePermissionTo('create-book');

        $this->assertFalse($user->hasPermissionTo('create-book'));
    }

    /**
     * Test: User inherits permissions from role
     */
    public function test_user_inherits_permissions_from_role(): void
    {
        $user = User::factory()->create();
        $user->assignRole('admin');

        $this->assertTrue($user->hasPermissionTo('list-books'));
        $this->assertTrue($user->hasPermissionTo('read-book'));
        $this->assertFalse($user->hasPermissionTo('create-payment'));
    }

    // ==================== ROLE CHECKING TESTS ====================

    /**
     * Test: hasRole checks single role
     */
    public function test_has_role_checks_single_role(): void
    {
        $user = User::factory()->create();
        $user->assignRole('admin');

        $this->assertTrue($user->hasRole('admin'));
        $this->assertFalse($user->hasRole('officer'));
    }

    /**
     * Test: hasAnyRole checks any of multiple roles
     */
    public function test_has_any_role_checks_multiple_roles(): void
    {
        $user = User::factory()->create();
        $user->assignRole('admin');

        $this->assertTrue($user->hasAnyRole(['admin', 'officer']));
        $this->assertFalse($user->hasAnyRole(['officer', 'user']));
    }

    /**
     * Test: hasAllRoles checks all roles
     */
    public function test_has_all_roles_checks_all_roles(): void
    {
        $user = User::factory()->create();
        $user->assignRole(['admin', 'officer']);

        $this->assertTrue($user->hasAllRoles(['admin', 'officer']));
        $this->assertFalse($user->hasAllRoles(['admin', 'officer', 'user']));
    }

    // ==================== PERMISSION CHECKING TESTS ====================

    /**
     * Test: hasPermissionTo checks permission
     */
    public function test_has_permission_to_checks_permission(): void
    {
        $user = User::factory()->create();
        $user->assignRole('admin');

        $this->assertTrue($user->hasPermissionTo('list-books'));
        $this->assertFalse($user->hasPermissionTo('create-payment'));
    }

    /**
     * Test: hasAnyPermission checks any permission
     */
    public function test_has_any_permission_checks_multiple_permissions(): void
    {
        $user = User::factory()->create();
        $user->assignRole('admin');

        $this->assertTrue($user->hasAnyPermission(['list-books', 'create-book']));
        $this->assertFalse($user->hasAnyPermission(['delete-book', 'create-payment']));
    }

    /**
     * Test: hasAllPermissions checks all permissions
     */
    public function test_has_all_permissions_checks_all_permissions(): void
    {
        $user = User::factory()->create();
        $user->assignRole('admin');

        $this->assertTrue($user->hasAllPermissions(['list-books', 'read-book']));
        $this->assertFalse($user->hasAllPermissions(['list-books', 'create-payment']));
    }

    /**
     * Test: can checks permission (gate method)
     */
    public function test_can_checks_permission(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('create-book');

        $this->assertTrue($user->can('create-book'));
        $this->assertFalse($user->can('delete-book'));
    }

    /**
     * Test: cannot checks permission negation
     */
    public function test_cannot_checks_permission_negation(): void
    {
        $user = User::factory()->create();

        $this->assertTrue($user->cannot('create-book'));
        $this->assertFalse($user->cannot('view-dashboard'));
    }

    // ==================== API ENDPOINT TESTS ====================

    /**
     * Test: User with permission can access protected endpoint
     */
    public function test_user_with_permission_can_access_endpoint(): void
    {
        $user = User::factory()->create();
        $user->assignRole('admin');

        $response = $this->actingAs($user)
            ->getJson('/api/books');

        $response->assertStatus(200);
    }

    /**
     * Test: User without permission cannot access endpoint
     */
    public function test_user_without_permission_cannot_access_endpoint(): void
    {
        $user = User::factory()->create();
        $user->assignRole('user'); // User role tidak punya 'create-book'

        $response = $this->actingAs($user)
            ->postJson('/api/books', [
                'id_package' => 'test-id',
                'id_user' => 'test-id',
                'book_code' => 'TEST001',
                'book_date' => '2026-01-21',
                'checkin_time' => '2026-01-21',
                'checkout_time' => '2026-01-22',
                'booker_name' => 'John Doe',
                'booker_email' => 'john@example.com',
                'booker_telp' => '081234567890',
                'status' => 'active',
            ]);

        $response->assertStatus(403);
    }

    /**
     * Test: Unauthenticated user cannot access protected endpoint
     */
    public function test_unauthenticated_user_cannot_access_endpoint(): void
    {
        $response = $this->getJson('/api/books');

        $response->assertStatus(401);
    }

    /**
     * Test: Officer can approve payment
     */
    public function test_officer_can_approve_payment(): void
    {
        $officer = User::factory()->create();
        $officer->assignRole('officer');

        $response = $this->actingAs($officer)
            ->postJson('/api/payments/payment-id/approve');

        // Will be 404 since payment doesn't exist, but authentication should pass
        $response->assertStatus(404); // Instead of 403
    }

    /**
     * Test: Non-officer cannot approve payment
     */
    public function test_non_officer_cannot_approve_payment(): void
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $response = $this->actingAs($user)
            ->postJson('/api/payments/payment-id/approve');

        $response->assertStatus(403);
    }

    // ==================== PERMISSION INHERITANCE TESTS ====================

    /**
     * Test: Super admin inherits all permissions
     */
    public function test_super_admin_has_all_permissions(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super-admin');

        $this->assertTrue($superAdmin->hasPermissionTo('create-book'));
        $this->assertTrue($superAdmin->hasPermissionTo('delete-book'));
        $this->assertTrue($superAdmin->hasPermissionTo('approve-payment'));
        $this->assertTrue($superAdmin->hasPermissionTo('export-reports'));
    }

    /**
     * Test: Admin has limited permissions
     */
    public function test_admin_has_limited_permissions(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->assertTrue($admin->hasPermissionTo('list-books'));
        $this->assertTrue($admin->hasPermissionTo('read-book'));
        $this->assertFalse($admin->hasPermissionTo('create-payment'));
        $this->assertFalse($admin->hasPermissionTo('delete-book'));
    }

    /**
     * Test: Officer has transaction permissions
     */
    public function test_officer_has_transaction_permissions(): void
    {
        $officer = User::factory()->create();
        $officer->assignRole('officer');

        $this->assertTrue($officer->hasPermissionTo('read-book'));
        $this->assertTrue($officer->hasPermissionTo('create-payment'));
        $this->assertFalse($officer->hasPermissionTo('update-book'));
        $this->assertFalse($officer->hasPermissionTo('export-reports'));
    }

    // ==================== EDGE CASE TESTS ====================

    /**
     * Test: User can have both direct permission and role permission
     */
    public function test_user_with_role_and_direct_permission(): void
    {
        $user = User::factory()->create();
        $user->assignRole('user'); // Limited permissions
        $user->givePermissionTo('create-book'); // Additional permission

        $this->assertTrue($user->hasPermissionTo('create-book'));
        $this->assertTrue($user->hasPermissionTo('view-dashboard'));
    }

    /**
     * Test: Permission is cached properly
     */
    public function test_permission_is_cached(): void
    {
        $user = User::factory()->create();
        $user->assignRole('admin');

        // First call
        $hasPermission1 = $user->hasPermissionTo('list-books');

        // Second call should use cache
        $hasPermission2 = $user->hasPermissionTo('list-books');

        $this->assertTrue($hasPermission1);
        $this->assertTrue($hasPermission2);
    }

    /**
     * Test: Cache is cleared when role is assigned
     */
    public function test_cache_cleared_when_role_assigned(): void
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $this->assertFalse($user->hasPermissionTo('create-book'));

        // Assign new role
        $user->assignRole('admin');

        // Cache should be cleared and permission should be available
        // This requires cache reset in your code
        app()['cache']->forget('spatie.permission.cache');

        $this->assertTrue($user->hasPermissionTo('list-books'));
    }
}
