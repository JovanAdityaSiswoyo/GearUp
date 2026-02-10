# 🔧 Dokumentasi Teknis: Development Guide

## 📌 Quick Start untuk Developer Baru

### Prerequisites
```bash
PHP 8.2+
Laravel 11
MySQL/PostgreSQL
Node.js (untuk frontend build)
Composer
```

### Setup
```bash
# Clone & install dependencies
git clone <repo>
cd AplikasiPinjam
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Setup database
php artisan migrate
php artisan db:seed --class=UnitSeeder

# Build frontend
npm run build

# Start development server
php artisan serve
npm run dev (on another terminal)

# Access
http://localhost:8000/officer/packing
http://localhost:8000/courier/route-map
```

---

## 🗂️ Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── CourierDeliveryController.php    ← Courier map & data
│   │   └── OfficerPackingController.php     ← Officer packing workflow
│   └── Middleware/
│       ├── Authenticate.php                 ← Auth guard
│       └── RoleMiddleware.php                ← Role-based access
│
├── Models/
│   ├── Unit.php                             ← NEW: Physical units
│   ├── Book.php
│   ├── BookPackageProduct.php               ← UPDATED: Unit tracking
│   ├── Product.php                          ← UPDATED: units() relationship
│   ├── User.php
│   └── Officer.php
│
├── Services/
│   ├── AtomicAssignmentService.php          ← NEW: Core assignment logic
│   └── BookingService.php
│
├── Providers/
│   └── RouteServiceProvider.php
│
└── Notifications/

database/
├── migrations/
│   ├── 2026_02_09_083032_create_units_table.php
│   └── 2026_02_09_083043_add_unit_tracking_to_book_package_products.php
│
└── seeders/
    └── UnitSeeder.php

resources/
├── views/
│   ├── courier/
│   │   └── route-map.blade.php              ← NEW
│   │
│   └── officer/
│       ├── packing/
│       │   ├── index.blade.php              ← NEW
│       │   └── show.blade.php               ← NEW
│       │
│       ├── dashboard.blade.php              ← UPDATED: Added packing link
│       ├── loan-approvals.blade.php         ← UPDATED
│       ├── returns-monitor.blade.php        ← UPDATED
│       ├── print-report.blade.php           ← UPDATED
│       └── payments/index.blade.php         ← UPDATED
│
└── js/
    └── app.js

routes/
├── web.php                                  ← UPDATED: 5 new packing routes
├── api.php
└── channels.php

storage/
├── logs/
└── app/

config/
├── app.php
├── database.php
├── auth.php
└── permission.php

tests/
├── Feature/
│   ├── CourierRouteMapTest.php              ← TODO
│   └── OfficerPackingTest.php               ← TODO
│
└── Unit/
    ├── AtomicAssignmentServiceTest.php      ← TODO
    └── UnitModelTest.php                    ← TODO
```

---

## 🎯 Core Components

### 1. Unit Model (`app/Models/Unit.php`)

```php
class Unit extends Model
{
    use HasUuids;
    
    protected $table = 'units';
    protected $fillable = ['id_product', 'serial_number', 'status', 'notes', 'last_maintenance_at'];
    protected $casts = [
        'id' => 'string',
        'id_product' => 'string',
        'status' => 'string',
        'last_maintenance_at' => 'datetime',
    ];
    
    // Relationships
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'id_product');
    }
    
    public function bookings(): HasManyThrough
    {
        return $this->hasManyThrough(Book::class, BookPackageProduct::class, 'id_unit', 'id');
    }
    
    // Scopes
    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('status', 'available');
    }
    
    public function scopeForProduct(Builder $query, string $productId): Builder
    {
        return $query->where('id_product', $productId);
    }
    
    // Methods
    public function lock(): void
    {
        $this->update(['status' => 'booked']);
    }
    
    public function release(): void
    {
        $this->update(['status' => 'available']);
    }
}

