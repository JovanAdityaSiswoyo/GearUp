<?php

use Illuminate\Support\Facades\Route;

// Landing Page (Public)
Route::get('/', function () {
    return view('home');
})->name('home');

// Guest Routes (Not Authenticated)
Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');
    
    Route::get('/register', function () {
        return view('auth.register');
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
    
    // Categories Management
    Route::resource('categories', App\Http\Controllers\CategoryController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
    
    // Brands Management
    Route::resource('brands', App\Http\Controllers\BrandController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
    
    // Packages Management
    Route::resource('packages', App\Http\Controllers\PackageController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
    
    // Bookings/Peminjaman Management
    Route::resource('bookings', App\Http\Controllers\BookingController::class)->except(['show']);
    
    // Returns/Pengembalian Management
    Route::get('/returns', [App\Http\Controllers\ReturnController::class, 'index'])->name('returns.index');
    Route::get('/returns/{return}', [App\Http\Controllers\ReturnController::class, 'show'])->name('returns.show');
    Route::post('/returns/{return}/process', [App\Http\Controllers\ReturnController::class, 'process'])->name('returns.process');
    
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
    
    // Book Loans Management
    Route::get('/books', function () { return 'Book Loans Index'; })->name('books.index');
    Route::get('/books/create', function () { return 'Create Book Loan'; })->name('books.create');
    
    // Product Loans Management
    Route::get('/products', function () { return 'Product Loans Index'; })->name('products.index');
    Route::get('/products/create', function () { return 'Create Product Loan'; })->name('products.create');
    
    // Payments Management
    Route::get('/payments', [App\Http\Controllers\OfficerPaymentController::class, 'index'])->name('payments.index');
    
    // Returns Management
    Route::get('/returns', function () { return 'Returns Index'; })->name('returns.index');
});

// Courier Routes
Route::prefix('courier')->middleware(['auth:web,courier'])->name('courier.')->group(function () {
    Route::get('/dashboard', function () {
        return view('courier.dashboard');
    })->name('dashboard');
    
    // Deliveries Management
    Route::get('/deliveries', function () { return 'Deliveries Index'; })->name('deliveries.index');
    Route::get('/deliveries/create', function () { return 'Create Delivery'; })->name('deliveries.create');
    
    // Pickups Management
    Route::get('/pickups', function () { return 'Pickups Index'; })->name('pickups.index');
    Route::get('/pickups/create', function () { return 'Create Pickup'; })->name('pickups.create');
    
    // Returns Management
    Route::get('/returns', function () { return 'Returns Index'; })->name('returns.index');
    
    // Tracking Management
    Route::get('/tracking', function () { return 'Tracking Index'; })->name('tracking.index');
});

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
    
    // User Booking Routes
    Route::get('/booking/create/{product}', [App\Http\Controllers\User\BookingController::class, 'create'])->name('user.booking.create');
    Route::post('/booking', [App\Http\Controllers\User\BookingController::class, 'store'])->name('user.booking.store');
});
