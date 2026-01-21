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

// User Routes (Regular Users)
Route::middleware(['auth:web'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

// Admin Routes
Route::prefix('admin')->middleware(['auth:web,admin'])->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');
    
    // Users Management
    Route::get('/users', function () { return 'Users Index'; })->name('users.index');
    Route::get('/users/create', function () { return 'Create User'; })->name('users.create');
    
    // Books Management
    Route::get('/books', function () { return 'Books Index'; })->name('books.index');
    Route::get('/books/create', function () { return 'Create Book'; })->name('books.create');
    
    // Products Management
    Route::get('/products', function () { return 'Products Index'; })->name('products.index');
    Route::get('/products/create', function () { return 'Create Product'; })->name('products.create');
    
    // Categories Management
    Route::get('/categories', function () { return 'Categories Index'; })->name('categories.index');
    
    // Packages Management
    Route::get('/packages', function () { return 'Packages Index'; })->name('packages.index');
    
    // Payments Management
    Route::get('/payments', function () { return 'Payments Index'; })->name('payments.index');
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
    Route::get('/payments', function () { return 'Payments Index'; })->name('payments.index');
    
    // Returns Management
    Route::get('/returns', function () { return 'Returns Index'; })->name('returns.index');
});

// Logout Route (Available for all guards)
Route::post('/logout', function () {
    if (auth()->guard('admin')->check()) {
        auth()->guard('admin')->logout();
    } elseif (auth()->guard('officer')->check()) {
        auth()->guard('officer')->logout();
    } else {
        auth()->guard('web')->logout();
    }
    
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('login');
})->name('logout');
