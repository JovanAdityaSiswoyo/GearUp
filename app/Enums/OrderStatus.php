<?php

namespace App\Enums;

/**
 * Order Status - Status Transaksi/Proses
 * Melacak alur kerja dari sisi aplikasi dan koordinasi antar role
 */
enum OrderStatus: string
{
    // ========== FASE PENGAJUAN ==========
    
    // User baru membuat pesanan, menunggu pembayaran
    case DRAFT = 'Draft';
    
    // Pembayaran sudah masuk, Officer perlu mengecek ketersediaan fisik barang
    case AWAITING_VALIDATION = 'Awaiting Validation';
    
    // Order sah. Stok barang dipotong dari sistem secara otomatis
    case CONFIRMED = 'Confirmed';

    // ========== FASE PENGIRIMAN (LOGISTIK) ==========
    
    // Barang sudah dipacking Officer, siap diambil User di lokasi
    case READY_FOR_PICKUP = 'Ready for Pickup';
    
    // Transisi lama delivery kurir (tetap disimpan untuk kompatibilitas data)
    case OUT_FOR_DELIVERY = 'Out for Delivery';
    
    // Barang sampai. Kurir melakukan foto paket untuk memastikan
    case DELIVERED = 'Delivered';

    // ========== FASE PENGEMBALIAN ==========
    
    // Sistem/Officer menjadwalkan pengembalian oleh User ke lokasi
    case PICKUP_SCHEDULED = 'Pickup Scheduled';
    
    // Transisi lama return kurir (tetap disimpan untuk kompatibilitas data)
    case ON_PROCESS_RETURN = 'On Process Return';
    
    // Barang sudah sampai gudang, menunggu Officer melakukan QC
    case PENDING_REVIEW = 'Pending Review';

    // ========== FASE PENYELESAIAN ==========
    
    // Barang kembali lengkap, deposit dikembalikan ke User
    case COMPLETED = 'Completed';
    
    // Ada barang rusak/kurang. Muncul tagihan denda atau penahanan deposit
    case ISSUE_DETECTED = 'Issue Detected';
    
    // Waktu sewa sudah habis tapi barang masih deployed
    case OVERDUE = 'Overdue';
    
    // Order dibatalkan (sebelum pengiriman)
    case CANCELLED = 'Cancelled';

    /**
     * Get label untuk display di UI
     */
    public function label(): string
    {
        return match($this) {
            // Fase Pengajuan
            self::DRAFT => 'Draft',
            self::AWAITING_VALIDATION => 'Menunggu Validasi',
            self::CONFIRMED => 'Terkonfirmasi',
            
            // Fase Pengiriman
            self::READY_FOR_PICKUP => 'Siap Diambil di Lokasi',
            self::OUT_FOR_DELIVERY => 'Dalam Proses Penyerahan',
            self::DELIVERED => 'Terkirim',
            
            // Fase Pengembalian
            self::PICKUP_SCHEDULED => 'Pengembalian Dijadwalkan',
            self::ON_PROCESS_RETURN => 'Pengembalian Dalam Proses',
            self::PENDING_REVIEW => 'Menunggu Review',
            
            // Fase Penyelesaian
            self::COMPLETED => 'Selesai',
            self::ISSUE_DETECTED => 'Ada Masalah',
            self::OVERDUE => 'Terlambat',
            self::CANCELLED => 'Dibatalkan',
        };
    }

    /**
     * Get deskripsi status
     */
    public function description(): string
    {
        return match($this) {
            self::DRAFT => 'User baru membuat pesanan, menunggu pembayaran',
            self::AWAITING_VALIDATION => 'Pembayaran sudah masuk, Officer perlu mengecek ketersediaan',
            self::CONFIRMED => 'Order sah. Stok barang dipotong dari sistem',
            
            self::READY_FOR_PICKUP => 'Barang sudah dipacking, siap diambil User di lokasi',
            self::OUT_FOR_DELIVERY => 'Barang sedang dalam proses penyerahan ke User',
            self::DELIVERED => 'Barang sampai ke tangan User',
            
            self::PICKUP_SCHEDULED => 'Pengembalian barang oleh User sudah dijadwalkan',
            self::ON_PROCESS_RETURN => 'Barang sedang dalam proses pengembalian ke lokasi',
            self::PENDING_REVIEW => 'Barang di gudang, menunggu QC Officer',
            
            self::COMPLETED => 'Barang kembali lengkap, deposit dikembalikan',
            self::ISSUE_DETECTED => 'Ada barang rusak atau kurang',
            self::CANCELLED => 'Order dibatalkan',
        };
    }

