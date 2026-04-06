<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookingStatusController;

/**
 * Booking Status Routes
 * 
 * Routes untuk mengelola status booking (product dan package)
 * Operasional dijalankan oleh officer tanpa role courier
 */

Route::middleware(['auth', 'verified'])->group(function () {
    
    // Officer routes
    Route::middleware(['role:officer'])->group(function () {
        // Product booking status
        Route::post('/book-products/{booking}/validate', [BookingStatusController::class, 'validateOrder'])->name('booking.validate');
        Route::post('/book-products/{booking}/confirm', [BookingStatusController::class, 'confirmOrder'])->name('booking.confirm');
        Route::post('/book-products/{booking}/prepare-pickup', [BookingStatusController::class, 'prepareForPickup'])->name('booking.prepare-pickup');
        Route::post('/book-products/{booking}/schedule-return', [BookingStatusController::class, 'scheduleReturn'])->name('booking.schedule-return');
        Route::post('/book-products/{booking}/complete', [BookingStatusController::class, 'completeOrder'])->name('booking.complete');
        Route::post('/book-products/{booking}/detect-issue', [BookingStatusController::class, 'detectIssue'])->name('booking.detect-issue');

        // Package booking status
        Route::post('/books/{booking}/validate', [BookingStatusController::class, 'validateOrder'])->name('package-booking.validate');
        Route::post('/books/{booking}/confirm', [BookingStatusController::class, 'confirmOrder'])->name('package-booking.confirm');
        Route::post('/books/{booking}/prepare-pickup', [BookingStatusController::class, 'prepareForPickup'])->name('package-booking.prepare-pickup');
        Route::post('/books/{booking}/schedule-return', [BookingStatusController::class, 'scheduleReturn'])->name('package-booking.schedule-return');
        Route::post('/books/{booking}/complete', [BookingStatusController::class, 'completeOrder'])->name('package-booking.complete');
        Route::post('/books/{booking}/detect-issue', [BookingStatusController::class, 'detectIssue'])->name('package-booking.detect-issue');
    });

    // Admin/Officer routes
    Route::middleware(['role:admin,officer'])->group(function () {
        // Product booking - Cancel
        Route::post('/book-products/{booking}/cancel', [BookingStatusController::class, 'cancelOrder'])->name('booking.cancel');

        // Package booking - Cancel
        Route::post('/books/{booking}/cancel', [BookingStatusController::class, 'cancelOrder'])->name('package-booking.cancel');
    });

    // Authenticated users
    Route::group([], function () {
        // Get timeline
        Route::get('/book-products/{booking}/timeline', [BookingStatusController::class, 'getTimeline'])->name('booking.timeline');
        Route::get('/books/{booking}/timeline', [BookingStatusController::class, 'getTimeline'])->name('package-booking.timeline');

        // Delivery status
        Route::get('/book-products/{booking}/delivery-status', [BookingStatusController::class, 'getDeliveryStatus'])->name('booking.delivery-status');
        Route::get('/books/{booking}/delivery-status', [BookingStatusController::class, 'getDeliveryStatus'])->name('package-booking.delivery-status');
    });
});
