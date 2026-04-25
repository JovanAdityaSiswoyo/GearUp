<?php

namespace App\Http\Controllers;

use App\Models\BookProduct;
use App\Models\Book;
use App\Models\BookPackageProduct;
use App\Models\Product;
use App\Models\Payment;
use App\Models\ActivityLog;
use App\Enums\OrderStatus;
use App\Services\ItemStatusTransitionService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Enums\ItemStatus;

class OfficerBookingStatusController extends Controller
{
    private const PENALTY_MAP = [
        'rusak_ringan' => 15,
        'rusak_sedang' => 30,
        'rusak_berat' => 70,
        'hilang' => 100,
    ];

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
            OrderStatus::PENDING => ItemStatus::BOOKED,
            OrderStatus::DIPINJAM => ItemStatus::DEPLOYED,
            OrderStatus::SELESAI => ItemStatus::AVAILABLE,
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
            OrderStatus::PENDING,
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
            OrderStatus::PENDING,
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
            OrderStatus::PENDING,
            'Barang siap diambil user di lokasi',
            'prepare_pickup'
        );
    }

    /**
     * Serahkan BookProduct langsung ke user di lokasi
     */
    public function handoverProduct(Request $request, $id): JsonResponse
    {
        $request->validate([
            'handover_photo' => 'required|image|mimes:jpeg,png,jpg,webp|max:4096',
        ]);

        $photoPath = $request->file('handover_photo')->store('booking-proofs/handover', 'public');

        return $this->updateOrderStatus(
            BookProduct::find($id),
            OrderStatus::DIPINJAM,
            'Barang berhasil diserahkan ke user',
            'handover',
            ['pickup_photo' => $photoPath]
        );
    }

    /**
     * Schedule return for BookProduct
     */
    public function scheduleReturnProduct($id): JsonResponse
    {
        return $this->updateOrderStatus(
            BookProduct::find($id),
            OrderStatus::DIPINJAM,
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
            OrderStatus::SELESAI,
            'Barang pengembalian berhasil diterima',
            'receive_return'
        );
    }

    /**
     * Complete BookProduct order
     */
    public function completeProduct(Request $request, $id): JsonResponse
    {
        $request->validate([
            'return_photo' => 'required|image|mimes:jpeg,png,jpg,webp|max:4096',
        ]);

        $photoPath = $request->file('return_photo')->store('booking-proofs/return', 'public');

        return $this->updateOrderStatus(
            BookProduct::find($id),
            OrderStatus::SELESAI,
            'Order berhasil diselesaikan',
            'complete',
            ['return_photo' => $photoPath]
        );
    }

    /**
     * Detect issue on BookProduct
     */
    public function detectIssueProduct(Request $request, $id): JsonResponse
    {
        $validated = $request->validate([
            'issue_notes' => 'required|string|min:10',
            'issue_condition' => 'required|string|in:rusak_ringan,rusak_sedang,rusak_berat,hilang',
            'issue_photo' => 'required|image|mimes:jpeg,png,jpg,webp|max:4096',
        ]);

        $booking = BookProduct::find($id);
        if (! $booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking tidak ditemukan',
            ], 404);
        }

        $penalty = $this->calculatePenalty($booking, $validated['issue_condition']);
        $photoPath = $request->file('issue_photo')->store('booking-proofs/issue', 'public');

        $response = $this->updateOrderStatus(
            $booking,
            OrderStatus::SELESAI,
            'Masalah berhasil dicatat. Denda otomatis berhasil dibuat.',
            'detect_issue',
            [
                'issue_notes' => $validated['issue_notes'],
                'issue_condition' => $validated['issue_condition'],
                'fine_percentage' => $penalty['percentage'],
                'fine_amount' => $penalty['amount'],
                'issue_photo' => $photoPath,
            ]
        );

        if ($response->getStatusCode() >= 400) {
            return $response;
        }

        $this->createOrUpdatePenaltyPayment($booking, $penalty, $validated['issue_condition']);

        return response()->json([
            'success' => true,
            'message' => sprintf(
                'Masalah berhasil dicatat. Denda %d%% = Rp %s dibuat dan menunggu pembayaran user.',
                $penalty['percentage'],
                number_format($penalty['amount'] / 100, 0, ',', '.')
            ),
        ]);
    }

    /**
     * Cancel BookProduct order
     */
    public function cancelProduct($id): JsonResponse
    {
        $reason = request('reason', '');

        return $this->updateOrderStatus(
            BookProduct::find($id),
            OrderStatus::SELESAI,
            'Order berhasil dibatalkan',
            'cancel',
            ['notes' => $reason]
        );
    }

    // Package routes
    /**
     * Validate Book (Package) order
     */
    public function validatePackage($id): JsonResponse
    {
        return $this->updateOrderStatus(
            Book::find($id),
            OrderStatus::PENDING,
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
            OrderStatus::PENDING,
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
            OrderStatus::PENDING,
            'Barang siap diambil user di lokasi',
            'prepare_pickup'
        );
    }

    /**
     * Serahkan Book (Package) langsung ke user di lokasi
     */
    public function handoverPackage(Request $request, $id): JsonResponse
    {
        $request->validate([
            'handover_photo' => 'required|image|mimes:jpeg,png,jpg,webp|max:4096',
        ]);

        $photoPath = $request->file('handover_photo')->store('booking-proofs/handover', 'public');

        return $this->updateOrderStatus(
            Book::find($id),
            OrderStatus::DIPINJAM,
            'Barang berhasil diserahkan ke user',
            'handover',
            ['pickup_photo' => $photoPath]
        );
    }

    /**
     * Schedule return for Book (Package)
     */
    public function scheduleReturnPackage($id): JsonResponse
    {
        return $this->updateOrderStatus(
            Book::find($id),
            OrderStatus::DIPINJAM,
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
            OrderStatus::SELESAI,
            'Barang pengembalian berhasil diterima',
            'receive_return'
        );
    }

    /**
     * Complete Book (Package) order
     */
    public function completePackage(Request $request, $id): JsonResponse
    {
        $request->validate([
            'return_photo' => 'required|image|mimes:jpeg,png,jpg,webp|max:4096',
        ]);

        $photoPath = $request->file('return_photo')->store('booking-proofs/return', 'public');

        return $this->updateOrderStatus(
            Book::find($id),
            OrderStatus::SELESAI,
            'Order berhasil diselesaikan',
            'complete',
            ['return_photo' => $photoPath]
        );
    }

    /**
     * Detect issue on Book (Package)
     */
    public function detectIssuePackage(Request $request, $id): JsonResponse
    {
        $validated = $request->validate([
            'issue_notes' => 'required|string|min:10',
            'issue_condition' => 'required|string|in:rusak_ringan,rusak_sedang,rusak_berat,hilang',
            'issue_photo' => 'required|image|mimes:jpeg,png,jpg,webp|max:4096',
        ]);

        $booking = Book::find($id);
        if (! $booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking tidak ditemukan',
            ], 404);
        }

        $penalty = $this->calculatePenalty($booking, $validated['issue_condition']);
        $photoPath = $request->file('issue_photo')->store('booking-proofs/issue', 'public');

        $response = $this->updateOrderStatus(
            $booking,
            OrderStatus::SELESAI,
            'Masalah berhasil dicatat. Denda otomatis berhasil dibuat.',
            'detect_issue',
            [
                'issue_notes' => $validated['issue_notes'],
                'issue_condition' => $validated['issue_condition'],
                'fine_percentage' => $penalty['percentage'],
                'fine_amount' => $penalty['amount'],
                'issue_photo' => $photoPath,
            ]
        );

        if ($response->getStatusCode() >= 400) {
            return $response;
        }

        $this->createOrUpdatePenaltyPayment($booking, $penalty, $validated['issue_condition']);

        return response()->json([
            'success' => true,
            'message' => sprintf(
                'Masalah berhasil dicatat. Denda %d%% = Rp %s dibuat dan menunggu pembayaran user.',
                $penalty['percentage'],
                number_format($penalty['amount'] / 100, 0, ',', '.')
            ),
        ]);
    }

    /**
     * Cancel Book (Package) order
     */
    public function cancelPackage($id): JsonResponse
    {
        $reason = request('reason', '');

        return $this->updateOrderStatus(
            Book::find($id),
            OrderStatus::SELESAI,
            'Order berhasil dibatalkan',
            'cancel',
            ['notes' => $reason]
        );
    }

    /**
     * Update order status helper method
     */
    private function updateOrderStatus($booking, $newStatus, $successMessage, string $action, array $extraUpdates = []): JsonResponse
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

        if (
            $newStatus === OrderStatus::DIPINJAM &&
            $booking->order_status !== OrderStatus::DIPINJAM &&
            !$this->hasOfficerApproval($booking)
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Booking harus di-approve officer terlebih dahulu lewat Booking Management sebelum status dipinjam.',
            ], 422);
        }

        DB::beginTransaction();
        try {
            $previousOrderStatus = $booking->order_status;
            $previousItemStatus = $booking->item_status?->value;
            
            // Get the corresponding item status from order status
            $correspondingItemStatus = $this->getItemStatusFromOrderStatus($newStatus);
            
            // Direct assignment (officers can override workflow)
            if ($correspondingItemStatus) {
                $booking->item_status = $correspondingItemStatus;
            }

            if ($newStatus === OrderStatus::DIPINJAM) {
                $booking->delivery_at = now();
            }

            if ($newStatus === OrderStatus::SELESAI) {
                $booking->returned_at = now();
            }

            foreach ($extraUpdates as $field => $value) {
                $booking->{$field} = $value;
            }
            
            $booking->order_status = $newStatus;
            $booking->save();

            if ($previousOrderStatus !== OrderStatus::SELESAI && $newStatus === OrderStatus::SELESAI) {
                $this->restoreStockForBooking($booking);
            }
            
            $this->logBookingActivity(
                $booking,
                $action,
                'Officer mengubah status booking',
                [
                    'previous_order_status' => $previousOrderStatus?->value,
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

    private function restoreStockForBooking($booking): void
    {
        if ($booking instanceof BookProduct) {
            $quantity = max(1, (int) ($booking->amount ?? 1));
            $product = Product::whereKey($booking->id_product)->lockForUpdate()->first();
            if ($product) {
                $product->increment('stock', $quantity);
            }

            return;
        }

        if ($booking instanceof Book) {
            $packageItems = BookPackageProduct::query()
                ->where('id_book', $booking->id)
                ->get(['id_product', 'qty']);

            foreach ($packageItems as $item) {
                $quantity = max(1, (int) ($item->qty ?? 1));
                $product = Product::whereKey($item->id_product)->lockForUpdate()->first();
                if ($product) {
                    $product->increment('stock', $quantity);
                }
            }
        }
    }

    private function hasOfficerApproval($booking): bool
    {
        return ActivityLog::query()
            ->where('log_name', 'booking_status')
            ->where('subject_type', get_class($booking))
            ->where('subject_id', (string) $booking->id)
            ->whereIn('event', ['validate', 'confirm'])
            ->exists();
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

    private function calculatePenalty($booking, string $issueCondition): array
    {
        $percentage = self::PENALTY_MAP[$issueCondition] ?? 0;
        $baseAmount = $this->calculateBaseRentalAmount($booking);
        $penaltyAmount = (int) round($baseAmount * ($percentage / 100));

        return [
            'base_amount' => $baseAmount,
            'percentage' => $percentage,
            'amount' => max(0, $penaltyAmount),
            'condition' => $issueCondition,
        ];
    }

    private function calculateBaseRentalAmount($booking): int
    {
        if ($booking instanceof BookProduct) {
            $computed = (int) round($booking->rental_total * 100);
            if ($computed > 0) {
                return $computed;
            }

            $unitPrice = (float) ($booking->product?->price_per_day ?? $booking->product?->price ?? 0);
            $quantity = max(1, (int) ($booking->amount ?? 1));
            $days = $this->getRentalDays($booking);

            return (int) round($unitPrice * $quantity * $days * 100);
        }

        if ($booking instanceof Book) {
            $dailyPrice = (float) ($booking->package?->price ?? 0);
            $days = $this->getRentalDays($booking);

            return (int) round($dailyPrice * $days * 100);
        }

        return 0;
    }

    private function getRentalDays($booking): int
    {
        if (! $booking->checkin_appointment_start || ! $booking->checkout_appointment_end) {
            return 1;
        }

        $start = $booking->checkin_appointment_start->copy()->startOfDay();
        $end = $booking->checkout_appointment_end->copy()->startOfDay();

        return max(1, $start->diffInDays($end) + 1);
    }

    private function createOrUpdatePenaltyPayment($booking, array $penalty, string $issueCondition): void
    {
        $existingPending = $booking->payments()
            ->where('method', 'penalty')
            ->where('status', 'pending')
            ->latest()
            ->first();

        $payload = [
            'amount' => $penalty['amount'],
            'currency' => 'IDR',
            'status' => 'pending',
            'provider' => 'manual',
            'method' => 'penalty',
            'meta' => [
                'type' => 'damage_penalty',
                'issue_condition' => $issueCondition,
                'fine_percentage' => $penalty['percentage'],
                'base_amount' => $penalty['base_amount'],
                'book_code' => $booking->book_code,
                'booking_type' => $booking instanceof BookProduct ? 'product' : 'package',
            ],
        ];

        if ($existingPending) {
            $existingPending->update($payload);

            return;
        }

        $booking->payments()->create($payload);
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
