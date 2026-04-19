<?php

namespace App\Http\Controllers;

use App\Models\BookProduct;
use App\Models\Book;
use App\Services\BookingStatusService;
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
        protected BookingStatusService $statusService
    ) {}

    // ========== OFFICER OPERATIONS ==========

    /**
     * Officer mengvalidasi order setelah pembayaran
     */
    public function validateOrder(BookProduct $booking)
    {
        $this->authorize('validate', $booking);

        try {
            $this->statusService->updateOrderStatus(
                $booking,
                OrderStatus::PENDING
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
        $this->authorize('confirm', $booking);

        try {
            $this->statusService->updateOrderStatus(
                $booking,
                OrderStatus::PENDING
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
        $this->authorize('prepare', $booking);

        try {
            $this->statusService->updateOrderStatus(
                $booking,
                OrderStatus::PENDING
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
        $this->authorize('schedule_return', $booking);

        try {
            // Validasi bahwa order sudah delivered
            if ($booking->order_status !== OrderStatus::DIPINJAM) {
                throw new \Exception('Order harus sudah delivered');
            }

            $this->statusService->updateOrderStatus(
                $booking,
                OrderStatus::DIPINJAM
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
        $this->authorize('complete', $booking);

        try {
            if ($booking->order_status !== OrderStatus::DIPINJAM) {
                throw new \Exception('Order harus dalam status dipinjam');
            }

            $this->statusService->updateOrderStatus(
                $booking,
                OrderStatus::SELESAI
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
        $this->authorize('detect_issue', $booking);

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
                OrderStatus::SELESAI,
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

    // ========== COMMON OPERATIONS ==========

    /**
     * Batalkan order (Officer/Admin)
     */
    public function cancelOrder(Request $request, BookProduct $booking)
    {
        $this->authorize('cancel', $booking);

        $validated = $request->validate([
            'reason' => 'required|string|min:10',
        ]);

        try {
            $this->statusService->updateOrderStatus(
                $booking,
                OrderStatus::SELESAI,
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

    public function getDeliveryStatus(BookProduct $booking)
    {
        $this->authorize('view', $booking);

        return response()->json(
            [
                'can_pickup_delivery' => $booking->order_status === OrderStatus::PENDING,
                'can_complete_delivery' => $booking->order_status === OrderStatus::DIPINJAM,
                'can_schedule_return' => $booking->order_status === OrderStatus::DIPINJAM,
                'can_complete_return' => $booking->order_status === OrderStatus::DIPINJAM,
                'current_status' => $booking->order_status->label(),
                'item_status' => $booking->item_status->label(),
            ]
        );
    }
}
