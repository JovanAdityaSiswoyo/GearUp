<?php

namespace App\Http\Controllers;

use App\Models\BookProduct;
use App\Models\Book;
use App\Enums\OrderStatus;
use Illuminate\Http\Request;
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
        $searchType = request('search_type', 'all');
        $searchQuery = request('search_query', '');
        $perPage = 5;

        // Base queries with relations
        $bookProductsQuery = BookProduct::with(['product.category', 'product.brand', 'user']);
        $booksQuery = Book::with(['package', 'user']);

        // Apply search filter if search query exists
        if ($searchQuery) {
            if ($searchType === 'booking_id') {
                $bookProductsQuery->where('book_code', 'LIKE', "%{$searchQuery}%");
                $booksQuery->where('book_code', 'LIKE', "%{$searchQuery}%");
            } elseif ($searchType === 'user_name') {
                $bookProductsQuery->where('booker_name', 'LIKE', "%{$searchQuery}%")
                    ->orWhere('booker_telp', 'LIKE', "%{$searchQuery}%");
                $booksQuery->where('booker_name', 'LIKE', "%{$searchQuery}%")
                    ->orWhere('booker_telp', 'LIKE', "%{$searchQuery}%");
            } elseif ($searchType === 'product_name') {
                $bookProductsQuery->whereHas('product', function ($q) use ($searchQuery) {
                    $q->where('name', 'LIKE', "%{$searchQuery}%");
                });
                $booksQuery->whereHas('package', function ($q) use ($searchQuery) {
                    $q->where('name_package', 'LIKE', "%{$searchQuery}%");
                });
            } elseif ($searchType === 'all') {
                // Search across all fields
                $bookProductsQuery->where(function ($q) use ($searchQuery) {
                    $q->where('book_code', 'LIKE', "%{$searchQuery}%")
                        ->orWhere('booker_name', 'LIKE', "%{$searchQuery}%")
                        ->orWhere('booker_telp', 'LIKE', "%{$searchQuery}%")
                        ->orWhereHas('product', function ($subQ) use ($searchQuery) {
                            $subQ->where('name', 'LIKE', "%{$searchQuery}%");
                        });
                });

                $booksQuery->where(function ($q) use ($searchQuery) {
                    $q->where('book_code', 'LIKE', "%{$searchQuery}%")
                        ->orWhere('booker_name', 'LIKE', "%{$searchQuery}%")
                        ->orWhere('booker_telp', 'LIKE', "%{$searchQuery}%")
                        ->orWhereHas('package', function ($subQ) use ($searchQuery) {
                            $subQ->where('name_package', 'LIKE', "%{$searchQuery}%");
                        });
                });
            }
        }

        // Apply status filter and pagination
        if ($filter === 'draft') {
            $bookProducts = $bookProductsQuery->where('order_status', OrderStatus::DRAFT)->latest()->paginate($perPage, ['*'], 'product_page');
            $books = $booksQuery->where('order_status', OrderStatus::DRAFT)->latest()->paginate($perPage, ['*'], 'package_page');
        } elseif ($filter === 'awaiting_validation') {
            $bookProducts = $bookProductsQuery->where('order_status', OrderStatus::AWAITING_VALIDATION)->latest()->paginate($perPage, ['*'], 'product_page');
            $books = $booksQuery->where('order_status', OrderStatus::AWAITING_VALIDATION)->latest()->paginate($perPage, ['*'], 'package_page');
        } elseif ($filter === 'confirmed') {
            $bookProducts = $bookProductsQuery->where('order_status', OrderStatus::CONFIRMED)->latest()->paginate($perPage, ['*'], 'product_page');
            $books = $booksQuery->where('order_status', OrderStatus::CONFIRMED)->latest()->paginate($perPage, ['*'], 'package_page');
        } elseif ($filter === 'delivery') {
            $bookProducts = $bookProductsQuery->whereIn('order_status', [
                OrderStatus::READY_FOR_PICKUP,
                OrderStatus::OUT_FOR_DELIVERY,
                OrderStatus::DELIVERED
            ])->latest()->paginate($perPage, ['*'], 'product_page');
            $books = $booksQuery->whereIn('order_status', [
                OrderStatus::READY_FOR_PICKUP,
                OrderStatus::OUT_FOR_DELIVERY,
                OrderStatus::DELIVERED
            ])->latest()->paginate($perPage, ['*'], 'package_page');
        } elseif ($filter === 'return') {
            $bookProducts = $bookProductsQuery->whereIn('order_status', [
                OrderStatus::PICKUP_SCHEDULED,
                OrderStatus::ON_PROCESS_RETURN,
                OrderStatus::PENDING_REVIEW
            ])->latest()->paginate($perPage, ['*'], 'product_page');
            $books = $booksQuery->whereIn('order_status', [
                OrderStatus::PICKUP_SCHEDULED,
                OrderStatus::ON_PROCESS_RETURN,
                OrderStatus::PENDING_REVIEW
            ])->latest()->paginate($perPage, ['*'], 'package_page');
        } elseif ($filter === 'completed') {
            $bookProducts = $bookProductsQuery->where('order_status', OrderStatus::COMPLETED)->latest()->paginate($perPage, ['*'], 'product_page');
            $books = $booksQuery->where('order_status', OrderStatus::COMPLETED)->latest()->paginate($perPage, ['*'], 'package_page');
        } else {
            // Default to 'all' filter
            $bookProducts = $bookProductsQuery->latest()->paginate($perPage, ['*'], 'product_page');
            $books = $booksQuery->latest()->paginate($perPage, ['*'], 'package_page');
        }

        return view('officer.bookings-management', [
            'bookProducts' => $bookProducts ?? collect(),
            'books' => $books ?? collect(),
            'searchQuery' => $searchQuery,
            'searchType' => $searchType,
        ]);
    }

    /**
     * Tampilkan detail booking
     */
    public function show(string $type, string $bookingId): View
    {
        $booking = $this->resolveBookingByType($type, $bookingId);

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
    public function updateBookingData(Request $request, string $type, string $bookingId)
    {
        try {
            $booking = $this->resolveBookingByType($type, $bookingId);

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

    private function resolveBookingByType(string $type, string $bookingId): BookProduct|Book
    {
        if ($type === 'product') {
            return BookProduct::findOrFail($bookingId);
        }

        if ($type === 'package') {
            return Book::findOrFail($bookingId);
        }

        abort(404, 'Tipe booking tidak valid');
    }
}

