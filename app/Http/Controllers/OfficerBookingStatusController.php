<?php

namespace App\Http\Controllers;

use App\Models\BookProduct;
use App\Models\Book;
use App\Enums\OrderStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class OfficerBookingStatusController extends Controller
{
    /**
     * Validate BookProduct order
     */
    public function validateProduct($id): JsonResponse
    {
        return $this->updateOrderStatus(
            BookProduct::find($id),
            OrderStatus::AWAITING_VALIDATION,
            'Order berhasil divalidasi'
        );
    }

    /**
     * Confirm BookProduct order
     */
    public function confirmProduct($id): JsonResponse
    {
        return $this->updateOrderStatus(
            BookProduct::find($id),
            OrderStatus::CONFIRMED,
            'Order berhasil dikonfirmasi'
        );
    }

    /**
     * Prepare BookProduct for pickup
     */
    public function preparePickupProduct($id): JsonResponse
    {
        return $this->updateOrderStatus(
            BookProduct::find($id),
            OrderStatus::READY_FOR_PICKUP,
            'Barang siap diambil kurir'
        );
    }

    /**
     * Schedule return for BookProduct
     */
    public function scheduleReturnProduct($id): JsonResponse
    {
        return $this->updateOrderStatus(
            BookProduct::find($id),
            OrderStatus::PICKUP_SCHEDULED,
            'Penjemputan berhasil dijadwalkan'
        );
    }

    /**
     * Complete BookProduct order
     */
    public function completeProduct($id): JsonResponse
    {
        return $this->updateOrderStatus(
            BookProduct::find($id),
            OrderStatus::COMPLETED,
            'Order berhasil diselesaikan'
        );
    }

    /**
     * Detect issue on BookProduct
     */
    public function detectIssueProduct($id): JsonResponse
    {
        $notes = request('issue_notes', '');
        
        $booking = BookProduct::find($id);
        if (!$booking) {
            return response()->json(['success' => false, 'message' => 'Booking tidak ditemukan'], 404);
        }

        if (!auth()->user()->can('update', $booking)) {
            return response()->json(['success' => false, 'message' => 'Anda tidak memiliki akses'], 403);
        }

        DB::beginTransaction();
        try {
            $booking->update([
                'order_status' => OrderStatus::ISSUE_DETECTED,
                'notes' => $notes
            ]);
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Masalah berhasil dicatat'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal mencatat masalah: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel BookProduct order
     */
    public function cancelProduct($id): JsonResponse
    {
        $reason = request('reason', '');
        
        $booking = BookProduct::find($id);
        if (!$booking) {
            return response()->json(['success' => false, 'message' => 'Booking tidak ditemukan'], 404);
        }

        if (!auth()->user()->can('update', $booking)) {
            return response()->json(['success' => false, 'message' => 'Anda tidak memiliki akses'], 403);
        }

        DB::beginTransaction();
        try {
            $booking->update([
                'order_status' => OrderStatus::CANCELLED,
                'notes' => $reason
            ]);
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order berhasil dibatalkan'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal membatalkan order: ' . $e->getMessage()
            ], 500);
        }
    }

    // Package routes
    /**
     * Validate Book (Package) order
     */
    public function validatePackage($id): JsonResponse
    {
        return $this->updateOrderStatus(
            Book::find($id),
            OrderStatus::AWAITING_VALIDATION,
            'Order berhasil divalidasi'
        );
    }

    /**
     * Confirm Book (Package) order
     */
    public function confirmPackage($id): JsonResponse
    {
        return $this->updateOrderStatus(
            Book::find($id),
            OrderStatus::CONFIRMED,
            'Order berhasil dikonfirmasi'
        );
    }

    /**
     * Prepare Book (Package) for pickup
     */
    public function preparePickupPackage($id): JsonResponse
    {
        return $this->updateOrderStatus(
            Book::find($id),
            OrderStatus::READY_FOR_PICKUP,
            'Barang siap diambil kurir'
        );
    }

    /**
     * Schedule return for Book (Package)
     */
    public function scheduleReturnPackage($id): JsonResponse
    {
        return $this->updateOrderStatus(
            Book::find($id),
            OrderStatus::PICKUP_SCHEDULED,
            'Penjemputan berhasil dijadwalkan'
        );
    }

    /**
     * Complete Book (Package) order
     */
    public function completePackage($id): JsonResponse
    {
        return $this->updateOrderStatus(
            Book::find($id),
            OrderStatus::COMPLETED,
            'Order berhasil diselesaikan'
        );
    }

    /**
     * Detect issue on Book (Package)
     */
    public function detectIssuePackage($id): JsonResponse
    {
        $notes = request('issue_notes', '');
        
        $booking = Book::find($id);
        if (!$booking) {
            return response()->json(['success' => false, 'message' => 'Booking tidak ditemukan'], 404);
        }

        if (!auth()->user()->can('update', $booking)) {
            return response()->json(['success' => false, 'message' => 'Anda tidak memiliki akses'], 403);
        }

        DB::beginTransaction();
        try {
            $booking->update([
                'order_status' => OrderStatus::ISSUE_DETECTED,
                'notes' => $notes
            ]);
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Masalah berhasil dicatat'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal mencatat masalah: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel Book (Package) order
     */
    public function cancelPackage($id): JsonResponse
    {
        $reason = request('reason', '');
        
        $booking = Book::find($id);
        if (!$booking) {
            return response()->json(['success' => false, 'message' => 'Booking tidak ditemukan'], 404);
        }

        if (!auth()->user()->can('update', $booking)) {
            return response()->json(['success' => false, 'message' => 'Anda tidak memiliki akses'], 403);
        }

        DB::beginTransaction();
        try {
            $booking->update([
                'order_status' => OrderStatus::CANCELLED,
                'notes' => $reason
            ]);
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order berhasil dibatalkan'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal membatalkan order: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update order status helper method
     */
    private function updateOrderStatus($booking, $newStatus, $successMessage): JsonResponse
    {
        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking tidak ditemukan'
            ], 404);
        }

        if (!auth()->user()->can('update', $booking)) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses'
            ], 403);
        }

        DB::beginTransaction();
        try {
            $booking->update(['order_status' => $newStatus]);
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $successMessage
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah status: ' . $e->getMessage()
            ], 500);
        }
    }
}
