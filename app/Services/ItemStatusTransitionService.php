<?php

namespace App\Services;

use App\Enums\ItemStatus;
use App\Enums\OrderStatus;
use App\Models\BookProduct;
use App\Models\Book;
use Exception;

/**
 * Service untuk handle transisi status item dengan validation
 * Mencegah transisi yang tidak valid dan memastikan alur operasional benar
 */
class ItemStatusTransitionService
{
    /**
     * Aturan transisi yang valid untuk ItemStatus
     * Key: status asal, Value: array status tujuan yang diperbolehkan
     */
    private const VALID_TRANSITIONS = [
        ItemStatus::AVAILABLE->value => [
            ItemStatus::BOOKED->value,
        ],
        ItemStatus::BOOKED->value => [
            ItemStatus::PACKING->value,
            ItemStatus::AVAILABLE->value, // Jika booking dibatalkan
        ],
        ItemStatus::PACKING->value => [
            ItemStatus::PICKED_UP->value,
            ItemStatus::BOOKED->value, // Rollback jika ada masalah
        ],
        ItemStatus::PICKED_UP->value => [
            ItemStatus::DEPLOYED->value,
        ],
        ItemStatus::DEPLOYED->value => [
            ItemStatus::RETURNING->value,
        ],
        ItemStatus::RETURNING->value => [
            ItemStatus::IN_INSPECTION->value, // HARUS lewat inspection, tidak boleh langsung Available
        ],
        ItemStatus::IN_INSPECTION->value => [
            ItemStatus::AVAILABLE->value,    // Jika barang OK
            ItemStatus::MAINTENANCE->value,   // Jika perlu perbaikan
            ItemStatus::LOST_SCRAPPED->value, // Jika rusak total
        ],
        ItemStatus::MAINTENANCE->value => [
            ItemStatus::IN_INSPECTION->value, // Setelah diperbaiki, harus dicek lagi
        ],
        ItemStatus::LOST_SCRAPPED->value => [
            // Terminal status, tidak bisa transisi
        ],
    ];

    /**
     * Validasi apakah transisi status valid
     * 
     * @param ItemStatus $from Status asal
     * @param ItemStatus $to Status tujuan
     * @return bool
     */
    public function isValidTransition(ItemStatus $from, ItemStatus $to): bool
    {
        $allowedTransitions = self::VALID_TRANSITIONS[$from->value] ?? [];
        return in_array($to->value, $allowedTransitions);
    }

    /**
     * Transition Gate: Update item status dengan validation
     * 
     * @param BookProduct|Book $booking
     * @param ItemStatus $newStatus
    * @param array $additionalData Data tambahan (timestamps, dll)
     * @throws Exception jika transisi tidak valid
     */
    public function transitionItemStatus($booking, ItemStatus $newStatus, array $additionalData = []): void
    {
        $currentStatus = $booking->item_status;

        // Validasi transisi
        if (!$this->isValidTransition($currentStatus, $newStatus)) {
            throw new Exception(
                "Invalid status transition: Cannot change from '{$currentStatus->label()}' to '{$newStatus->label()}'. " .
                "Valid transitions: " . implode(', ', $this->getValidNextStatuses($currentStatus))
            );
        }

        // CRITICAL CHECK: Returning TIDAK BOLEH langsung ke Available
        if ($currentStatus === ItemStatus::RETURNING && $newStatus === ItemStatus::AVAILABLE) {
            throw new Exception(
                "CRITICAL: Item returning from deployment MUST go through In-Inspection first. " .
                "This prevents next user from receiving dirty or damaged items."
            );
        }

        // Update status
        $booking->item_status = $newStatus;

        // Handover ke user: catat timestamp saat Picked-Up
        if ($newStatus === ItemStatus::PICKED_UP) {
            $booking->picked_up_at = now();
            $booking->id_courier = null;
        }

        // Catat timestamp untuk status lainnya
        if ($newStatus === ItemStatus::DEPLOYED) {
            $booking->delivery_at = now();
        }

        if ($newStatus === ItemStatus::RETURNING) {
            $booking->return_started_at = now();
        }

        if ($newStatus === ItemStatus::IN_INSPECTION) {
            $booking->inspection_started_at = now();
        }

        $booking->save();
    }

    /**
     * Get daftar status valid berikutnya dari status saat ini
     * 
     * @param ItemStatus $currentStatus
     * @return array
     */
    public function getValidNextStatuses(ItemStatus $currentStatus): array
    {
        $allowedValues = self::VALID_TRANSITIONS[$currentStatus->value] ?? [];
        
        return array_map(function($value) {
            return ItemStatus::from($value)->label();
        }, $allowedValues);
    }

    /**
     * Transition Gate untuk OrderStatus
     * Mapping antara ItemStatus dan OrderStatus yang sesuai
     */
    public function syncOrderStatus($booking, ItemStatus $itemStatus): void
    {
        $orderStatus = match($itemStatus) {
            ItemStatus::AVAILABLE => OrderStatus::DRAFT,
            ItemStatus::BOOKED => OrderStatus::CONFIRMED,
            ItemStatus::PACKING => OrderStatus::READY_FOR_PICKUP,
            ItemStatus::PICKED_UP => OrderStatus::OUT_FOR_DELIVERY,
            ItemStatus::DEPLOYED => OrderStatus::DELIVERED,
            ItemStatus::RETURNING => OrderStatus::PICKUP_SCHEDULED,
            ItemStatus::IN_INSPECTION => OrderStatus::PENDING_REVIEW,
            default => $booking->order_status, // Keep existing
        };

        if ($booking->order_status !== $orderStatus) {
            $booking->order_status = $orderStatus;
            $booking->save();
        }
    }
}
