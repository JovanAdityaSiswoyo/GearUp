<?php

use Illuminate\Support\Facades\Route;


// Landing Page (Public)
Route::get('/', function () {
    return view('home');
})->name('home');

// Public Product & Package Lists + Detail
Route::get('/products', [App\Http\Controllers\User\ProductController::class, 'index'])->name('user.products.index');
Route::get('/product/{product}', [App\Http\Controllers\User\ProductController::class, 'show'])->name('user.product.show');
Route::get('/packages', [App\Http\Controllers\User\PackageController::class, 'index'])->name('user.packages.index');
Route::get('/package/{package}', [App\Http\Controllers\User\PackageController::class, 'show'])->name('user.package.show');
Route::get('/brand/{brand}', [App\Http\Controllers\User\ProductController::class, 'brandProducts'])->name('user.brand.products');
Route::get('/cart', [App\Http\Controllers\User\CartController::class, 'index'])->name('user.cart.index');
Route::post('/cart/add/{product}', [App\Http\Controllers\User\CartController::class, 'add'])->name('user.cart.add');
Route::post('/cart/update/{product}', [App\Http\Controllers\User\CartController::class, 'update'])->name('user.cart.update');
Route::post('/cart/remove/{product}', [App\Http\Controllers\User\CartController::class, 'remove'])->name('user.cart.remove');

// Guest Routes (Not Authenticated)
Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return redirect('/');
    })->name('login');
    
    Route::get('/register', function () {
        return redirect('/');
    })->name('register');
});

