<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookingStatusController;

/**
 * Booking Status Routes
 * 
 * Routes untuk mengelola status booking (product dan package)
 * Hanya courier yang bisa mengakses operasi delivery/return yang critical
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

    // Courier routes - KRUSIAL untuk logistik
    Route::middleware(['role:courier'])->group(function () {
        // Product booking - Delivery
        Route::post('/book-products/{booking}/courier/pickup-delivery', [BookingStatusController::class, 'courierPickupDelivery'])->name('booking.courier-pickup');
        Route::post('/book-products/{booking}/courier/complete-delivery', [BookingStatusController::class, 'courierCompleteDelivery'])->name('booking.courier-deliver');
        
        // Product booking - Return
        Route::post('/book-products/{booking}/courier/pickup-return', [BookingStatusController::class, 'courierPickupReturn'])->name('booking.courier-pickup-return');
        Route::post('/book-products/{booking}/courier/complete-return', [BookingStatusController::class, 'courierCompleteReturn'])->name('booking.courier-return');

        // Package booking - Delivery
        Route::post('/books/{booking}/courier/pickup-delivery', [BookingStatusController::class, 'courierPickupDelivery'])->name('package-booking.courier-pickup');
        Route::post('/books/{booking}/courier/complete-delivery', [BookingStatusController::class, 'courierCompleteDelivery'])->name('package-booking.courier-deliver');
        
        // Package booking - Return
        Route::post('/books/{booking}/courier/pickup-return', [BookingStatusController::class, 'courierPickupReturn'])->name('package-booking.courier-pickup-return');
        Route::post('/books/{booking}/courier/complete-return', [BookingStatusController::class, 'courierCompleteReturn'])->name('package-booking.courier-return');
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

        // Delivery status (untuk courier)
        Route::get('/book-products/{booking}/delivery-status', [BookingStatusController::class, 'getDeliveryStatus'])->name('booking.delivery-status');
        Route::get('/books/{booking}/delivery-status', [BookingStatusController::class, 'getDeliveryStatus'])->name('package-booking.delivery-status');
    });
});
