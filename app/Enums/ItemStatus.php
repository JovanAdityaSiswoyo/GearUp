<?php

namespace App\Enums;

/**
 * Item Status - Status Barang Fisik (Inventory Status)
 * Melacak kondisi dan lokasi barang di gudang
 */
enum ItemStatus: string
{
    // Barang bersih, lengkap, dan siap di rak gudang
    case AVAILABLE = 'Available';
    
    // Barang sudah dipesan untuk tanggal tertentu (tidak bisa diambil order lain)
    case BOOKED = 'Booked';
    
    // Barang sedang diambil dari rak dan dimasukkan ke tas/wadah
    case PACKING = 'Packing';
    
    // Barang sudah diserahkan ke user di lokasi
    case PICKED_UP = 'Picked-Up';
    
    // Barang sudah di tangan User dan sedang digunakan di lapangan
    case DEPLOYED = 'Deployed';
    
    // Barang sedang dalam proses pengembalian ke lokasi
    case RETURNING = 'Returning';
    
    // Barang sudah di gudang tapi belum boleh disewa karena sedang dicek
    case IN_INSPECTION = 'In-Inspection';
    
    // Barang rusak ringan (perlu dijahit/dicuci/servis)
    case MAINTENANCE = 'Maintenance';
    
    // Barang hilang atau rusak total dan sudah dihapus dari inventaris aktif
    case LOST_SCRAPPED = 'Lost/Scrapped';

    /**
     * Get label untuk display di UI
     */
    public function label(): string
    {
        return match($this) {
            self::AVAILABLE => 'Tersedia',
            self::BOOKED => 'Dipesan',
            self::PACKING => 'Sedang Dikemas',
            self::PICKED_UP => 'Diambil User',
            self::DEPLOYED => 'Sedang Digunakan',
            self::RETURNING => 'Dalam Perjalanan Kembali',
            self::IN_INSPECTION => 'Dalam Pemeriksaan',
            self::MAINTENANCE => 'Perawatan',
            self::LOST_SCRAPPED => 'Hilang/Scrap',
        };
    }

    /**
     * Get role yang bertanggung jawab
     */
    public function responsibleRole(): string
    {
        return match($this) {
            self::AVAILABLE => 'Admin',
            self::BOOKED => 'Officer',
            self::PACKING => 'Officer',
            self::PICKED_UP => 'Officer/User',
            self::DEPLOYED => 'User',
            self::RETURNING => 'Officer/User',
            self::IN_INSPECTION => 'Officer',
            self::MAINTENANCE => 'Officer',
            self::LOST_SCRAPPED => 'Admin',
        };
    }

    /**
     * Get deskripsi status
     */
    public function description(): string
    {
        return match($this) {
            self::AVAILABLE => 'Barang bersih, lengkap, dan siap di rak gudang',
            self::BOOKED => 'Barang sudah dipesan untuk tanggal tertentu',
            self::PACKING => 'Barang sedang diambil dari rak dan dimasukkan ke tas/wadah',
            self::PICKED_UP => 'Barang sudah diserahkan ke user di lokasi',
            self::DEPLOYED => 'Barang sudah di tangan User dan sedang digunakan di lapangan',
            self::RETURNING => 'Barang sedang dalam proses pengembalian ke lokasi',
            self::IN_INSPECTION => 'Barang sudah di gudang tapi belum boleh disewa',
            self::MAINTENANCE => 'Barang rusak ringan',
            self::LOST_SCRAPPED => 'Barang hilang atau rusak total',
        };
    }

    /**
     * Get all cases as array
     */
    public static function all(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
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
