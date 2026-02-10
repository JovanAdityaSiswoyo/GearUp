<?php

namespace App\Http\Controllers;

use App\Models\BookProduct;
use App\Models\Book;
use App\Services\BookingStatusService;
use App\Services\CourierStatusService;
use App\Enums\OrderStatus;
use App\Enums\ItemStatus;
use Illuminate\Http\Request;

/**
 * Booking Status Controller
 * Mengelola perubahan status order dan item untuk booking
 */
class BookingStatusController extends Controller
{
    public function __construct(
        protected BookingStatusService $statusService,
        protected CourierStatusService $courierService
    ) {}

    // ========== OFFICER OPERATIONS ==========

    /**
     * Officer mengvalidasi order setelah pembayaran
     */
    public function validateOrder(BookProduct $booking)
    {
        authorize('validate', $booking);

        try {
            $this->statusService->updateOrderStatus(
                $booking,
                OrderStatus::AWAITING_VALIDATION
            );

            return response()->json([
                'success' => true,
                'message' => 'Order telah divalidasi',
                'status' => $booking->order_status->label(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Officer mengkonfirmasi order
     */
    public function confirmOrder(BookProduct $booking)
    {
        authorize('confirm', $booking);

        try {
            $this->statusService->updateOrderStatus(
                $booking,
                OrderStatus::CONFIRMED
            );

            return response()->json([
                'success' => true,
                'message' => 'Order telah dikonfirmasi',
                'status' => $booking->order_status->label(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Officer menyiapkan barang untuk diambil kurir
     */
    public function prepareForPickup(Request $request, BookProduct $booking)
    {
        authorize('prepare', $booking);

        try {
            $this->statusService->updateOrderStatus(
                $booking,
                OrderStatus::READY_FOR_PICKUP
            );

            return response()->json([
                'success' => true,
                'message' => 'Barang siap untuk diambil kurir',
                'status' => $booking->order_status->label(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Officer menjadwalkan penjemputan barang
     */
    public function scheduleReturn(Request $request, BookProduct $booking)
    {
        authorize('schedule_return', $booking);

        try {
            // Validasi bahwa order sudah delivered
            if ($booking->order_status !== OrderStatus::DELIVERED) {
                throw new \Exception('Order harus sudah delivered');
            }

            $this->statusService->updateOrderStatus(
                $booking,
                OrderStatus::PICKUP_SCHEDULED
            );

            return response()->json([
                'success' => true,
                'message' => 'Penjemputan barang sudah dijadwalkan',
                'status' => $booking->order_status->label(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Officer melakukan QC dan menyelesaikan order
     */
    public function completeOrder(Request $request, BookProduct $booking)
    {
        authorize('complete', $booking);

        try {
            // Validasi bahwa barang sudah pending review
            if ($booking->order_status !== OrderStatus::PENDING_REVIEW) {
                throw new \Exception('Order harus dalam status Pending Review');
            }

            $this->statusService->updateOrderStatus(
                $booking,
                OrderStatus::COMPLETED
            );

            return response()->json([
                'success' => true,
                'message' => 'Order telah diselesaikan',
                'status' => $booking->order_status->label(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Officer mendeteksi issue/kerusakan barang
     */
    public function detectIssue(Request $request, BookProduct $booking)
    {
        authorize('detect_issue', $booking);

        $validated = $request->validate([
            'issue_notes' => 'required|string|min:10',
            'issue_photos' => 'array',
            'issue_photos.*' => 'image|mimes:jpeg,png|max:2048',
        ]);

        try {
            $additionalData = [
                'issue_notes' => $validated['issue_notes'],
            ];

            // Handle photos jika ada
            if ($request->hasFile('issue_photos')) {
                $photos = [];
                foreach ($request->file('issue_photos') as $photo) {
                    $photos[] = $photo->store('issues');
                }
                $additionalData['issue_photos'] = json_encode($photos);
            }

            $this->statusService->updateOrderStatus(
                $booking,
                OrderStatus::ISSUE_DETECTED,
                $additionalData
            );

            return response()->json([
                'success' => true,
                'message' => 'Issue telah dicatat',
                'status' => $booking->order_status->label(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    // ========== COURIER OPERATIONS ==========

    /**
     * Courier mengambil barang untuk pengiriman
     */
    public function courierPickupDelivery(Request $request, BookProduct $booking)
    {
        authorize('courier_pickup', $booking);

        $validated = $request->validate([
            'photo' => 'required|image|mimes:jpeg,png|max:2048',
        ]);

        try {
            $photoPath = $request->file('photo')->store('delivery_photos');

            $this->courierService->pickupForDelivery(
                $booking,
                $photoPath
            );

            return response()->json([
                'success' => true,
                'message' => 'Barang berhasil diambil untuk pengiriman',
                'status' => $booking->order_status->label(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Courier menyelesaikan pengiriman
     */
    public function courierCompleteDelivery(Request $request, BookProduct $booking)
    {
        authorize('courier_deliver', $booking);

        $validated = $request->validate([
            'photo' => 'required|image|mimes:jpeg,png|max:2048',
        ]);

        try {
            $photoPath = $request->file('photo')->store('delivery_photos');

            $this->courierService->completeDelivery(
                $booking,
                $photoPath
            );

            return response()->json([
                'success' => true,
                'message' => 'Pengiriman selesai',
                'status' => $booking->order_status->label(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Courier mengambil barang untuk dikembalikan
     */
    public function courierPickupReturn(Request $request, BookProduct $booking)
    {
        authorize('courier_return', $booking);

        $validated = $request->validate([
            'photo' => 'required|image|mimes:jpeg,png|max:2048',
        ]);

        try {
            $photoPath = $request->file('photo')->store('return_photos');

            $this->courierService->pickupForReturn(
                $booking,
                $photoPath
            );

            return response()->json([
                'success' => true,
                'message' => 'Barang berhasil diambil untuk dikembalikan',
                'status' => $booking->order_status->label(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Courier menyelesaikan pengembalian
     */
    public function courierCompleteReturn(Request $request, BookProduct $booking)
    {
        authorize('courier_complete_return', $booking);

        $validated = $request->validate([
            'photo' => 'required|image|mimes:jpeg,png|max:2048',
        ]);

        try {
            $photoPath = $request->file('photo')->store('return_photos');

            $this->courierService->completeReturn(
                $booking,
                $photoPath
            );

            return response()->json([
                'success' => true,
                'message' => 'Barang berhasil dikembalikan ke gudang',
                'status' => $booking->order_status->label(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    // ========== COMMON OPERATIONS ==========

    /**
     * Batalkan order (Officer/Admin)
     */
    public function cancelOrder(Request $request, BookProduct $booking)
    {
        authorize('cancel', $booking);

        $validated = $request->validate([
            'reason' => 'required|string|min:10',
        ]);

        try {
            $this->statusService->updateOrderStatus(
                $booking,
                OrderStatus::CANCELLED,
                ['cancellation_reason' => $validated['reason']]
            );

            return response()->json([
                'success' => true,
                'message' => 'Order berhasil dibatalkan',
                'status' => $booking->order_status->label(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Get status timeline
     */
    public function getTimeline(BookProduct $booking)
    {
        return response()->json(
            $this->statusService->getStatusTimeline($booking)
        );
    }

    /**
     * Get delivery status (untuk courier)
     */
    public function getDeliveryStatus(BookProduct $booking)
    {
        authorize('view', $booking);

        return response()->json(
            $this->courierService->getDeliveryStatus($booking)
        );
    }
}
