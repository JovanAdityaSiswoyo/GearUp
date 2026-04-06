<?php

namespace App\Http\Controllers;

use App\Models\BookProduct;
use App\Models\Book;
use App\Models\ActivityLog;
use App\Enums\OrderStatus;
use App\Services\ItemStatusTransitionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Enums\ItemStatus;

class OfficerBookingStatusController extends Controller
{
    protected ItemStatusTransitionService $transitionService;

    public function __construct(ItemStatusTransitionService $transitionService)
    {
        $this->transitionService = $transitionService;
    }

    private function hasAuthenticatedActor(): bool
    {
        return auth('officer')->check() || auth('admin')->check() || auth('web')->check();
    }

    private function getItemStatusFromOrderStatus($orderStatus): ?ItemStatus
    {
        return match($orderStatus) {
            OrderStatus::DRAFT => ItemStatus::AVAILABLE,
            OrderStatus::CONFIRMED => ItemStatus::BOOKED,
            OrderStatus::READY_FOR_PICKUP => ItemStatus::PACKING,
            OrderStatus::OUT_FOR_DELIVERY => ItemStatus::PICKED_UP,
            OrderStatus::DELIVERED => ItemStatus::DEPLOYED,
            OrderStatus::PICKUP_SCHEDULED => ItemStatus::RETURNING,
            OrderStatus::PENDING_REVIEW => ItemStatus::IN_INSPECTION,
            OrderStatus::COMPLETED => ItemStatus::AVAILABLE,
            OrderStatus::ISSUE_DETECTED,
            OrderStatus::CANCELLED => null, // Tidak ada mapping langsung
            default => null,
        };
    }

    /**
     * Validate BookProduct order
     */
    public function validateProduct($id): JsonResponse
    {
        return $this->updateOrderStatus(
            BookProduct::find($id),
            OrderStatus::AWAITING_VALIDATION,
            'Order berhasil divalidasi',
            'validate'
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
            'Order berhasil dikonfirmasi',
            'confirm'
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
            'Barang siap diambil user di lokasi',
            'prepare_pickup'
        );
    }

    /**
     * Serahkan BookProduct langsung ke user di lokasi
     */
    public function handoverProduct($id): JsonResponse
    {
        return $this->updateOrderStatus(
            BookProduct::find($id),
            OrderStatus::DELIVERED,
            'Barang berhasil diserahkan ke user',
            'handover'
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
            'Jadwal pengembalian berhasil dicatat',
            'schedule_return'
        );
    }

