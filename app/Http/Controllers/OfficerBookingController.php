<?php

namespace App\Http\Controllers;

use App\Models\BookProduct;
use App\Models\Book;
use App\Enums\OrderStatus;
use Illuminate\View\View;

/**
 * Officer Booking Management Controller
 */
class OfficerBookingController extends Controller
{
    /**
     * Tampilkan semua booking untuk management
     */
    public function index(): View
    {
        $filter = request('filter', 'all');

        $query = collect();
        $bookQuery = collect();

        // Filter product bookings
        if ($filter === 'all') {
            $bookProducts = BookProduct::with(['product.category', 'product.brand', 'user'])->latest()->get();
            $books = Book::with(['package', 'user'])->latest()->get();
        } elseif ($filter === 'draft') {
            $bookProducts = BookProduct::with(['product.category', 'product.brand', 'user'])->where('order_status', OrderStatus::DRAFT)->latest()->get();
            $books = Book::with(['package', 'user'])->where('order_status', OrderStatus::DRAFT)->latest()->get();
        } elseif ($filter === 'awaiting_validation') {
            $bookProducts = BookProduct::with(['product.category', 'product.brand', 'user'])->where('order_status', OrderStatus::AWAITING_VALIDATION)->latest()->get();
            $books = Book::with(['package', 'user'])->where('order_status', OrderStatus::AWAITING_VALIDATION)->latest()->get();
        } elseif ($filter === 'confirmed') {
            $bookProducts = BookProduct::with(['product.category', 'product.brand', 'user'])->where('order_status', OrderStatus::CONFIRMED)->latest()->get();
            $books = Book::with(['package', 'user'])->where('order_status', OrderStatus::CONFIRMED)->latest()->get();
        } elseif ($filter === 'delivery') {
            $bookProducts = BookProduct::with(['product.category', 'product.brand', 'user'])->whereIn('order_status', [
                OrderStatus::READY_FOR_PICKUP,
                OrderStatus::OUT_FOR_DELIVERY,
                OrderStatus::DELIVERED
            ])->latest()->get();
            $books = Book::with(['package', 'user'])->whereIn('order_status', [
                OrderStatus::READY_FOR_PICKUP,
                OrderStatus::OUT_FOR_DELIVERY,
                OrderStatus::DELIVERED
            ])->latest()->get();
        } elseif ($filter === 'return') {
            $bookProducts = BookProduct::with(['product.category', 'product.brand', 'user'])->whereIn('order_status', [
                OrderStatus::PICKUP_SCHEDULED,
                OrderStatus::ON_PROCESS_RETURN,
                OrderStatus::PENDING_REVIEW
            ])->latest()->get();
            $books = Book::with(['package', 'user'])->whereIn('order_status', [
                OrderStatus::PICKUP_SCHEDULED,
                OrderStatus::ON_PROCESS_RETURN,
                OrderStatus::PENDING_REVIEW
            ])->latest()->get();
        } elseif ($filter === 'completed') {
            $bookProducts = BookProduct::with(['product.category', 'product.brand', 'user'])->where('order_status', OrderStatus::COMPLETED)->latest()->get();
            $books = Book::with(['package', 'user'])->where('order_status', OrderStatus::COMPLETED)->latest()->get();
        } else {
            $bookProducts = collect();
            $books = collect();
        }

        return view('officer.bookings-management', [
            'bookProducts' => $bookProducts ?? collect(),
            'books' => $books ?? collect(),
        ]);
    }

    /**
     * Tampilkan detail booking
     */
    public function show($bookingId): View
    {
        // Try to find as BookProduct first
        $booking = BookProduct::find($bookingId);
        
        // If not found, try to find as Book
        if (!$booking) {
            $booking = Book::find($bookingId);
        }

        // If still not found, abort
        if (!$booking) {
            abort(404, 'Booking tidak ditemukan');
        }

        // Authorize dapat melihat detail
        authorize('view', $booking);

        // Load relationships
        if ($booking instanceof BookProduct) {
            $booking->load('product.category', 'product.brand', 'user');
        } else {
            $booking->load('package', 'user');
        }

        return view('officer.booking-detail', [
            'booking' => $booking,
        ]);
    }

    /**
     * Update booking data via AJAX (from detail modal)
     */
    public function updateBookingData(Request $request, $bookingId)
    {
        try {
            // Try to find as BookProduct first
            $booking = BookProduct::find($bookingId);
            
            // If not found, try to find as Book
            if (!$booking) {
                $booking = Book::find($bookingId);
            }

            // If still not found, abort
            if (!$booking) {
                return response()->json(['success' => false, 'message' => 'Booking tidak ditemukan'], 404);
            }

            // Validate
            $validated = $request->validate([
                'order_status' => 'required|string',
            ]);

            // Update
            $booking->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Status booking berhasil diperbarui',
                'data' => ['status' => $booking->order_status->label()]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi error: ' . $e->getMessage()
            ], 500);
        }
    }
}

