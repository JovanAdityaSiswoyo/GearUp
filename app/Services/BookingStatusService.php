<?php

namespace App\Services;

use App\Models\BookProduct;
use App\Models\Book;
use App\Enums\ItemStatus;
use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * Service untuk mengelola status transitions pada booking
 * Memastikan validasi dan aturan bisnis untuk perubahan status
 */
class BookingStatusService
{
    protected ItemStatusTransitionService $transitionService;

    public function __construct(ItemStatusTransitionService $transitionService)
    {
        $this->transitionService = $transitionService;
    }

    /**
     * Update status order dengan validasi
     * 
     * @param Model $booking BookProduct atau Book
     * @param OrderStatus|string $newStatus
     * @param array $additionalData data tambahan untuk disimpan
     * @return bool
     * @throws \Exception
     */
    public function updateOrderStatus(Model $booking, OrderStatus|string $newStatus, array $additionalData = []): bool
    {
        if (is_string($newStatus)) {
            $newStatus = OrderStatus::tryFrom($newStatus);
            if (!$newStatus) {
                throw new \Exception("Invalid order status provided");
            }
        }

        // Validasi status transition yang diizinkan
        $this->validateStatusTransition($booking->order_status, $newStatus);

        $updateData = ['order_status' => $newStatus];
        
        $updateData['item_status'] = match ($newStatus) {
            OrderStatus::PENDING => ItemStatus::BOOKED,
            OrderStatus::DIPINJAM => ItemStatus::DEPLOYED,
            OrderStatus::SELESAI => ItemStatus::AVAILABLE,
        };

        // Handle additional timestamps
        if ($newStatus === OrderStatus::DIPINJAM) {
            $updateData['delivery_at'] = now();
        }

        if ($newStatus === OrderStatus::SELESAI) {
            $updateData['returned_at'] = now();
        }

        // Merge dengan additional data dan save
        $updateData = array_merge($updateData, $additionalData);
        $booking->update($updateData);

        return true;
    }

    /**
     * Update status item dengan validasi
     * 
     * @param Model $booking BookProduct atau Book
     * @param ItemStatus|string $newStatus
     * @return bool
     * @throws \Exception
     */
    public function updateItemStatus(Model $booking, ItemStatus|string $newStatus): bool
    {
        if (is_string($newStatus)) {
            $newStatus = ItemStatus::tryFrom($newStatus);
            if (!$newStatus) {
                throw new \Exception("Invalid item status provided");
            }
        }

        // Use transition service for validation
        try {
            $this->transitionService->transitionItemStatus($booking, $newStatus);
        } catch (\Exception $e) {
            throw new \Exception("Cannot change item status: " . $e->getMessage());
        }

        return true;
    }

    /**
     * Validasi status transition
     * 
     * @param OrderStatus $currentStatus
     * @param OrderStatus $newStatus
     * @throws \Exception
     */
    private function validateStatusTransition(OrderStatus $currentStatus, OrderStatus $newStatus): void
    {
        $allowedNextStatuses = $currentStatus->nextStatuses();

        if (!in_array($newStatus, $allowedNextStatuses)) {
            $current = $currentStatus->value;
            $new = $newStatus->value;
            throw new \Exception(
                "Cannot transition from '{$current}' to '{$new}'. Allowed next statuses: " .
                implode(', ', array_map(fn($s) => $s->value, $allowedNextStatuses))
            );
        }
    }

    /**
     * Check apakah item status bisa diubah
     * 
     * @param Model $booking
     * @param ItemStatus $newStatus
     * @return bool
     */
    private function canChangeItemStatus(Model $booking, ItemStatus $newStatus): bool
    {
        // Status LOST_SCRAPPED hanya bisa diset oleh admin
        if ($newStatus === ItemStatus::LOST_SCRAPPED) {
            $actor = Auth::user();
            return $actor && method_exists($actor, 'hasRole')
                ? (bool) call_user_func([$actor, 'hasRole'], 'admin')
                : false;
        }

        // Tidak bisa mengubah status jika order sudah selesai
        if (!$booking->order_status->isActive()) {
            return false;
        }

        return true;
    }

    /**
     * Get status tracking timeline untuk booking
     * 
     * @param Model $booking
     * @return array
     */
    public function getStatusTimeline(Model $booking): array
    {
        return [
            'order_status' => [
                'current' => $booking->order_status,
                'label' => $booking->order_status->label(),
                'phase' => $booking->order_status->phase(),
                'description' => $booking->order_status->description(),
                'next_possible' => array_map(
                    fn($s) => ['value' => $s->value, 'label' => $s->label()],
                    $booking->order_status->nextStatuses()
                ),
            ],
            'item_status' => [
                'current' => $booking->item_status,
                'label' => $booking->item_status->label(),
                'responsible_role' => $booking->item_status->responsibleRole(),
                'description' => $booking->item_status->description(),
            ],
            'dates' => [
                'booking_date' => $booking->created_at,
                'delivery_date' => $booking->delivery_at,
                'return_date' => $booking->returned_at,
                'checkin_start' => $booking->checkin_appointment_start,
                'checkout_end' => $booking->checkout_appointment_end,
            ],
        ];
    }

    /**
     * Check apakah order sedang di fase delivery
     * 
     * @param Model $booking
     * @return bool
     */
    public function isInDeliveryPhase(Model $booking): bool
    {
        return $booking->order_status->isInDeliveryPhase();
    }

    /**
     * Check apakah order sedang di fase return
     * 
     * @param Model $booking
     * @return bool
     */
    public function isInReturnPhase(Model $booking): bool
    {
        return $booking->order_status->isInReturnPhase();
    }

    /**
     * Check apakah order masih aktif (bisa dimodifikasi)
     * 
     * @param Model $booking
     * @return bool
     */
    public function isBookingActive(Model $booking): bool
    {
        return $booking->order_status->isActive();
    }

}
