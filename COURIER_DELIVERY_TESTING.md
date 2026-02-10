<!-- Test Script untuk Courier Delivery Pages -->
<!-- File: test-courier-delivery.ps1 (PowerShell) atau test-courier-delivery.sh (Bash) -->

# Courier Delivery Pages - Testing Guide

## 1. DATABASE SETUP TEST

```sql
-- Verify columns exist
DESC book_products;
DESC books;

-- Should show:
-- id_courier (UUID, nullable)
-- order_status (VARCHAR)
-- item_status (VARCHAR)
-- delivery_at (TIMESTAMP, nullable)
-- returned_at (TIMESTAMP, nullable)
```

## 2. AUTHENTICATION TEST

### Create Courier & User
```php
// In tinker or test
$courier = Courier::factory()->create([
    'nama' => 'Test Courier',
    'phone' => '08123456789',
]);

$user = User::factory()->create([
    'courier_id' => $courier->id,
    'role' => 'courier',
]);

$user->assignRole('courier');
```

### Login Test
```php
// Test login
auth()->guard('web')->login($user);

// Verify guard
auth()->guard('courier')->check(); // Should be true
```

## 3. ROUTE ACCESS TEST

```php
// Test anonymous user
$response = $this->get('/courier/dashboard');
$response->assertRedirect('/'); // Or login page

// Test authenticated courier
$response = $this->actingAs($user, 'web')
    ->get('/courier/dashboard');
$response->assertStatus(200);
$response->assertViewIs('courier.index');
```

## 4. DASHBOARD TEST

```php
// Test dashboard loads with data
$response = $this->actingAs($user, 'web')
    ->get('/courier/dashboard');

// Verify data passed to view
$response->assertViewHas('stats');
$response->assertViewHas('activeDeliveries');
$response->assertViewHas('activeReturns');
$response->assertViewHas('recentCompleted');

// Verify stats structure
$stats = $response->viewData('stats');
assert(isset($stats['readyForPickup']));
assert(isset($stats['outForDelivery']));
```

## 5. DELIVERY LISTING TEST

### Test Delivery Management Page
```php
// Create test bookings
$booking = BookProduct::factory()->create([
    'id_courier' => $courier->id,
    'order_status' => OrderStatus::READY_FOR_PICKUP,
    'item_status' => ItemStatus::BOOKED,
]);

// Test page loads
$response = $this->actingAs($user, 'web')
    ->get('/courier/deliveries');
$response->assertStatus(200);

// Test booking appears in view
$response->assertSee($booking->book_code);
```

### Test Delivery Detail Page
```php
// Test detail page
$response = $this->actingAs($user, 'web')
    ->get("/courier/deliveries/BookProduct/{$booking->id}");

$response->assertStatus(200);
$response->assertViewIs('courier.delivery-detail');
$response->assertViewHas('booking');
```

### Test Unauthorized Access
```php
// Create booking for different courier
$otherCourier = Courier::factory()->create();
$otherBooking = BookProduct::factory()->create([
    'id_courier' => $otherCourier->id,
]);

// Try to access
$response = $this->actingAs($user, 'web')
    ->get("/courier/deliveries/BookProduct/{$otherBooking->id}");

$response->assertStatus(403); // Forbidden
```

## 6. HISTORY PAGE TEST

```php
// Create completed bookings
$completed = BookProduct::factory()->create([
    'id_courier' => $courier->id,
    'order_status' => OrderStatus::COMPLETED,
    'returned_at' => now(),
]);

// Test history page
$response = $this->actingAs($user, 'web')
    ->get('/courier/deliveries/history');

$response->assertStatus(200);
$response->assertViewIs('courier.delivery-history');

// Test filter
$response = $this->actingAs($user, 'web')
    ->get('/courier/deliveries/history?filter=completed');

$response->assertSee($completed->book_code);
```

## 7. STATUS TRANSITION TEST

```php
// Test courier pickup delivery
$response = $this->actingAs($user, 'web')
    ->postJson('/booking-status/BookProduct/' . $booking->id . '/courier/pickup-delivery', [
        'photo' => UploadedFile::fake()->image('delivery.jpg'),
    ]);

$response->assertStatus(200);
$response->assertJson(['success' => true]);

// Verify status changed
$booking->refresh();
assert($booking->order_status == OrderStatus::OUT_FOR_DELIVERY);
```

## 8. PHOTO UPLOAD TEST

```php
// Test photo upload with valid file
$file = UploadedFile::fake()->image('photo.jpg', 640, 480);

$response = $this->actingAs($user, 'web')
    ->postJson('/booking-status/BookProduct/' . $booking->id . '/courier/pickup-delivery', [
        'photo' => $file,
    ]);

$response->assertStatus(200);

// Verify file stored
$booking->refresh();
assert(!empty($booking->photo_delivery));
assert(Storage::disk('public')->exists($booking->photo_delivery));

// Test invalid file
$response = $this->actingAs($user, 'web')
    ->postJson('/booking-status/BookProduct/' . $booking->id . '/courier/pickup-delivery', [
        'photo' => UploadedFile::fake()->create('test.pdf', 100),
    ]);

// Should fail or be rejected
```

## 9. FULL WORKFLOW TEST