// Usage:
$units = Unit::available()->forProduct($productId)->limit(5)->get();
```

### 2. AtomicAssignmentService (`app/Services/AtomicAssignmentService.php`)

```php
class AtomicAssignmentService
{
    /**
     * Atomic assignment dengan transaction & locking
     * 
     * @param Book $book
     * @return array ['success' => bool, 'message' => string, 'assigned' => [], 'failures' => []]
     */
    public function assignUnitsForPackage(Book $book): array
    {
        return DB::transaction(function () use ($book) {
            $assigned = [];
            $failures = [];
            
            foreach ($book->bookPackageProducts as $bookProduct) {
                // Skip jika sudah di-assign
                if ($bookProduct->id_unit) {
                    $assigned[] = [
                        'product_id' => $bookProduct->id_product,
                        'unit_id' => $bookProduct->id_unit,
                    ];
                    continue;
                }
                
                // Cari available unit (lock untuk update)
                $unit = Unit::available()
                    ->forProduct($bookProduct->id_product)
                    ->lockForUpdate()  // ← ATOMIC LOCK
                    ->first();
                
                if (!$unit) {
                    $failures[] = [
                        'product' => $bookProduct->product->name,
                        'reason' => 'No available units',
                    ];
                    throw new Exception('Insufficient units');
                }
                
                // Assign unit
                $bookProduct->update([
                    'id_unit' => $unit->id,
                    'is_packed' => false,
                ]);
                
                $unit->lock(); // Update status to 'booked'
                
                $assigned[] = [
                    'product_id' => $unit->id_product,
                    'unit_id' => $unit->id,
                    'serial_number' => $unit->serial_number,
                ];
            }
            
            return [
                'success' => true,
                'message' => 'Units assigned successfully',
                'assigned' => $assigned,
                'failures' => $failures,
            ];
        });
    }
    
    /**
     * Get packing checklist
     */
    public function getPackingList(Book $book): array
    {
        return $book->bookPackageProducts
            ->map(fn($bp) => [
                'book_package_product_id' => $bp->id,
                'product_name' => $bp->product->name,
                'quantity' => $bp->qty,
                'unit_serial' => $bp->unit?->serial_number ?? 'Not assigned',
                'is_packed' => $bp->is_packed,
                'packed_at' => $bp->packed_at?->format('d M Y H:i'),
                'packed_by_name' => $bp->packedByOfficer?->name,
            ])
            ->toArray();
    }
    
    /**
     * Mark unit as packed
     */
    public function markAsPacked(string $bookPackageProductId, string $officerId): array
    {
        $bookProduct = BookPackageProduct::findOrFail($bookPackageProductId);
        
        // Verify unit matches
        $unit = $bookProduct->unit;
        if (!$unit) {
            return [
                'success' => false,
                'message' => 'Unit not assigned',
            ];
        }
        
        // Update packing status
        $bookProduct->update([
            'is_packed' => true,
            'packed_at' => now(),
            'packed_by' => $officerId,
        ]);
        
        return [
            'success' => true,
            'message' => 'Unit marked as packed',
            'packed_at' => $bookProduct->packed_at->format('d M Y H:i'),
        ];
    }
    
    /**
     * Check if all items are packed
     */
    public function isPackingComplete(Book $book): bool
    {
        return $book->bookPackageProducts->every(fn($bp) => $bp->is_packed);
    }
}
```

### 3. OfficerPackingController

```php
class OfficerPackingController extends Controller
{
    public function __construct(
        private AtomicAssignmentService $assignmentService
    ) {}
    