// Admin Routes
Route::prefix('admin')->middleware(['auth:web,admin'])->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');
    
    // Users Management
    Route::resource('users', App\Http\Controllers\AdminUserController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
    
    // Products Management
    Route::resource('products', App\Http\Controllers\ProductController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
    Route::patch('products/{product}/stock', [App\Http\Controllers\ProductController::class, 'addStock'])->name('products.stock');
    
    // Units Management
    Route::get('units/bulk-print', [App\Http\Controllers\UnitController::class, 'bulkPrint'])->name('units.bulk-print');
    Route::resource('units', App\Http\Controllers\UnitController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);
    Route::post('products/{product}/units/bulk', [App\Http\Controllers\UnitController::class, 'bulkCreate'])->name('products.units.bulk');
    
    // Categories Management
    Route::resource('categories', App\Http\Controllers\CategoryController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
    
    // Brands Management
    Route::resource('brands', App\Http\Controllers\BrandController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
    
    // Packages Management
    Route::resource('packages', App\Http\Controllers\PackageController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
    
    // Bookings/Peminjaman Management
    Route::get('/bookings', [App\Http\Controllers\BookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/create', [App\Http\Controllers\BookingController::class, 'create'])->name('bookings.create');
    Route::post('/bookings', [App\Http\Controllers\BookingController::class, 'store'])->name('bookings.store');
    Route::get('/bookings/{type}/{booking}', [App\Http\Controllers\BookingController::class, 'show'])
        ->where('type', 'product|package')
        ->name('bookings.show');
    Route::get('/bookings/{type}/{booking}/edit', [App\Http\Controllers\BookingController::class, 'edit'])
        ->where('type', 'product|package')
        ->name('bookings.edit');
    Route::put('/bookings/{type}/{booking}', [App\Http\Controllers\BookingController::class, 'update'])
        ->where('type', 'product|package')
        ->name('bookings.update');
    Route::delete('/bookings/{type}/{booking}', [App\Http\Controllers\BookingController::class, 'destroy'])
        ->where('type', 'product|package')
        ->name('bookings.destroy');
    Route::post('/bookings/{type}/{booking}/update-data', [App\Http\Controllers\BookingController::class, 'updateData'])
        ->where('type', 'product|package')
        ->name('booking-update.store');
    
    // Returns/Pengembalian Management
    Route::get('/returns', [App\Http\Controllers\ReturnController::class, 'index'])->name('returns.index');
    Route::get('/returns/{type}/{return}', [App\Http\Controllers\ReturnController::class, 'show'])
        ->where('type', 'product|package')
        ->name('returns.show');
    Route::post('/returns/{type}/{return}/process', [App\Http\Controllers\ReturnController::class, 'process'])
        ->where('type', 'product|package')
        ->name('returns.process');
    
    // Payments Management
    Route::get('/payments', [App\Http\Controllers\PaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/{id}', [App\Http\Controllers\PaymentController::class, 'show'])->name('payments.show');
    Route::post('/payments/{id}/verify', [App\Http\Controllers\PaymentController::class, 'verify'])->name('payments.verify');
    
    // Reconciliation
    Route::get('/reconciliation', [App\Http\Controllers\ReconciliationController::class, 'index'])->name('reconciliation.index');
    Route::post('/reconciliation/{payment}/verify', [App\Http\Controllers\ReconciliationController::class, 'verify'])->name('reconciliation.verify');
    Route::get('/reconciliation/report', [App\Http\Controllers\ReconciliationController::class, 'report'])->name('reconciliation.report');
    Route::get('/reconciliation/match-bookings', [App\Http\Controllers\ReconciliationController::class, 'matchBookings'])->name('reconciliation.match-bookings');
    Route::post('/reconciliation/{booking}/create-payment', [App\Http\Controllers\ReconciliationController::class, 'createPayment'])->name('reconciliation.create-payment');
    
    // Activity Log
    Route::get('/activity-log', [App\Http\Controllers\ActivityLogController::class, 'index'])->name('activity-log.index');
    
    // CMS Content Management
    Route::resource('cms', App\Http\Controllers\CmsContentController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
    
    // Gallery Images Delete
    Route::delete('/gallery-images/{id}', [App\Http\Controllers\GalleryImageController::class, 'destroy'])->name('gallery-images.destroy');
});

// Officer Routes
Route::prefix('officer')->middleware(['auth:web,officer'])->name('officer.')->group(function () {
    Route::get('/dashboard', function () {
        return view('officer.dashboard');
    })->name('dashboard');

    // Equipment Loan Approvals
    Route::get('/loan-approvals', [App\Http\Controllers\OfficerLoanApprovalController::class, 'index'])->name('loan-approvals.index');
    Route::post('/loan-approvals/{id}/approve', [App\Http\Controllers\OfficerLoanApprovalController::class, 'approve'])->name('loans.approve');
    Route::post('/loan-approvals/{id}/reject', [App\Http\Controllers\OfficerLoanApprovalController::class, 'reject'])->name('loans.reject');

    // Returns Monitoring
    Route::get('/returns-monitor', [App\Http\Controllers\OfficerReturnMonitorController::class, 'index'])->name('returns.monitor');
    Route::post('/returns-monitor/{id}/start-return', [App\Http\Controllers\OfficerReturnMonitorController::class, 'startReturn'])->name('returns.start-return');
    Route::post('/returns-monitor/{id}/start-inspection', [App\Http\Controllers\OfficerReturnMonitorController::class, 'startInspection'])->name('returns.start-inspection');
    Route::post('/returns-monitor/{id}/process', [App\Http\Controllers\OfficerReturnMonitorController::class, 'process'])->name('returns.process');

    // Print Reports
    Route::get('/print-report', [App\Http\Controllers\OfficerReportController::class, 'print'])->name('reports.print');

    // Payments & Penalties
    Route::get('/payments', [App\Http\Controllers\OfficerPaymentController::class, 'index'])->name('payments.index');
    Route::get('/penalties', [App\Http\Controllers\OfficerPaymentController::class, 'penalties'])->name('penalties.index');
    Route::get('/penalties/export-pdf', [App\Http\Controllers\OfficerPaymentController::class, 'exportPdf'])->name('penalties.export-pdf');

    // Book Loans Management (placeholder)
    Route::get('/books', function () { return 'Book Loans Index'; })->name('books.index');
    Route::get('/books/create', function () { return 'Create Book Loan'; })->name('books.create');

    // Product Loans Management (placeholder)
    Route::get('/products', function () { return 'Product Loans Index'; })->name('products.index');
    Route::get('/products/create', function () { return 'Create Product Loan'; })->name('products.create');

    // Booking Management (New)
    Route::get('/bookings', [App\Http\Controllers\OfficerBookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{type}/{bookingId}', [App\Http\Controllers\OfficerBookingController::class, 'show'])
        ->name('bookings.show')
        ->where('type', 'product|package');
    Route::post('/bookings/{type}/{bookingId}', [App\Http\Controllers\OfficerBookingController::class, 'updateBookingData'])
        ->name('booking-detail.update')
        ->where('type', 'product|package');

    // Officer Booking Status Actions (dedicated to officer flow)
    Route::post('/book-products/{booking}/validate', [App\Http\Controllers\OfficerBookingStatusController::class, 'validateProduct'])->name('book-products.validate');
    Route::post('/book-products/{booking}/confirm', [App\Http\Controllers\OfficerBookingStatusController::class, 'confirmProduct'])->name('book-products.confirm');
    Route::post('/book-products/{booking}/prepare-pickup', [App\Http\Controllers\OfficerBookingStatusController::class, 'preparePickupProduct'])->name('book-products.prepare-pickup');
    Route::post('/book-products/{booking}/handover', [App\Http\Controllers\OfficerBookingStatusController::class, 'handoverProduct'])->name('book-products.handover');
    Route::post('/book-products/{booking}/schedule-return', [App\Http\Controllers\OfficerBookingStatusController::class, 'scheduleReturnProduct'])->name('book-products.schedule-return');
    Route::post('/book-products/{booking}/receive-return', [App\Http\Controllers\OfficerBookingStatusController::class, 'receiveReturnProduct'])->name('book-products.receive-return');
    Route::post('/book-products/{booking}/complete', [App\Http\Controllers\OfficerBookingStatusController::class, 'completeProduct'])->name('book-products.complete');
    Route::post('/book-products/{booking}/detect-issue', [App\Http\Controllers\OfficerBookingStatusController::class, 'detectIssueProduct'])->name('book-products.detect-issue');
    Route::post('/book-products/{booking}/cancel', [App\Http\Controllers\OfficerBookingStatusController::class, 'cancelProduct'])->name('book-products.cancel');

    Route::post('/book-packages/{booking}/validate', [App\Http\Controllers\OfficerBookingStatusController::class, 'validatePackage'])->name('book-packages.validate');
    Route::post('/book-packages/{booking}/confirm', [App\Http\Controllers\OfficerBookingStatusController::class, 'confirmPackage'])->name('book-packages.confirm');
    Route::post('/book-packages/{booking}/prepare-pickup', [App\Http\Controllers\OfficerBookingStatusController::class, 'preparePickupPackage'])->name('book-packages.prepare-pickup');
    Route::post('/book-packages/{booking}/handover', [App\Http\Controllers\OfficerBookingStatusController::class, 'handoverPackage'])->name('book-packages.handover');
    Route::post('/book-packages/{booking}/schedule-return', [App\Http\Controllers\OfficerBookingStatusController::class, 'scheduleReturnPackage'])->name('book-packages.schedule-return');
    Route::post('/book-packages/{booking}/receive-return', [App\Http\Controllers\OfficerBookingStatusController::class, 'receiveReturnPackage'])->name('book-packages.receive-return');
    Route::post('/book-packages/{booking}/complete', [App\Http\Controllers\OfficerBookingStatusController::class, 'completePackage'])->name('book-packages.complete');
    Route::post('/book-packages/{booking}/detect-issue', [App\Http\Controllers\OfficerBookingStatusController::class, 'detectIssuePackage'])->name('book-packages.detect-issue');
    Route::post('/book-packages/{booking}/cancel', [App\Http\Controllers\OfficerBookingStatusController::class, 'cancelPackage'])->name('book-packages.cancel');

    // Assignment workflow handled directly by officer
});

// Officer Booking Status Actions (POST routes)
Route::post('/book-products/{booking}/validate', [App\Http\Controllers\OfficerBookingStatusController::class, 'validateProduct'])->name('booking.validate')->middleware(['auth:web,officer']);
Route::post('/book-products/{booking}/confirm', [App\Http\Controllers\OfficerBookingStatusController::class, 'confirmProduct'])->name('booking.confirm')->middleware(['auth:web,officer']);
Route::post('/book-products/{booking}/prepare-pickup', [App\Http\Controllers\OfficerBookingStatusController::class, 'preparePickupProduct'])->name('booking.prepare-pickup')->middleware(['auth:web,officer']);
Route::post('/book-products/{booking}/handover', [App\Http\Controllers\OfficerBookingStatusController::class, 'handoverProduct'])->name('booking.handover')->middleware(['auth:web,officer']);
Route::post('/book-products/{booking}/schedule-return', [App\Http\Controllers\OfficerBookingStatusController::class, 'scheduleReturnProduct'])->name('booking.schedule-return')->middleware(['auth:web,officer']);
Route::post('/book-products/{booking}/receive-return', [App\Http\Controllers\OfficerBookingStatusController::class, 'receiveReturnProduct'])->name('booking.receive-return')->middleware(['auth:web,officer']);
Route::post('/book-products/{booking}/complete', [App\Http\Controllers\OfficerBookingStatusController::class, 'completeProduct'])->name('booking.complete')->middleware(['auth:web,officer']);
Route::post('/book-products/{booking}/detect-issue', [App\Http\Controllers\OfficerBookingStatusController::class, 'detectIssueProduct'])->name('booking.detect-issue')->middleware(['auth:web,officer']);
Route::post('/book-products/{booking}/cancel', [App\Http\Controllers\OfficerBookingStatusController::class, 'cancelProduct'])->name('booking.cancel')->middleware(['auth:web,officer']);

Route::post('/book-packages/{booking}/validate', [App\Http\Controllers\OfficerBookingStatusController::class, 'validatePackage'])->name('package-booking.validate')->middleware(['auth:web,officer']);
Route::post('/book-packages/{booking}/confirm', [App\Http\Controllers\OfficerBookingStatusController::class, 'confirmPackage'])->name('package-booking.confirm')->middleware(['auth:web,officer']);
Route::post('/book-packages/{booking}/prepare-pickup', [App\Http\Controllers\OfficerBookingStatusController::class, 'preparePickupPackage'])->name('package-booking.prepare-pickup')->middleware(['auth:web,officer']);
Route::post('/book-packages/{booking}/handover', [App\Http\Controllers\OfficerBookingStatusController::class, 'handoverPackage'])->name('package-booking.handover')->middleware(['auth:web,officer']);
Route::post('/book-packages/{booking}/schedule-return', [App\Http\Controllers\OfficerBookingStatusController::class, 'scheduleReturnPackage'])->name('package-booking.schedule-return')->middleware(['auth:web,officer']);
Route::post('/book-packages/{booking}/receive-return', [App\Http\Controllers\OfficerBookingStatusController::class, 'receiveReturnPackage'])->name('package-booking.receive-return')->middleware(['auth:web,officer']);
Route::post('/book-packages/{booking}/complete', [App\Http\Controllers\OfficerBookingStatusController::class, 'completePackage'])->name('package-booking.complete')->middleware(['auth:web,officer']);
Route::post('/book-packages/{booking}/detect-issue', [App\Http\Controllers\OfficerBookingStatusController::class, 'detectIssuePackage'])->name('package-booking.detect-issue')->middleware(['auth:web,officer']);
Route::post('/book-packages/{booking}/cancel', [App\Http\Controllers\OfficerBookingStatusController::class, 'cancelPackage'])->name('package-booking.cancel')->middleware(['auth:web,officer']);

// Operational handling is done by officer on-site.

// Logout Route (Available for all guards)
Route::post('/logout', function () {
    $guard = 'web';
    $actor = null;

    if (auth()->guard('admin')->check()) {
        $guard = 'admin';
        $actor = auth()->guard('admin')->user();
        auth()->guard('admin')->logout();
    } elseif (auth()->guard('officer')->check()) {
        $guard = 'officer';
        $actor = auth()->guard('officer')->user();
        auth()->guard('officer')->logout();
    } else {
        $actor = auth()->guard('web')->user();
        auth()->guard('web')->logout();
    }

    if ($actor) {
        \App\Models\ActivityLog::create([
            'log_name' => 'auth',
            'description' => 'Logout berhasil: ' . ($actor->email ?? 'unknown') . ' via guard ' . $guard,
            'subject_type' => get_class($actor),
            'subject_id' => (string) $actor->id,
            'causer_type' => get_class($actor),
            'causer_id' => (string) $actor->id,
            'event' => 'logout',
            'properties' => [
                'guard' => $guard,
                'email' => $actor->email ?? null,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ],
        ]);
    }
    
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('home');
})->name('logout');

// Scan Unit Routes (Public - accessible to all authenticated users)
Route::middleware(['auth:web,admin,officer'])->group(function () {
    Route::get('/scan-unit/{unit}', [App\Http\Controllers\ScanUnitController::class, 'show'])->name('scan-unit.show');
    Route::get('/scan-unit/{unit}/history', [App\Http\Controllers\ScanUnitController::class, 'history'])->name('scan-unit.history');
    Route::post('/scan-unit/{unit}/start-packing', [App\Http\Controllers\ScanUnitController::class, 'startPacking'])->name('scan-unit.start-packing')->middleware('auth:web,officer');
    Route::post('/scan-unit/{unit}/pickup', [App\Http\Controllers\ScanUnitController::class, 'pickupUnit'])->name('scan-unit.pickup')->middleware('auth:web,officer');
});

// QR Code Camera Scanner (for inline scanning in packing, etc.)
Route::get('/scan-unit-camera', [App\Http\Controllers\ScanUnitController::class, 'camera'])->name('scan-unit-camera')->middleware('auth:web,officer');

// API for unit lookup (for QR scanner)
Route::get('/api/units/{unit}', [App\Http\Controllers\UnitController::class, 'getUnitData'])
    ->middleware('auth:web,admin,officer')
    ->name('api.units.show');

// User Profile Routes (Protected)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [App\Http\Controllers\User\ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [App\Http\Controllers\User\ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/photo', [App\Http\Controllers\User\ProfileController::class, 'updatePhoto'])->name('profile.update-photo');
    Route::post('/profile/language', [App\Http\Controllers\User\ProfileController::class, 'switchLanguage'])->name('profile.switch-language');
    Route::get('/my-booking', [App\Http\Controllers\User\BookingController::class, 'myBooking'])->name('user.my-booking');
    Route::get('/my-returns', [App\Http\Controllers\User\BookingController::class, 'myReturns'])->name('user.my-returns');

    // User Fines Routes
    Route::get('/fines', [App\Http\Controllers\User\FineController::class, 'index'])->name('user.fines.index');
    Route::patch('/fines/{id}/verify', [App\Http\Controllers\User\FineController::class, 'verify'])->name('fines.verify');
    Route::patch('/fines/{id}/pay', [App\Http\Controllers\User\TransactionController::class, 'payPenalty'])->name('fines.pay');
    Route::get('/fines/export-pdf', [App\Http\Controllers\User\FineController::class, 'exportPdf'])->name('fines.export-pdf');

    Route::post('/booking/{type}/{booking}/pay', [App\Http\Controllers\User\TransactionController::class, 'payBooking'])
        ->where('type', 'product|package')
        ->name('user.booking.pay');
    Route::get('/payments/checkout/{transaction}', [App\Http\Controllers\User\TransactionController::class, 'checkout'])
        ->name('user.payment.checkout');

    // User Cart Routes
    Route::post('/cart/checkout', [App\Http\Controllers\User\CartController::class, 'checkout'])->name('user.cart.checkout');

    // Route for cart booking summary after checkout
    Route::get('/booking/cart', [App\Http\Controllers\User\BookingController::class, 'cartBooking'])->name('user.booking.cart');
    Route::post('/booking/cart', [App\Http\Controllers\User\BookingController::class, 'cartBooking']);


    // User Booking Routes
    Route::get('/booking/create/{product}', [App\Http\Controllers\User\BookingController::class, 'create'])->name('user.booking.create');
    Route::get('/booking/create-multi', [App\Http\Controllers\User\BookingController::class, 'createMulti'])->name('user.booking.create-multi');
    Route::post('/booking', [App\Http\Controllers\User\BookingController::class, 'store'])->name('user.booking.store');

    // User Booking Package Routes
    Route::get('/booking/package/{package}', [App\Http\Controllers\User\BookingPackageController::class, 'create'])->name('user.booking.package.create');
    Route::post('/booking/package/{package}', [App\Http\Controllers\User\BookingPackageController::class, 'store'])->name('user.booking.package.store');

    // Booking Status Routes (status management untuk officer dan admin)
    require __DIR__ . '/booking-status.php';
});