```php
// Create booking
$booking = BookProduct::factory()->create([
    'id_courier' => $courier->id,
    'order_status' => OrderStatus::CONFIRMED,
    'item_status' => ItemStatus::BOOKED,
]);

// Step 1: Officer prepares for pickup
$this->actingAs($officer, 'web')
    ->postJson('/booking-status/BookProduct/' . $booking->id . '/prepare-pickup');

// Step 2: Courier pickups for delivery
$this->actingAs($user, 'web')
    ->postJson('/booking-status/BookProduct/' . $booking->id . '/courier/pickup-delivery', [
        'photo' => UploadedFile::fake()->image('photo.jpg'),
    ]);

// Step 3: Courier completes delivery
$this->actingAs($user, 'web')
    ->postJson('/booking-status/BookProduct/' . $booking->id . '/courier/complete-delivery', [
        'photo' => UploadedFile::fake()->image('photo.jpg'),
    ]);

// Step 4: Schedule return
$this->actingAs($officer, 'web')
    ->postJson('/booking-status/BookProduct/' . $booking->id . '/schedule-return');

// Step 5: Courier pickups for return
$this->actingAs($user, 'web')
    ->postJson('/booking-status/BookProduct/' . $booking->id . '/courier/pickup-return', [
        'photo' => UploadedFile::fake()->image('photo.jpg'),
    ]);

// Step 6: Courier completes return
$this->actingAs($user, 'web')
    ->postJson('/booking-status/BookProduct/' . $booking->id . '/courier/complete-return', [
        'photo' => UploadedFile::fake()->image('photo.jpg'),
    ]);

// Verify final state
$booking->refresh();
assert($booking->order_status == OrderStatus::PENDING_REVIEW);
assert($booking->returned_at != null);
```

## 10. MANUAL TESTING CHECKLIST

### Visual Inspection
- [ ] Dashboard loads without errors
- [ ] Stats cards display correctly
- [ ] Active deliveries list shows items
- [ ] Active returns list shows items
- [ ] Recent completed table displays

### Navigation
- [ ] Breadcrumb works correctly
- [ ] Links navigate to correct pages
- [ ] Back buttons return to list
- [ ] Menu items highlight correctly

### Delivery Management
- [ ] All active deliveries display
- [ ] All active returns display
- [ ] Status badges show correct colors
- [ ] Buttons are clickable

### Detail View
- [ ] Product information displays
- [ ] Receiver information displays
- [ ] Timeline shows all steps
- [ ] Sidebar status card sticky
- [ ] Action buttons appear based on status

### Photo Upload
- [ ] Dialog opens on button click
- [ ] File picker works
- [ ] Image preview displays
- [ ] Submit button functional
- [ ] Success message shows

### History Page
- [ ] All completed items display
- [ ] Filter tabs work
- [ ] Pagination displays
- [ ] Items are clickable
- [ ] Detail view opens from history

### Authorization
- [ ] Non-courier cannot access
- [ ] Courier sees only own deliveries
- [ ] Cannot modify others' deliveries
- [ ] Cannot access restricted routes

### Responsive
- [ ] Mobile view (320px)
- [ ] Tablet view (768px)
- [ ] Desktop view (1024px)
- [ ] All content visible
- [ ] Navigation works on mobile

## 11. PERFORMANCE TESTING

```php
// Test query count
\DB::enableQueryLog();

$this->actingAs($user)->get('/courier/dashboard');

$queries = count(\DB::getQueryLog());
assert($queries < 20); // Should be efficient

// Test page load time
$start = microtime(true);
$this->actingAs($user)->get('/courier/dashboard');
$end = microtime(true);

$time = ($end - $start) * 1000; // milliseconds
assert($time < 500); // Should load in under 500ms
```

## 12. STRESS TEST

```php
// Create many bookings
for ($i = 0; $i < 100; $i++) {
    BookProduct::factory()->create([
        'id_courier' => $courier->id,
        'order_status' => OrderStatus::READY_FOR_PICKUP,
    ]);
}

// Test pagination
$response = $this->actingAs($user)->get('/courier/deliveries/history?page=5');
$response->assertStatus(200);

// Verify pagination works
assert($response->viewData('bookings')->count() <= 15);
```

## 13. BROWSER TESTING (Selenium/Dusk)

```php
$browser = $this->browse(function ($browser) {
    // Login
    $browser->visit('/login')
        ->type('email', $user->email)
        ->type('password', 'password')
        ->press('Login');

    // Navigate to dashboard
    $browser->visit('/courier/dashboard')
        ->assertSee('Dashboard Courier')
        ->assertSee('Siap Diambil');

    // Click deliveries
    $browser->click('[href="' . route('courier.deliveries.index') . '"]')
        ->assertSee('Pengiriman');

    // Click delivery item
    $browser->click('a[href*="/deliveries/BookProduct/"]')
        ->assertSee('Detail Pengiriman');
});
```

## 14. ROLLBACK TEST

```php
// Test that failed operations don't corrupt data
$booking = BookProduct::factory()->create([
    'id_courier' => $courier->id,
    'order_status' => OrderStatus::READY_FOR_PICKUP,
]);

// Try invalid operation
$this->actingAs($user)->postJson(
    '/booking-status/BookProduct/' . $booking->id . '/invalid-action',
    []
)->assertStatus(400); // Or error response

// Verify booking unchanged
$booking->refresh();
assert($booking->order_status == OrderStatus::READY_FOR_PICKUP);
```

## Running Tests

```bash
# Run all tests
php artisan test tests/Feature/CourierDeliveryTest.php

# Run specific test
php artisan test tests/Feature/CourierDeliveryTest.php::test_courier_dashboard

# Run with coverage
php artisan test --coverage

# Run with report
php artisan test --report
```

---

**Test Status**: Ready  
**Last Updated**: 2024  
**Version**: 1.0
