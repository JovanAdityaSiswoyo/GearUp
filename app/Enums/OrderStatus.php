<?php

namespace App\Enums;

/**
 * Order Status - Status Transaksi/Proses
 * Alur booking disederhanakan menjadi 3 tahap.
 */
enum OrderStatus: string
{
    case PENDING = 'pending';
    case DIPINJAM = 'dipinjam';
    case SELESAI = 'selesai';

    /**
     * Get label untuk display di UI
     */
    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Pending',
            self::DIPINJAM => 'Dipinjam',
            self::SELESAI => 'Selesai',
        };
    }

    /**
     * Get deskripsi status
     */
    public function description(): string
    {
        return match($this) {
            self::PENDING => 'Pengajuan pinjaman telah dibuat dan menunggu proses lanjutan',
            self::DIPINJAM => 'Barang sedang dipinjam oleh user',
            self::SELESAI => 'Pinjaman telah selesai',
        };
    }

    /**
     * Get role yang bertanggung jawab di status ini
     */
    public function responsibleRole(): string
    {
        return match($this) {
            self::PENDING => 'Officer',
            self::DIPINJAM => 'User',
            self::SELESAI => 'Officer',
        };
    }

    /**
     * Get fase yang sesuai
     */
    public function phase(): string
    {
        return match($this) {
            self::PENDING => 'Fase Pengajuan',
            self::DIPINJAM => 'Fase Pinjam',
            self::SELESAI => 'Fase Penyelesaian',
        };
    }

    /**
     * Get possible next statuses
     */
    public function nextStatuses(): array
    {
        return match($this) {
            self::PENDING => [self::DIPINJAM],
            self::DIPINJAM => [self::SELESAI],
            self::SELESAI => [],
        };
    }

    /**
     * Check if booking is still active (can be modified)
     */
    public function isActive(): bool
    {
        return $this !== self::SELESAI;
    }

    /**
     * Check if in delivery phase
     */
    public function isInDeliveryPhase(): bool
    {
        return $this === self::DIPINJAM;
    }

    /**
     * Check if in return phase
     */
    public function isInReturnPhase(): bool
    {
        return $this === self::DIPINJAM;
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
                self::PENDING->value => self::PENDING->label(),
            ],
            'Fase Pinjam' => [
                self::DIPINJAM->value => self::DIPINJAM->label(),
            ],
            'Fase Penyelesaian' => [
                self::SELESAI->value => self::SELESAI->label(),
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
