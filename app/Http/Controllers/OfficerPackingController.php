<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Services\AtomicAssignmentService;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class OfficerPackingController extends Controller
{
    protected AtomicAssignmentService $atomicService;

    public function __construct(AtomicAssignmentService $atomicService)
    {
        $this->atomicService = $atomicService;
    }

    /**
     * Tampilkan daftar bookings yang perlu packing (both products and packages)
     */
    public function index(Request $request): View
    {
        $search = $request->get('search');
        
        // Get individual product bookings
        $productBookings = \App\Models\BookProduct::with(['product.category', 'user'])
            ->whereIn('order_status', [
                \App\Enums\OrderStatus::CONFIRMED,
                \App\Enums\OrderStatus::READY_FOR_PICKUP,
            ])
            ->get()
            ->map(function($booking) {
                return (object)[
                    'id' => $booking->id,
                    'book_code' => $booking->book_code,
                    'booker_name' => $booking->booker_name,
                    'item_name' => $booking->product->name ?? 'N/A',
                    'item_type' => 'Product',
                    'order_status' => $booking->order_status,
                    'checkin_date' => $booking->checkin_appointment_start,
                    'created_at' => $booking->created_at,
                    'type' => 'book-product',
                ];
            });
        
        // Get package bookings
        $packageBookings = \App\Models\Book::with(['package', 'user'])
            ->whereIn('order_status', [
                \App\Enums\OrderStatus::CONFIRMED,
                \App\Enums\OrderStatus::READY_FOR_PICKUP,
            ])
            ->get()
            ->map(function($booking) {
                return (object)[
                    'id' => $booking->id,
                    'book_code' => $booking->book_code,
                    'booker_name' => $booking->booker_name,
                    'item_name' => $booking->package->name_package ?? 'N/A',
                    'item_type' => 'Package',
                    'order_status' => $booking->order_status,
                    'checkin_date' => $booking->checkin_appointment_start,
                    'created_at' => $booking->created_at,
                    'type' => 'book',
                ];
            });

        // Merge and filter by search
        $bookings = $productBookings->merge($packageBookings)
            ->sortByDesc('created_at');

        if ($search) {
            $bookings = $bookings->filter(function($booking) use ($search) {
                return stripos($booking->book_code, $search) !== false ||
                       stripos($booking->booker_name, $search) !== false ||
                       stripos($booking->item_name, $search) !== false;
            });
        }

        // Manual pagination
        $perPage = 10;
        $currentPage = $request->get('page', 1);
        $offset = ($currentPage - 1) * $perPage;
        
        $total = $bookings->count();
        $bookings = $bookings->slice($offset, $perPage)->values();
        
        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $bookings,
            $total,
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('officer.packing.index', ['bookings' => $paginator]);
    }

    /**
     * Tampilkan detail packing checklist
     */
    public function show(string $bookingId): View
    {
        $booking = Book::with([
            'package.products',
            'user',
            'detailBook',
            'bookPackageProducts.product',
            'bookPackageProducts.unit',
            'bookPackageProducts.packedByOfficer'
        ])->findOrFail($bookingId);

        $packageProducts = DB::table('package_products')
            ->join('products', 'package_products.id_product', '=', 'products.id')
            ->where('package_products.id_package', $booking->id_package)
            ->select('products.id', 'products.name')
            ->get();

        $packingList = $this->atomicService->getPackingList($booking);
        $packingProgress = $this->getPackingProgress($booking);

        return view('officer.packing.show', compact('booking', 'packingList', 'packingProgress', 'packageProducts'));
    }

    /**
     * Scan & mark unit sebagai packed
     */
    public function scanUnit(Request $request): JsonResponse | \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'book_package_product_id' => 'required|uuid|exists:book_package_products,id',
            'unit_serial' => 'required|string',
        ]);

        $bookPackageProduct = \App\Models\BookPackageProduct::with('unit')->findOrFail($validated['book_package_product_id']);

        // Verify serial number matches
        if (!$bookPackageProduct->unit || $bookPackageProduct->unit->serial_number !== $validated['unit_serial']) {
            $message = '❌ Serial number tidak sesuai! Expected: ' . ($bookPackageProduct->unit->serial_number ?? 'N/A');
            
            // Return JSON for AJAX requests
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                ], 400);
            }
            
            // Redirect with error for direct access
            return back()->with('error', $message);
        }

        // Check if already packed
        if ($bookPackageProduct->is_packed) {
            $message = '⚠️ Unit ini sudah dipacking sebelumnya.';
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                ], 400);
            }
            
            return back()->with('error', $message);
        }

        $officerId = auth()->guard('officer')->id();
        $success = $this->atomicService->markAsPacked($validated['book_package_product_id'], $officerId);

        if ($success) {
            $message = '✅ Unit berhasil discan dan ditandai sebagai packed!';
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'packed_at' => now()->format('d M Y H:i'),
                ]);
            }
            
            return back()->with('success', $message);
        }

        $message = '❌ Gagal menyimpan data packing.';
        
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => $message,
            ], 500);
        }
        
        return back()->with('error', $message);
    }

    /**
     * Assign units untuk package booking (atomic assignment)
     */
    public function assignUnits(string $bookingId): JsonResponse
    {
        $booking = Book::findOrFail($bookingId);

        // Check if already assigned
        $existingAssignments = \App\Models\BookPackageProduct::where('id_book', $booking->id)->count();
        if ($existingAssignments > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Units sudah di-assign untuk booking ini.',
            ], 400);
        }

        $result = $this->atomicService->assignUnitsForPackage($booking);

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => 'Berhasil assign ' . count($result['assigned']) . ' units!',
                'assigned' => $result['assigned'],
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $result['message'],
            'failures' => $result['failures'],
        ], 400);
    }

    /**
     * Get packing progress untuk booking
     */
    private function getPackingProgress(Book $booking): array
    {
        $assignedItems = \App\Models\BookPackageProduct::where('id_book', $booking->id)->count();
        $packageItems = DB::table('package_products')
            ->where('id_package', $booking->id_package)
            ->count();
        $totalItems = max($assignedItems, $packageItems);
        $packedItems = \App\Models\BookPackageProduct::where('id_book', $booking->id)
            ->where('is_packed', true)
            ->count();

        $percentage = $totalItems > 0 ? round(($packedItems / $totalItems) * 100) : 0;

        return [
            'total' => $totalItems,
            'packed' => $packedItems,
            'remaining' => $totalItems - $packedItems,
            'percentage' => $percentage,
            'is_complete' => $this->atomicService->isPackingComplete($booking),
        ];
    }

    /**
     * Finalize packing & update booking status
     */
    public function finalizePacking(string $bookingId): JsonResponse
    {
        $booking = Book::findOrFail($bookingId);

        if (!$this->atomicService->isPackingComplete($booking)) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak semua item sudah dipacking. Silakan scan semua unit terlebih dahulu.',
            ], 400);
        }

        // Update booking status ke READY_FOR_PICKUP
        $booking->order_status = \App\Enums\OrderStatus::READY_FOR_PICKUP;
        $booking->save();

        return response()->json([
            'success' => true,
            'message' => '✅ Packing selesai! Booking siap untuk pickup oleh courier.',
        ]);
    }
}