    /**
     * Terima pengembalian BookProduct di lokasi
     */
    public function receiveReturnProduct($id): JsonResponse
    {
        return $this->updateOrderStatus(
            BookProduct::find($id),
            OrderStatus::PENDING_REVIEW,
            'Barang pengembalian berhasil diterima',
            'receive_return'
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
            'Order berhasil diselesaikan',
            'complete'
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

        if (!$this->hasAuthenticatedActor()) {
            return response()->json(['success' => false, 'message' => 'Anda tidak memiliki akses'], 403);
        }

        DB::beginTransaction();
        try {
            $booking->update([
                'order_status' => OrderStatus::ISSUE_DETECTED,
                'notes' => $notes
            ]);
            $this->logBookingActivity(
                $booking,
                'detect_issue',
                'Officer mencatat issue pada booking',
                [
                    'new_order_status' => OrderStatus::ISSUE_DETECTED->value,
                    'notes' => $notes,
                ]
            );
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

        if (!$this->hasAuthenticatedActor()) {
            return response()->json(['success' => false, 'message' => 'Anda tidak memiliki akses'], 403);
        }

        DB::beginTransaction();
        try {
            $booking->update([
                'order_status' => OrderStatus::CANCELLED,
                'notes' => $reason
            ]);
            $this->logBookingActivity(
                $booking,
                'cancel',
                'Officer membatalkan booking',
                [
                    'new_order_status' => OrderStatus::CANCELLED->value,
                    'reason' => $reason,
                ]
            );
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
            'Order berhasil divalidasi',
            'validate'
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
            'Order berhasil dikonfirmasi',
            'confirm'
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
            'Barang siap diambil user di lokasi',
            'prepare_pickup'
        );
    }

    /**
     * Serahkan Book (Package) langsung ke user di lokasi
     */
    public function handoverPackage($id): JsonResponse
    {
        return $this->updateOrderStatus(
            Book::find($id),
            OrderStatus::DELIVERED,
            'Barang berhasil diserahkan ke user',
            'handover'
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
            'Jadwal pengembalian berhasil dicatat',
            'schedule_return'
        );
    }

    /**
     * Terima pengembalian Book (Package) di lokasi
     */
    public function receiveReturnPackage($id): JsonResponse
    {
        return $this->updateOrderStatus(
            Book::find($id),
            OrderStatus::PENDING_REVIEW,
            'Barang pengembalian berhasil diterima',
            'receive_return'
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
            'Order berhasil diselesaikan',
            'complete'
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

        if (!$this->hasAuthenticatedActor()) {
            return response()->json(['success' => false, 'message' => 'Anda tidak memiliki akses'], 403);
        }

        DB::beginTransaction();
        try {
            $booking->update([
                'order_status' => OrderStatus::ISSUE_DETECTED,
                'notes' => $notes
            ]);
            $this->logBookingActivity(
                $booking,
                'detect_issue',
                'Officer mencatat issue pada booking',
                [
                    'new_order_status' => OrderStatus::ISSUE_DETECTED->value,
                    'notes' => $notes,
                ]
            );
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

        if (!$this->hasAuthenticatedActor()) {
            return response()->json(['success' => false, 'message' => 'Anda tidak memiliki akses'], 403);
        }

        DB::beginTransaction();
        try {
            $booking->update([
                'order_status' => OrderStatus::CANCELLED,
                'notes' => $reason
            ]);
            $this->logBookingActivity(
                $booking,
                'cancel',
                'Officer membatalkan booking',
                [
                    'new_order_status' => OrderStatus::CANCELLED->value,
                    'reason' => $reason,
                ]
            );
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
    private function updateOrderStatus($booking, $newStatus, $successMessage, string $action): JsonResponse
    {
        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking tidak ditemukan'
            ], 404);
        }

        if (!$this->hasAuthenticatedActor()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses'
            ], 403);
        }

        DB::beginTransaction();
        try {
            $previousOrderStatus = $booking->order_status?->value;
            $previousItemStatus = $booking->item_status?->value;
            
            // Get the corresponding item status from order status
            $correspondingItemStatus = $this->getItemStatusFromOrderStatus($newStatus);
            
            // Direct assignment (officers can override workflow)
            if ($correspondingItemStatus) {
                $booking->item_status = $correspondingItemStatus;
            }
            
            $booking->order_status = $newStatus;
            $booking->save();
            
            $this->logBookingActivity(
                $booking,
                $action,
                'Officer mengubah status booking',
                [
                    'previous_order_status' => $previousOrderStatus,
                    'new_order_status' => $newStatus->value,
                    'previous_item_status' => $previousItemStatus,
                    'new_item_status' => $booking->item_status?->value,
                ]
            );
            DB::commit();

            return response()->json(['success' => true, 'message' => $successMessage]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah status: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getAuthenticatedActor()
    {
        if (auth('officer')->check()) {
            return auth('officer')->user();
        }

        if (auth('admin')->check()) {
            return auth('admin')->user();
        }

        if (auth('web')->check()) {
            return auth('web')->user();
        }

        return null;
    }

    private function logBookingActivity($booking, string $event, string $description, array $properties = []): void
    {
        $actor = $this->getAuthenticatedActor();

        ActivityLog::create([
            'log_name' => 'booking_status',
            'description' => $description,
            'subject_type' => get_class($booking),
            'subject_id' => (string) $booking->id,
            'causer_type' => $actor ? get_class($actor) : null,
            'causer_id' => $actor ? (string) $actor->id : null,
            'event' => $event,
            'properties' => array_merge([
                'book_code' => $booking->book_code ?? null,
                'booking_type' => $booking instanceof BookProduct ? 'product' : 'package',
            ], $properties),
        ]);
    }
}