    /**
     * Get role yang bertanggung jawab di status ini
     */
    public function responsibleRole(): string
    {
        return match($this) {
            self::DRAFT => 'User',
            self::AWAITING_VALIDATION => 'Officer',
            self::CONFIRMED => 'Officer',
            
            self::READY_FOR_PICKUP => 'Officer/User',
            self::OUT_FOR_DELIVERY => 'Officer',
            self::DELIVERED => 'Officer',
            
            self::PICKUP_SCHEDULED => 'Officer',
            self::ON_PROCESS_RETURN => 'Officer/User',
            self::PENDING_REVIEW => 'Officer',
            
            self::COMPLETED => 'Officer',
            self::ISSUE_DETECTED => 'Officer',
            self::CANCELLED => 'Officer/Admin',
        };
    }

    /**
     * Get fase yang sesuai
     */
    public function phase(): string
    {
        return match($this) {
            self::DRAFT, self::AWAITING_VALIDATION, self::CONFIRMED => 'Fase Pengajuan',
            self::READY_FOR_PICKUP, self::OUT_FOR_DELIVERY, self::DELIVERED => 'Fase Pengiriman',
            self::PICKUP_SCHEDULED, self::ON_PROCESS_RETURN, self::PENDING_REVIEW => 'Fase Pengembalian',
            self::COMPLETED, self::ISSUE_DETECTED, self::CANCELLED => 'Fase Penyelesaian',
        };
    }

    /**
     * Get possible next statuses
     */
    public function nextStatuses(): array
    {
        return match($this) {
            self::DRAFT => [self::AWAITING_VALIDATION, self::CANCELLED],
            self::AWAITING_VALIDATION => [self::CONFIRMED, self::CANCELLED],
            self::CONFIRMED => [self::READY_FOR_PICKUP, self::CANCELLED],
            
            self::READY_FOR_PICKUP => [self::DELIVERED, self::OUT_FOR_DELIVERY],
            self::OUT_FOR_DELIVERY => [self::DELIVERED],
            self::DELIVERED => [self::PICKUP_SCHEDULED],
            
            self::PICKUP_SCHEDULED => [self::PENDING_REVIEW, self::ON_PROCESS_RETURN, self::CANCELLED],
            self::ON_PROCESS_RETURN => [self::PENDING_REVIEW],
            self::PENDING_REVIEW => [self::COMPLETED, self::ISSUE_DETECTED],
            
            self::COMPLETED, self::ISSUE_DETECTED, self::CANCELLED => [],
        };
    }

    /**
     * Check if booking is still active (can be modified)
     */
    public function isActive(): bool
    {
        return !in_array($this, [
            self::COMPLETED,
            self::ISSUE_DETECTED,
            self::CANCELLED,
        ]);
    }

    /**
     * Check if in delivery phase
     */
    public function isInDeliveryPhase(): bool
    {
        return in_array($this, [
            self::READY_FOR_PICKUP,
            self::OUT_FOR_DELIVERY,
            self::DELIVERED,
        ]);
    }

    /**
     * Check if in return phase
     */
    public function isInReturnPhase(): bool
    {
        return in_array($this, [
            self::PICKUP_SCHEDULED,
            self::ON_PROCESS_RETURN,
            self::PENDING_REVIEW,
        ]);
    }

    /**
     * Get all cases as array
     */
    public static function all(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }

    /**
     * Get all cases grouped by phase
     */
    public static function groupedByPhase(): array
    {
        return [
            'Fase Pengajuan' => [
                self::DRAFT->value => self::DRAFT->label(),
                self::AWAITING_VALIDATION->value => self::AWAITING_VALIDATION->label(),
                self::CONFIRMED->value => self::CONFIRMED->label(),
            ],
            'Fase Pengiriman' => [
                self::READY_FOR_PICKUP->value => self::READY_FOR_PICKUP->label(),
                self::OUT_FOR_DELIVERY->value => self::OUT_FOR_DELIVERY->label(),
                self::DELIVERED->value => self::DELIVERED->label(),
            ],
            'Fase Pengembalian' => [
                self::PICKUP_SCHEDULED->value => self::PICKUP_SCHEDULED->label(),
                self::ON_PROCESS_RETURN->value => self::ON_PROCESS_RETURN->label(),
                self::PENDING_REVIEW->value => self::PENDING_REVIEW->label(),
            ],
            'Fase Penyelesaian' => [
                self::COMPLETED->value => self::COMPLETED->label(),
                self::ISSUE_DETECTED->value => self::ISSUE_DETECTED->label(),
                self::CANCELLED->value => self::CANCELLED->label(),
            ],
        ];
    }

    /**
     * Get all cases with labels
     */
    public static function withLabels(): array
    {
        $result = [];
        foreach (self::cases() as $case) {
            $result[$case->value] = $case->label();
        }
        return $result;
    }
}
