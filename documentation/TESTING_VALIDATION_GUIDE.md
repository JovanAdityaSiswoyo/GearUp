# 🧪 Testing & Validation Guide

## 📋 Table of Contents
1. [Unit Testing](#unit-testing)
2. [Feature Testing](#feature-testing)
3. [Manual Testing Checklist](#manual-testing-checklist)
4. [Integration Testing](#integration-testing)
5. [Performance Testing](#performance-testing)
6. [Test Data & Seeding](#test-data--seeding)

---

## Unit Testing

### Test Files Location
```
tests/Unit/
├── Services/
│   └── AtomicAssignmentServiceTest.php
├── Models/
│   ├── UnitTest.php
│   └── BookPackageProductTest.php
```

### AtomicAssignmentService Tests

**File:** `tests/Unit/Services/AtomicAssignmentServiceTest.php`

```php
namespace Tests\Unit\Services;

use App\Models\{Book, BookPackageProduct, Product, Unit};
use App\Services\AtomicAssignmentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AtomicAssignmentServiceTest extends TestCase
{
    use RefreshDatabase;
    
    private AtomicAssignmentService $service;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new AtomicAssignmentService();
    }
    
    /** @test */
    public function it_assigns_units_successfully()
    {
        // Arrange
        $product = Product::factory()->create();
        $unit = Unit::factory()->create([
            'id_product' => $product->id,
            'status' => 'available',
        ]);
        
        $booking = Book::factory()->create();
        BookPackageProduct::factory()->create([
            'id_book' => $booking->id,
            'id_product' => $product->id,
            'id_unit' => null,
        ]);
        
        // Act
        $result = $this->service->assignUnitsForPackage($booking);
        
        // Assert
        $this->assertTrue($result['success']);
        $this->assertCount(1, $result['assigned']);
        $this->assertEquals($unit->id, $result['assigned'][0]['unit_id']);
    }
    
    /** @test */
    public function it_fails_when_insufficient_units()
    {
        // Arrange
        $product = Product::factory()->create();
        // No units available
        
        $booking = Book::factory()->create();
        BookPackageProduct::factory()->create([
            'id_book' => $booking->id,
            'id_product' => $product->id,
        ]);
        
        // Act & Assert
        $this->expectException(Exception::class);
        $this->service->assignUnitsForPackage($booking);
    }
    
    /** @test */
    public function it_prevents_double_booking()
    {
        // Arrange
        $product = Product::factory()->create();
        Unit::factory(2)->create([
            'id_product' => $product->id,
            'status' => 'available',
        ]);
        
        $booking1 = Book::factory()->create();
        BookPackageProduct::factory()->create([
            'id_book' => $booking1->id,
            'id_product' => $product->id,
        ]);
        
        $booking2 = Book::factory()->create();
        BookPackageProduct::factory()->create([
            'id_book' => $booking2->id,
            'id_product' => $product->id,
        ]);
        
        // Act
        $result1 = $this->service->assignUnitsForPackage($booking1);
        $result2 = $this->service->assignUnitsForPackage($booking2);
        
        // Assert - different units assigned
        $this->assertNotEquals(
            $result1['assigned'][0]['unit_id'],
            $result2['assigned'][0]['unit_id']
        );
    }
    
    /** @test */
    public function it_marks_units_as_packed()
    {
        // Arrange
        $product = Product::factory()->create();
        $unit = Unit::factory()->create([
            'id_product' => $product->id,
            'status' => 'available',
        ]);
        
        $booking = Book::factory()->create();
        $bookProduct = BookPackageProduct::factory()->create([
            'id_book' => $booking->id,
            'id_product' => $product->id,
            'id_unit' => $unit->id,
            'is_packed' => false,
        ]);
        
        // Act
        $result = $this->service->markAsPacked(
            $bookProduct->id,
            auth()->id() ?? 'test-officer'
        );
        
        // Assert
        $this->assertTrue($result['success']);
        $this->assertTrue($bookProduct->fresh()->is_packed);
    }
    
    /** @test */
    public function it_checks_packing_complete()
    {
        // Arrange
        $product1 = Product::factory()->create();
        $product2 = Product::factory()->create();
        
        $booking = Book::factory()->create();
        $bp1 = BookPackageProduct::factory()->create([
            'id_book' => $booking->id,
            'id_product' => $product1->id,
            'is_packed' => true,
        ]);
        $bp2 = BookPackageProduct::factory()->create([
            'id_book' => $booking->id,
            'id_product' => $product2->id,
            'is_packed' => false,
        ]);
        
        // Act & Assert
        $this->assertFalse($this->service->isPackingComplete($booking));
        
        $bp2->update(['is_packed' => true]);
        $this->assertTrue($this->service->isPackingComplete($booking));
    }
}
```

### Unit Model Tests

**File:** `tests/Unit/Models/UnitTest.php`

```php
namespace Tests\Unit\Models;

use App\Models\{Product, Unit};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UnitTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    public function it_has_unique_serial_number()
    {
        $unit = Unit::factory()->create([
            'serial_number' => 'TEN-005-WXYZ'
        ]);
        
        // This should fail due to unique constraint
        $this->expectException(Throwable::class);
        Unit::factory()->create([
            'serial_number' => 'TEN-005-WXYZ'
        ]);
    }
    
    /** @test */
    public function it_belongs_to_product()
    {
        $product = Product::factory()->create();
        $unit = Unit::factory()->create(['id_product' => $product->id]);
        
        $this->assertTrue($unit->product->is($product));
    }
    
    /** @test */
    public function it_can_lock()
    {
        $unit = Unit::factory()->create(['status' => 'available']);
        
        $unit->lock();
        
        $this->assertEquals('booked', $unit->fresh()->status);
    }
    
    /** @test */
    public function it_can_release()
    {
        $unit = Unit::factory()->create(['status' => 'booked']);
        
        $unit->release();
        
        $this->assertEquals('available', $unit->fresh()->status);
    }
    
    /** @test */
    public function available_scope_returns_only_available()
    {
        Unit::factory(3)->create(['status' => 'available']);
        Unit::factory(2)->create(['status' => 'booked']);
        
        $available = Unit::available()->get();
        
        $this->assertCount(3, $available);
        $available->each(fn($u) => $this->assertEquals('available', $u->status));
    }
    
    /** @test */
    public function forProduct_scope_filters_by_product()
    {
        $product1 = Product::factory()->create();
        $product2 = Product::factory()->create();
        
        Unit::factory(3)->create(['id_product' => $product1->id]);
        Unit::factory(2)->create(['id_product' => $product2->id]);
        
        $units = Unit::forProduct($product1->id)->get();
        
        $this->assertCount(3, $units);
    }
}
```

---

## Feature Testing

### Test Files Location
```
tests/Feature/
├── Officer/
│   ├── PackingListTest.php
│   ├── PackingChecklistTest.php
│   └── PackingWorkflowTest.php
├── Courier/
│   └── RouteMapTest.php
```

### Packing List Feature Test

**File:** `tests/Feature/Officer/PackingListTest.php`

```php
namespace Tests\Feature\Officer;

use App\Models\{Book, Officer, Product, User};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PackingListTest extends TestCase
{
    use RefreshDatabase;
    
    private User $officer;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->officer = User::factory()->create();
        $this->officer->assignRole('officer');
    }
    
    /** @test */
    public function officer_can_view_packing_list()
    {
        $response = $this->actingAs($this->officer)
            ->get(route('officer.packing.index'));
        
        $response->assertStatus(200);
        $response->assertViewIs('officer.packing.index');
        $response->assertViewHas('bookings');
    }
    
    /** @test */
    public function packing_list_shows_only_confirmed_bookings()
    {
        Book::factory()->create(['status' => 'CONFIRMED']);
        Book::factory()->create(['status' => 'PENDING']);
        Book::factory()->create(['status' => 'READY_FOR_PICKUP']);
        
        $response = $this->actingAs($this->officer)
            ->get(route('officer.packing.index'));
        
        $response->assertViewHas('bookings', fn($bookings) => 
            $bookings->count() >= 2 // CONFIRMED + READY_FOR_PICKUP
        );
    }
    
    /** @test */
    public function officer_can_search_bookings()
    {
        $booking = Book::factory()->create([
            'booking_code' => 'BK-12345',
            'status' => 'CONFIRMED'
        ]);
        
        $response = $this->actingAs($this->officer)
            ->get(route('officer.packing.index', ['search' => 'BK-12345']));
        
        $response->assertViewHas('bookings', fn($bookings) =>
            $bookings->contains('id', $booking->id)
        );
    }
    
    /** @test */
    public function unauthenticated_user_cannot_access()
    {
        $response = $this->get(route('officer.packing.index'));
        
        $response->assertRedirect(route('login'));
    }
    
    /** @test */
    public function non_officer_cannot_access()
    {
        $user = User::factory()->create();
        $user->assignRole('customer');
        
        $response = $this->actingAs($user)
            ->get(route('officer.packing.index'));
        
        $response->assertForbidden();
    }
}
```

### Packing Workflow Feature Test

**File:** `tests/Feature/Officer/PackingWorkflowTest.php`

```php
namespace Tests\Feature\Officer;

use App\Models\{Book, BookPackageProduct, Product, Unit, User};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PackingWorkflowTest extends TestCase
{
    use RefreshDatabase;
    
    private User $officer;
    private Book $booking;
    private Unit $unit;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->officer = User::factory()->create();
        $this->officer->assignRole('officer');
        
        // Setup test booking
        $product = Product::factory()->create();
        $this->unit = Unit::factory()->create([
            'id_product' => $product->id,
            'status' => 'available',
            'serial_number' => 'TEST-001-XXXX'
        ]);
        
        $this->booking = Book::factory()->create(['status' => 'CONFIRMED']);
        BookPackageProduct::factory()->create([
            'id_book' => $this->booking->id,
            'id_product' => $product->id,
            'id_unit' => null,
        ]);
    }
    
    /** @test */
    public function complete_packing_workflow()
    {
        // Step 1: View packing detail
        $response = $this->actingAs($this->officer)
            ->get(route('officer.packing.show', $this->booking->id));
        
        $response->assertStatus(200);
        
        // Step 2: Assign units
        $response = $this->actingAs($this->officer)
            ->postJson(
                route('officer.packing.assignUnits', $this->booking->id),
                []
            );
        
        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        
        // Verify unit assigned
        $this->assertTrue(
            $this->booking->bookPackageProducts
                ->first()
                ->fresh()
                ->id_unit !== null
        );
        
        // Step 3: Scan unit
        $bookProduct = $this->booking->bookPackageProducts->first();
        $response = $this->actingAs($this->officer)
            ->postJson(route('officer.packing.scanUnit'), [
                'book_package_product_id' => $bookProduct->id,
                'unit_serial' => $this->unit->serial_number,
            ]);
        
        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        
        // Verify marked as packed
        $this->assertTrue($bookProduct->fresh()->is_packed);
        
        // Step 4: Finalize packing
        $response = $this->actingAs($this->officer)
            ->postJson(
                route('officer.packing.finalizePacking', $this->booking->id),
                []
            );
        
        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        
        // Verify booking status updated
        $this->assertEquals(
            'READY_FOR_PICKUP',
            $this->booking->fresh()->status
        );
    }
    
    /** @test */
    public function cannot_finalize_with_unpacked_items()
    {
        // Assign units first
        $this->actingAs($this->officer)
            ->postJson(
                route('officer.packing.assignUnits', $this->booking->id),
                []
            );
        
        // Try to finalize without packing
        $response = $this->actingAs($this->officer)
            ->postJson(
                route('officer.packing.finalizePacking', $this->booking->id),
                []
            );
        
        $response->assertStatus(422);
        $response->assertJsonPath('success', false);
    }
    
    /** @test */
    public function cannot_scan_wrong_unit_serial()
    {
        // Assign units
        $this->actingAs($this->officer)
            ->postJson(
                route('officer.packing.assignUnits', $this->booking->id),
                []
            );
        
        $bookProduct = $this->booking->bookPackageProducts->first();
        
        // Try to scan wrong serial
        $response = $this->actingAs($this->officer)
            ->postJson(route('officer.packing.scanUnit'), [
                'book_package_product_id' => $bookProduct->id,
                'unit_serial' => 'WRONG-SERIAL',
            ]);
        
        $response->assertStatus(422);
        $response->assertJsonPath('success', false);
    }
}
```

### Courier Route Map Test

**File:** `tests/Feature/Courier/RouteMapTest.php`

```php
namespace Tests\Feature\Courier;

use App\Models\{Book, Courier, Delivery, Return as ReturnModel, User};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RouteMapTest extends TestCase
{
    use RefreshDatabase;
    
    private User $courier;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->courier = User::factory()->create();
        $this->courier->assignRole('courier');
    }
    
    /** @test */
    public function courier_can_view_route_map()
    {
        $response = $this->actingAs($this->courier)
            ->get(route('courier.route-map'));
        
        $response->assertStatus(200);
        $response->assertViewIs('courier.route-map');
    }
    
    /** @test */
    public function route_map_data_returns_tasks()
    {
        // Create test deliveries/returns
        Book::factory(5)->create(['status' => 'READY_FOR_PICKUP']);
        
        $response = $this->actingAs($this->courier)
            ->getJson(route('courier.route-map.data'));
        
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'total_deliveries',
            'total_returns',
            'total_tasks',
            'grouped_by_area',
            'all_tasks',
        ]);
    }
    
    /** @test */
    public function route_map_filters_by_type()
    {
        $response = $this->actingAs($this->courier)
            ->getJson(route('courier.route-map.data', ['type' => 'delivery']));
        
        $response->assertStatus(200);
        $response->assertJsonStructure(['grouped_by_area', 'all_tasks']);
    }
}
```

---

## Manual Testing Checklist

### 1. Pre-Testing Setup

- [ ] Database migrations ran: `php artisan migrate`
- [ ] Units seeded: `php artisan db:seed --class=UnitSeeder`
- [ ] Create test officer account
- [ ] Create test courier account
- [ ] Create test bookings with status CONFIRMED

### 2. Officer Packing - List View

**URL:** `http://localhost:8000/officer/packing`

- [ ] Page loads correctly
- [ ] All bookings displayed in table
- [ ] Search functionality works
- [ ] Pagination works (if >10 bookings)
- [ ] Action buttons visible
- [ ] Status badges show correct colors
- [ ] Empty state shows if no bookings

**Test Data:**
```bash
# Create test bookings
php artisan tinker
>>> Book::factory(15)->create(['status' => 'CONFIRMED'])
```

### 3. Officer Packing - Checklist View

**URL:** `http://localhost:8000/officer/packing/{booking_id}`

- [ ] Booking info displays correctly
- [ ] Progress bar shows 0/N items
- [ ] All items listed in checklist
- [ ] Each item shows serial number
- [ ] QR input field visible
- [ ] Finalize button disabled (gray) when not 100%
- [ ] Back button works

### 4. Unit Assignment

**Action:** Click button or manually POST to `/assign-units`

- [ ] All units assigned
- [ ] Progress bar doesn't update (needs scanning)
- [ ] Serial numbers appear in checklist
- [ ] Database shows units locked (status = booked)
- [ ] Multiple bookings get different units

**Verify in database:**
```bash
php artisan tinker
>>> $booking = Book::with('bookPackageProducts.unit')->find('id')
>>> $booking->bookPackageProducts->each(fn($bp) => dd($bp->unit->serial_number))
```

### 5. QR Scanning

**Action:** Input serial number in QR field, click Scan

**Test Cases:**

a) **Correct Serial:**
- [ ] Item marked with ✅ checkmark
- [ ] Background color changes to green
- [ ] Progress bar updates (e.g., 1/3)
- [ ] `packed_at` timestamp shows
- [ ] Serial field clears for next item

**Test:**
```bash
# Get correct serial
php artisan tinker
>>> $bp = BookPackageProduct::with('unit')->find('id')
>>> echo $bp->unit->serial_number
# Copy and paste into input
```

b) **Wrong Serial:**
- [ ] Error alert shows: "Serial tidak sesuai!"
- [ ] Item NOT marked as packed
- [ ] Progress bar doesn't change
- [ ] Can retry with correct serial

**Test:**
```bash
# Input different serial
TEN-005-WXYZ (when expecting KMP-012-QRST)
```

c) **Invalid Serial:**
- [ ] Validation error shows
- [ ] Cannot submit empty field
- [ ] Cannot submit non-existent serial

### 6. Finalize Packing

**Conditions to test:**

a) **When 100% Complete:**
- [ ] Button enabled (green color)
- [ ] Clicking shows confirmation modal
- [ ] After confirm, redirects to packing list
- [ ] Database shows booking status = READY_FOR_PICKUP
- [ ] Units show status = deployed

**Test:**
```bash
# Mark all items as packed manually
php artisan tinker
>>> BookPackageProduct::where('id_book', 'id')->update(['is_packed' => true])
```

b) **When < 100%:**
- [ ] Button disabled (gray color)
- [ ] Cursor shows "not-allowed"
- [ ] Clicking doesn't do anything
- [ ] Error message if forced via API

### 7. Courier Route Map

**URL:** `http://localhost:8000/courier/route-map`

- [ ] Page loads
- [ ] Map displays with Leaflet
- [ ] Markers show on map
- [ ] Area list on right shows tasks
- [ ] Filter buttons work
- [ ] Statistics show (total deliveries, returns)
- [ ] Clicking marker shows details
- [ ] Refresh data button works

**Create test deliveries:**
```bash
php artisan tinker
>>> Book::factory(10)->create(['status' => 'READY_FOR_PICKUP'])
```

### 8. Permissions & Access Control

- [ ] Officer can access `/officer/packing`
- [ ] Courier cannot access `/officer/packing` → 403
- [ ] Customer cannot access `/officer/packing` → 403
- [ ] Unauthenticated user redirects to login
- [ ] Officer can access `/courier/route-map` → 403 (if restricted)
- [ ] Courier can access `/courier/route-map` → 200

### 9. Database Integrity

```bash
php artisan tinker

# Check units locked
>>> Unit::where('status', 'booked')->count() # Should > 0

# Check packing marked
>>> BookPackageProduct::where('is_packed', true)->count() # Should > 0

# Check relationships intact
>>> $bp = BookPackageProduct::find('id')
>>> dd($bp->unit->serial_number) # Should have unit

# Check audit trail
>>> $bp->packed_at # Should have timestamp
>>> $bp->packed_by  # Should have officer ID
```

---

## Integration Testing

### Full Workflow Test

```bash
# Step 1: Create product with units
php artisan tinker
>>> $product = Product::factory()->create(['name' => 'Test Tenda'])
>>> Unit::factory(10)->create(['id_product' => $product->id, 'status' => 'available'])

# Step 2: Create booking
>>> $user = User::factory()->create()
>>> $package = Package::factory()->create()
>>> $booking = Book::factory()->create([
...   'id_user' => $user->id,
...   'id_package' => $package->id,
...   'status' => 'CONFIRMED'
... ])

# Step 3: Add product to booking
>>> BookPackageProduct::factory()->create([
...   'id_book' => $booking->id,
...   'id_product' => $product->id,
...   'qty' => 1
... ])

# Step 4: Login as officer and test
>>> $officer = User::factory()->create()
>>> $officer->assignRole('officer')
```

Then use Postman or browser to:
1. Login as officer
2. Navigate to `/officer/packing`
3. Click on test booking
4. Click "Assign Units"
5. Scan unit serial
6. Finalize packing

### Concurrent Request Testing

```bash
# Test race condition prevention
# Create booking with limited units (3)

# Run 5 concurrent POST requests to assign-units
# Only first 3 should succeed, last 2 should rollback

# Use Apache Bench:
ab -n 5 -c 5 -p data.json http://localhost:8000/officer/packing/booking-id/assign-units
```

---

## Performance Testing

### Load Test

```bash
# Install Apache Bench (if not installed)
# Mac: brew install httpd
# Ubuntu: apt-get install apache2-utils

# Test packing list with 100 bookings
ab -n 100 -c 10 http://localhost:8000/officer/packing

# Expected:
# Requests per second: > 10
# Time per request: < 100ms
# 50% of requests: < 50ms
```

### Query Performance

```bash
# Enable query logging
php artisan tinker
>>> DB::enableQueryLog()
>>> Book::with('bookPackageProducts.unit')->paginate(10)
>>> dd(DB::getQueryLog())

# Expected: Max 5-10 queries (not N+1)
```

### Database Size

```bash
# Check table sizes
SELECT 
    table_name,
    ROUND(((data_length + index_length) / 1024 / 1024), 2) AS size_mb
FROM information_schema.tables
WHERE table_schema = 'aplikasi_pinjam'
ORDER BY (data_length + index_length) DESC;
```

---

## Test Data & Seeding

### Create Test Bookings

```php
// In tinker or test:
$products = Product::limit(3)->get();

Book::factory()
    ->has(
        BookPackageProduct::factory()
            ->count(count($products))
            ->sequence(...$products->map(fn($p) => ['id_product' => $p->id])->toArray())
    )
    ->create(['status' => 'CONFIRMED']);
```

### Reset Test Data

```bash
# Drop and recreate
php artisan migrate:fresh --seed

# Or just clear specific tables
php artisan tinker
>>> BookPackageProduct::truncate()
>>> Book::truncate()
>>> Unit::query()->update(['status' => 'available'])
```

### Seed Production-like Data

```bash
# In seeder
$users = User::factory(10)->create();
$products = Product::limit(10)->get();
$packages = Package::limit(5)->get();

foreach ($packages as $package) {
    Book::factory(20)
        ->for($users->random())
        ->for($package)
        ->create(['status' => 'CONFIRMED']);
}
```

---

## Run All Tests

```bash
# Run all tests
php artisan test

# Run specific test class
php artisan test tests/Feature/Officer/PackingWorkflowTest.php

# Run specific test method
php artisan test tests/Feature/Officer/PackingWorkflowTest.php --filter=complete_packing_workflow

# Run with coverage
php artisan test --coverage

# Run only unit tests
php artisan test tests/Unit

# Run only feature tests
php artisan test tests/Feature
```

---

## Expected Test Results

✅ All tests should PASS:
- Unit tests: 12 tests
- Feature tests: 15 tests
- Integration tests: 5 tests
- **Total: 32 tests**

If any fail, check:
1. Database migrations: `php artisan migrate:status`
2. Test database setup: `.env.testing`
3. Seeding: Check if units exist in test DB
4. Permissions: Check if roles assigned in test
5. Routes: `php artisan route:list | grep packing`

---

**Last Updated:** February 9, 2026
**Status:** ✅ Testing Guide Complete