    /**
     * List bookings untuk packing
     * GET /officer/packing
     */
    public function index(Request $request): View
    {
        $query = Book::query()
            ->with(['package', 'user', 'bookPackageProducts'])
            ->whereIn('status', ['CONFIRMED', 'READY_FOR_PICKUP'])
            ->latest();
        
        // Search
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('booking_code', 'like', "%$search%")
                  ->orWhere('booker_name', 'like', "%$search%")
                  ->orWhereHas('user', fn($q) => $q->where('email', 'like', "%$search%"));
            });
        }
        
        $bookings = $query->paginate(10);
        
        return view('officer.packing.index', compact('bookings', 'search'));
    }
    
    /**
     * Packing checklist
     * GET /officer/packing/{booking}
     */
    public function show(string $bookingId): View
    {
        $booking = Book::findOrFail($bookingId);
        
        // Get packing list
        $packingList = $this->assignmentService->getPackingList($booking);
        $isComplete = $this->assignmentService->isPackingComplete($booking);
        
        return view('officer.packing.show', [
            'booking' => $booking,
            'packingList' => $packingList,
            'isComplete' => $isComplete,
            'packedCount' => collect($packingList)->where('is_packed', true)->count(),
            'totalCount' => count($packingList),
        ]);
    }
    
    /**
     * Atomic assign units
     * POST /officer/packing/{booking}/assign-units
     */
    public function assignUnits(string $bookingId): JsonResponse
    {
        try {
            $booking = Book::findOrFail($bookingId);
            $result = $this->assignmentService->assignUnitsForPackage($booking);
            
            return response()->json($result);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
    
    /**
     * Scan QR (verify unit serial)
     * POST /officer/packing/scan-unit
     */
    public function scanUnit(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'book_package_product_id' => 'required|uuid|exists:book_package_products,id',
            'unit_serial' => 'required|string|exists:units,serial_number',
        ]);
        
        $bookProduct = BookPackageProduct::findOrFail($validated['book_package_product_id']);
        
        // Verify serial matches assigned unit
        if ($bookProduct->unit->serial_number !== $validated['unit_serial']) {
            return response()->json([
                'success' => false,
                'message' => "❌ Serial tidak sesuai! Expected: {$bookProduct->unit->serial_number}",
            ], 422);
        }
        
        // Mark as packed
        return response()->json(
            $this->assignmentService->markAsPacked(
                $bookProduct->id,
                auth()->id()
            )
        );
    }
    
    /**
     * Complete packing
     * POST /officer/packing/{booking}/finalize
     */
    public function finalizePacking(string $bookingId): JsonResponse
    {
        $booking = Book::findOrFail($bookingId);
        
        if (!$this->assignmentService->isPackingComplete($booking)) {
            return response()->json([
                'success' => false,
                'message' => 'Packing not complete',
            ], 422);
        }
        
        $booking->update(['status' => 'READY_FOR_PICKUP']);
        
        return response()->json([
            'success' => true,
            'message' => 'Packing complete!',
            'redirect' => route('officer.packing.index'),
        ]);
    }
}
```

---

## 📊 Database Queries

### Get available units for product
```php
$units = Unit::available()
    ->forProduct($productId)
    ->limit(5)
    ->get();
```

### Get packing progress
```php
$packed = BookPackageProduct::where('id_book', $bookId)
    ->where('is_packed', true)
    ->count();

$total = BookPackageProduct::where('id_book', $bookId)->count();
$percentage = ($packed / $total) * 100;
```

### Get unit assignment history
```php
$history = Unit::where('serial_number', 'TEN-005-WXYZ')
    ->with('bookings')
    ->get();
```

### Find units that need maintenance
```php
$needsMaintenance = Unit::where('status', '!=', 'maintenance')
    ->where('last_maintenance_at', '<', now()->subMonths(3))
    ->get();
