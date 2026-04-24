<?php

namespace App\Http\Controllers;

use App\Models\BookProduct;
use App\Models\Book;
use App\Models\ActivityLog;
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

        $statusByFilter = [
            'draft' => OrderStatus::PENDING,
            'awaiting_validation' => OrderStatus::PENDING,
            'confirmed' => OrderStatus::PENDING,
            'pending' => OrderStatus::PENDING,
            'delivery' => OrderStatus::DIPINJAM,
            'return' => OrderStatus::DIPINJAM,
            'dipinjam' => OrderStatus::DIPINJAM,
            'completed' => OrderStatus::SELESAI,
            'selesai' => OrderStatus::SELESAI,
        ];

        if (isset($statusByFilter[$filter])) {
            $targetStatus = $statusByFilter[$filter];
            $bookProducts = $bookProductsQuery
                ->where('order_status', $targetStatus)
                ->latest()
                ->paginate($perPage, ['*'], 'product_page');
            $books = $booksQuery
                ->where('order_status', $targetStatus)
                ->latest()
                ->paginate($perPage, ['*'], 'package_page');
        } else {
            // Default to all statuses
            $bookProducts = $bookProductsQuery->latest()->paginate($perPage, ['*'], 'product_page');
            $books = $booksQuery->latest()->paginate($perPage, ['*'], 'package_page');
        }

        $approvedProductIds = ActivityLog::query()
            ->where('log_name', 'booking_status')
            ->where('subject_type', BookProduct::class)
            ->whereIn('event', ['validate', 'confirm'])
            ->pluck('subject_id')
            ->map(static fn($id) => (string) $id)
            ->flip()
            ->all();

        $approvedPackageIds = ActivityLog::query()
            ->where('log_name', 'booking_status')
            ->where('subject_type', Book::class)
            ->whereIn('event', ['validate', 'confirm'])
            ->pluck('subject_id')
            ->map(static fn($id) => (string) $id)
            ->flip()
            ->all();

        return view('officer.bookings-management', [
            'bookProducts' => $bookProducts ?? collect(),
            'books' => $books ?? collect(),
            'searchQuery' => $searchQuery,
            'searchType' => $searchType,
            'approvedProductIds' => $approvedProductIds,
            'approvedPackageIds' => $approvedPackageIds,
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
            $booking->load('product.category', 'product.brand', 'user', 'detailBookProduct');
        } else {
            $booking->load('package', 'user', 'detailBook');
        }

        $isApproved = $this->hasOfficerApproval($booking);

        return view('officer.booking-detail', [
            'booking' => $booking,
            'isApproved' => $isApproved,
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

            $targetStatus = OrderStatus::tryFrom($validated['order_status']);
            if (!$targetStatus) {
                throw new \Exception('Status booking tidak valid');
            }

            if (
                $targetStatus === OrderStatus::DIPINJAM &&
                $booking->order_status !== OrderStatus::DIPINJAM &&
                !$this->hasOfficerApproval($booking)
            ) {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking harus di-approve officer terlebih dahulu lewat Booking Management sebelum status dipinjam.',
                ], 422);
            }

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

    private function hasOfficerApproval(BookProduct|Book $booking): bool
    {
        return ActivityLog::query()
            ->where('log_name', 'booking_status')
            ->where('subject_type', get_class($booking))
            ->where('subject_id', (string) $booking->id)
            ->whereIn('event', ['validate', 'confirm'])
            ->exists();
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

