<?php

namespace App\Services;

use App\Models\BookProduct;
use App\Models\Book;
use App\Enums\ItemStatus;
use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Model;

/**
 * Courier-specific status management
 * Hanya untuk courier, karena critical untuk logistik
 */
class CourierStatusService
{
    protected BookingStatusService $statusService;

    public function __construct(BookingStatusService $statusService)
    {
        $this->statusService = $statusService;
    }

    /**
     * Courier mengambil barang - update ke OUT_FOR_DELIVERY
     * Hanya courier yang assigned bisa melakukan ini
     * 
     * @param Model $booking
     * @param string|null $photoUrl
     * @return bool
     * @throws \Exception
     */
    public function pickupForDelivery(Model $booking, ?string $photoUrl = null): bool
    {
        if (!$this->isAssignedCourier($booking)) {
            throw new \Exception("Only assigned courier can perform this action");
        }

        if ($booking->order_status !== OrderStatus::READY_FOR_PICKUP) {
            throw new \Exception("Booking must be in 'Ready for Pickup' status");
        }

        $additionalData = [];
        if ($photoUrl) {
            $additionalData['pickup_photo'] = $photoUrl;
        }

        return $this->statusService->updateOrderStatus(
            $booking,
            OrderStatus::OUT_FOR_DELIVERY,
            $additionalData
        );
    }

    /**
     * Courier mengantarkan barang - update ke DELIVERED
     * 
     * @param Model $booking
     * @param string|null $photoUrl
     * @return bool
     * @throws \Exception
     */
    public function completeDelivery(Model $booking, ?string $photoUrl = null): bool
    {
        if (!$this->isAssignedCourier($booking)) {
            throw new \Exception("Only assigned courier can perform this action");
        }

        if ($booking->order_status !== OrderStatus::OUT_FOR_DELIVERY) {
            throw new \Exception("Booking must be 'Out for Delivery'");
        }

        $additionalData = [];
        if ($photoUrl) {
            $additionalData['delivery_photo'] = $photoUrl;
        }

        return $this->statusService->updateOrderStatus(
            $booking,
            OrderStatus::DELIVERED,
            $additionalData
        );
    }

    /**
     * Courier mengambil barang untuk dikembalikan - update ke ON_PROCESS_RETURN
     * 
     * @param Model $booking
     * @param string|null $photoUrl
     * @return bool
     * @throws \Exception
     */
    public function pickupForReturn(Model $booking, ?string $photoUrl = null): bool
    {
        if (!$this->isAssignedCourier($booking)) {
            throw new \Exception("Only assigned courier can perform this action");
        }

        if ($booking->order_status !== OrderStatus::PICKUP_SCHEDULED) {
            throw new \Exception("Booking must be in 'Pickup Scheduled' status");
        }

        $additionalData = [];
        if ($photoUrl) {
            $additionalData['return_pickup_photo'] = $photoUrl;
        }

        return $this->statusService->updateOrderStatus(
            $booking,
            OrderStatus::ON_PROCESS_RETURN,
            $additionalData
        );
    }

    /**
     * Courier mengembalikan barang ke gudang - update ke PENDING_REVIEW
     * 
     * @param Model $booking
     * @param string|null $photoUrl
     * @return bool
     * @throws \Exception
     */
    public function completeReturn(Model $booking, ?string $photoUrl = null): bool
    {
        if (!$this->isAssignedCourier($booking)) {
            throw new \Exception("Only assigned courier can perform this action");
        }

        if ($booking->order_status !== OrderStatus::ON_PROCESS_RETURN) {
            throw new \Exception("Booking must be 'On Process Return'");
        }

        $additionalData = [];
        if ($photoUrl) {
            $additionalData['return_delivery_photo'] = $photoUrl;
        }

        return $this->statusService->updateOrderStatus(
            $booking,
            OrderStatus::PENDING_REVIEW,
            $additionalData
        );
    }

    /**
     * Check apakah courier yang assign
     * 
     * @param Model $booking
     * @return bool
     */
    private function isAssignedCourier(Model $booking): bool
    {
        $user = auth()->user();
        
        // Check apakah user adalah courier
        if (!$user || !$user->hasRole('courier')) {
            return false;
        }

        // Check apakah courier ini yang assign ke booking
        return $booking->id_courier === $user->courier?->id;
    }

    /**
     * Get delivery status untuk courier
     * 
     * @param Model $booking
     * @return array
     */
    public function getDeliveryStatus(Model $booking): array
    {
        return [
            'is_assigned_to_me' => $this->isAssignedCourier($booking),
            'can_pickup_delivery' => $booking->order_status === OrderStatus::READY_FOR_PICKUP,
            'can_complete_delivery' => $booking->order_status === OrderStatus::OUT_FOR_DELIVERY,
            'can_schedule_return' => $booking->order_status === OrderStatus::DELIVERED,
            'can_pickup_return' => $booking->order_status === OrderStatus::PICKUP_SCHEDULED,
            'can_complete_return' => $booking->order_status === OrderStatus::ON_PROCESS_RETURN,
            'current_status' => $booking->order_status->label(),
            'item_status' => $booking->item_status->label(),
        ];
    }
}