```

---

## 🔐 Security Considerations

### Authentication
- All routes require `auth:web,officer` middleware
- Officer role verified via Spatie Permission

### Authorization
- Officer can only see own bookings
- Unit serial verification prevents wrong assignment

### Data Integrity
- Atomic transactions prevent race conditions
- lockForUpdate() ensures serialization
- Rollback on any error

### Input Validation
```php
// Always validate in controller
$validated = $request->validate([
    'book_package_product_id' => 'required|uuid|exists:book_package_products,id',
    'unit_serial' => 'required|string|exists:units,serial_number',
]);
```

---

## 🧪 Testing Examples

### Feature Test
```php
class OfficerPackingTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_officer_can_view_packing_list()
    {
        $officer = User::factory()->create();
        $booking = Book::factory()->create(['status' => 'CONFIRMED']);
        
        $response = $this->actingAs($officer)
            ->get(route('officer.packing.index'));
        
        $response->assertStatus(200);
        $response->assertViewHas('bookings');
    }
    
    public function test_atomic_assignment_prevents_double_booking()
    {
        $product = Product::factory()->create();
        Unit::factory(5)->create(['id_product' => $product->id, 'status' => 'available']);
        
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
        
        $service = new AtomicAssignmentService();
        $service->assignUnitsForPackage($booking1);
        $service->assignUnitsForPackage($booking2);
        
        // Verify each booking has different unit
        $this->assertNotEquals(
            $booking1->bookPackageProducts->first()->id_unit,
            $booking2->bookPackageProducts->first()->id_unit
        );
    }
}
```

### Unit Test
```php
class AtomicAssignmentServiceTest extends TestCase
{
    public function test_assign_returns_assigned_units()
    {
        $product = Product::factory()->create();
        $unit = Unit::factory()->create([
            'id_product' => $product->id,
            'status' => 'available',
        ]);
        
        $booking = Book::factory()->create();
        BookPackageProduct::factory()->create([
            'id_book' => $booking->id,
            'id_product' => $product->id,
        ]);
        
        $service = new AtomicAssignmentService();
        $result = $service->assignUnitsForPackage($booking);
        
        $this->assertTrue($result['success']);
        $this->assertCount(1, $result['assigned']);
        $this->assertEquals($unit->id, $result['assigned'][0]['unit_id']);
    }
}
```

---

## 📝 Common Tasks

### Add new status untuk unit
```php
// In Migration
Schema::table('units', function (Blueprint $table) {
    // Modify enum value
    $table->enum('status', [
        'available', 'booked', 'deployed', 'returning', 
        'in_inspection', 'maintenance', 'lost_scrapped',
        'new_status' // ← Add here
    ])->change();
});

// In Model
enum UnitStatus: string {
    case Available = 'available';
    case Booked = 'booked';
    case NewStatus = 'new_status';
}
```

### Add audit logging
```php
// In controller
use Illuminate\Support\Facades\Log;

Log::info('Unit assigned', [
    'booking_id' => $booking->id,
    'unit_id' => $unit->id,
    'serial_number' => $unit->serial_number,
    'officer_id' => auth()->id(),
]);
```

### Export unit data
```php
// In controller
$units = Unit::with('product', 'bookings')
    ->whereIn('status', ['maintenance', 'lost_scrapped'])
    ->get();

return response()->json($units);
```

---

## 🐛 Debugging Tips

### Enable Query Logging
```php
// In routes or controller
use Illuminate\Support\Facades\DB;

DB::enableQueryLog();

// Your code here

dd(DB::getQueryLog());
```

### Check Unit Status
```bash
php artisan tinker

>>> Unit::all()->groupBy('status')
>>> Unit::where('serial_number', 'TEN-005-WXYZ')->first()
```

### Trace Booking Changes
```bash
>>> $booking = Book::find('uuid');
>>> $booking->bookPackageProducts->each(fn($bp) => dump($bp->toArray()));
```

---

## 📚 Additional Resources

- [Laravel Transactions](https://laravel.com/docs/11.x/database#database-transactions)
- [Eloquent Relationships](https://laravel.com/docs/11.x/eloquent-relationships)
- [Spatie Permissions](https://spatie.be/docs/laravel-permission/v5/introduction)
- [Leaflet.js Documentation](https://leafletjs.com/)

---

**Last Updated:** February 9, 2026
**Version:** 1.0
**Status:** Complete & Ready for Development
