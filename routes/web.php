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
    Route::resource('units', App\Http\Controllers\UnitController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
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
    Route::post('/returns-monitor/{id}/process', [App\Http\Controllers\OfficerReturnMonitorController::class, 'process'])->name('returns.process');

    // Print Reports
    Route::get('/print-report', [App\Http\Controllers\OfficerReportController::class, 'print'])->name('reports.print');

    // Book Loans Management (placeholder)
    Route::get('/books', function () { return 'Book Loans Index'; })->name('books.index');
    Route::get('/books/create', function () { return 'Create Book Loan'; })->name('books.create');

    // Product Loans Management (placeholder)
    Route::get('/products', function () { return 'Product Loans Index'; })->name('products.index');
    Route::get('/products/create', function () { return 'Create Product Loan'; })->name('products.create');

    // Payments Management
    Route::get('/payments', [App\Http\Controllers\OfficerPaymentController::class, 'index'])->name('payments.index');

    // Booking Management (New)
    Route::get('/bookings', [App\Http\Controllers\OfficerBookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{bookingId}', [App\Http\Controllers\OfficerBookingController::class, 'show'])->name('bookings.show')->where('bookingId', '[0-9]+');
    Route::post('/bookings/{bookingId}', [App\Http\Controllers\OfficerBookingController::class, 'updateBookingData'])->name('booking-detail.update')->where('bookingId', '[0-9]+');

    // Packing Management (Atomic Assignment)
    Route::get('/packing', [App\Http\Controllers\OfficerPackingController::class, 'index'])->name('packing.index');
    Route::get('/packing/{booking}', [App\Http\Controllers\OfficerPackingController::class, 'show'])->name('packing.show');
    Route::post('/packing/{booking}/assign-units', [App\Http\Controllers\OfficerPackingController::class, 'assignUnits'])->name('packing.assign');
    Route::post('/packing/scan-unit', [App\Http\Controllers\OfficerPackingController::class, 'scanUnit'])->name('packing.scan');
    Route::post('/packing/{booking}/finalize', [App\Http\Controllers\OfficerPackingController::class, 'finalizePacking'])->name('packing.finalize');
});

// Officer Booking Status Actions (POST routes)
Route::post('/book-products/{id}/validate', [App\Http\Controllers\OfficerBookingStatusController::class, 'validateProduct'])->middleware(['auth:web,officer']);
Route::post('/book-products/{id}/confirm', [App\Http\Controllers\OfficerBookingStatusController::class, 'confirmProduct'])->middleware(['auth:web,officer']);
Route::post('/book-products/{id}/prepare-pickup', [App\Http\Controllers\OfficerBookingStatusController::class, 'preparePickupProduct'])->middleware(['auth:web,officer']);
Route::post('/book-products/{id}/schedule-return', [App\Http\Controllers\OfficerBookingStatusController::class, 'scheduleReturnProduct'])->middleware(['auth:web,officer']);
Route::post('/book-products/{id}/complete', [App\Http\Controllers\OfficerBookingStatusController::class, 'completeProduct'])->middleware(['auth:web,officer']);
Route::post('/book-products/{id}/detect-issue', [App\Http\Controllers\OfficerBookingStatusController::class, 'detectIssueProduct'])->middleware(['auth:web,officer']);
Route::post('/book-products/{id}/cancel', [App\Http\Controllers\OfficerBookingStatusController::class, 'cancelProduct'])->middleware(['auth:web,officer']);

Route::post('/book-packages/{id}/validate', [App\Http\Controllers\OfficerBookingStatusController::class, 'validatePackage'])->middleware(['auth:web,officer']);
Route::post('/book-packages/{id}/confirm', [App\Http\Controllers\OfficerBookingStatusController::class, 'confirmPackage'])->middleware(['auth:web,officer']);
Route::post('/book-packages/{id}/prepare-pickup', [App\Http\Controllers\OfficerBookingStatusController::class, 'preparePickupPackage'])->middleware(['auth:web,officer']);
Route::post('/book-packages/{id}/schedule-return', [App\Http\Controllers\OfficerBookingStatusController::class, 'scheduleReturnPackage'])->middleware(['auth:web,officer']);
Route::post('/book-packages/{id}/complete', [App\Http\Controllers\OfficerBookingStatusController::class, 'completePackage'])->middleware(['auth:web,officer']);
Route::post('/book-packages/{id}/detect-issue', [App\Http\Controllers\OfficerBookingStatusController::class, 'detectIssuePackage'])->middleware(['auth:web,officer']);
Route::post('/book-packages/{id}/cancel', [App\Http\Controllers\OfficerBookingStatusController::class, 'cancelPackage'])->middleware(['auth:web,officer']);

// Courier Routes
Route::prefix('courier')->middleware(['auth:web,courier'])->name('courier.')->group(function () {
    Route::get('/dashboard', function() {
        return view('courier.dashboard');
    })->name('dashboard');
    
    // Deliveries Management (New)
    Route::get('/deliveries', [App\Http\Controllers\CourierDeliveryController::class, 'index'])->name('deliveries.index');
    Route::get('/deliveries/history', [App\Http\Controllers\CourierDeliveryController::class, 'history'])->name('deliveries.history');
    Route::get('/deliveries/{type}/{id}', [App\Http\Controllers\CourierDeliveryController::class, 'show'])->name('deliveries.show');
    
    // Returns Management
    Route::get('/returns', [App\Http\Controllers\CourierDeliveryController::class, 'returns'])->name('returns.index');
    
    // Route Batching / Map View
    Route::get('/route-map', [App\Http\Controllers\CourierDeliveryController::class, 'routeMap'])->name('route.map');
    Route::get('/route-map/data', [App\Http\Controllers\CourierDeliveryController::class, 'routeMapData'])->name('route.map.data');
});

// Courier Actions (POST routes outside auth middleware to allow AJAX)
Route::post('/book-products/{id}/courier/pickup-delivery', [App\Http\Controllers\CourierDeliveryController::class, 'pickupDelivery'])->middleware(['auth:web,courier']);
Route::post('/book-products/{id}/courier/complete-delivery', [App\Http\Controllers\CourierDeliveryController::class, 'completeDelivery'])->middleware(['auth:web,courier']);
Route::post('/book-products/{id}/courier/pickup-return', [App\Http\Controllers\CourierDeliveryController::class, 'pickupReturn'])->middleware(['auth:web,courier']);
Route::post('/book-products/{id}/courier/complete-return', [App\Http\Controllers\CourierDeliveryController::class, 'completeReturn'])->middleware(['auth:web,courier']);

Route::post('/book-packages/{id}/courier/pickup-delivery', [App\Http\Controllers\CourierDeliveryController::class, 'pickupDeliveryPackage'])->middleware(['auth:web,courier']);
Route::post('/book-packages/{id}/courier/complete-delivery', [App\Http\Controllers\CourierDeliveryController::class, 'completeDeliveryPackage'])->middleware(['auth:web,courier']);
Route::post('/book-packages/{id}/courier/pickup-return', [App\Http\Controllers\CourierDeliveryController::class, 'pickupReturnPackage'])->middleware(['auth:web,courier']);
Route::post('/book-packages/{id}/courier/complete-return', [App\Http\Controllers\CourierDeliveryController::class, 'completeReturnPackage'])->middleware(['auth:web,courier']);

// Logout Route (Available for all guards)
Route::post('/logout', function () {
    if (auth()->guard('admin')->check()) {
        auth()->guard('admin')->logout();
    } elseif (auth()->guard('officer')->check()) {
        auth()->guard('officer')->logout();
    } elseif (auth()->guard('courier')->check()) {
        auth()->guard('courier')->logout();
    } else {
        auth()->guard('web')->logout();
    }
    
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('home');
})->name('logout');

// User Profile Routes (Protected)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [App\Http\Controllers\User\ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [App\Http\Controllers\User\ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/photo', [App\Http\Controllers\User\ProfileController::class, 'updatePhoto'])->name('profile.update-photo');
    Route::post('/profile/language', [App\Http\Controllers\User\ProfileController::class, 'switchLanguage'])->name('profile.switch-language');
    Route::get('/my-booking', [App\Http\Controllers\User\BookingController::class, 'myBooking'])->name('user.my-booking');
    Route::get('/my-returns', [App\Http\Controllers\User\BookingController::class, 'myReturns'])->name('user.my-returns');


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

    // Booking Status Routes (Status management untuk courier, officer, admin)
    require __DIR__ . '/booking-status.php';
});
