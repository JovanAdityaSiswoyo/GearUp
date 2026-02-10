<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Services\AtomicAssignmentService;
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
     * Tampilkan daftar package bookings yang perlu packing
     */
    public function index(Request $request): View
    {
        $search = $request->get('search');
        
        $query = Book::with(['package', 'user', 'bookPackageProducts.product', 'bookPackageProducts.unit'])
            ->whereIn('order_status', [
                \App\Enums\OrderStatus::CONFIRMED,
                \App\Enums\OrderStatus::READY_FOR_PICKUP,
            ])
            ->latest();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('booking_code', 'like', "%{$search}%")
                  ->orWhere('booker_name', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $bookings = $query->paginate(10);

        return view('officer.packing.index', compact('bookings'));
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

        $packingList = $this->atomicService->getPackingList($booking);
        $packingProgress = $this->getPackingProgress($booking);

        return view('officer.packing.show', compact('booking', 'packingList', 'packingProgress'));
    }

    /**
     * Scan & mark unit sebagai packed
     */
    public function scanUnit(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'book_package_product_id' => 'required|uuid|exists:book_package_products,id',
            'unit_serial' => 'required|string',
        ]);

        $bookPackageProduct = \App\Models\BookPackageProduct::with('unit')->findOrFail($validated['book_package_product_id']);

        // Verify serial number matches
        if (!$bookPackageProduct->unit || $bookPackageProduct->unit->serial_number !== $validated['unit_serial']) {
            return response()->json([
                'success' => false,
                'message' => '❌ Serial number tidak sesuai! Expected: ' . ($bookPackageProduct->unit->serial_number ?? 'N/A'),
            ], 400);
        }

        // Check if already packed
        if ($bookPackageProduct->is_packed) {
            return response()->json([
                'success' => false,
                'message' => '⚠️ Unit ini sudah dipacking sebelumnya.',
            ], 400);
        }

        $officerId = auth()->guard('officer')->id();
        $success = $this->atomicService->markAsPacked($validated['book_package_product_id'], $officerId);

        if ($success) {
            return response()->json([
                'success' => true,
                'message' => '✅ Unit berhasil discan dan ditandai sebagai packed!',
                'packed_at' => now()->format('d M Y H:i'),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => '❌ Gagal menyimpan data packing.',
        ], 500);
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
        $totalItems = \App\Models\BookPackageProduct::where('id_book', $booking->id)->count();
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
