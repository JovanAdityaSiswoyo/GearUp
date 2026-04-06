<?php

namespace App\Services;

use App\Models\Unit;
use App\Models\UnitLog;
use Illuminate\Support\Facades\Log;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\SvgWriter;
use Endroid\QrCode\Encoding\Encoding;

class QrCodeService
{
    /**
     * Generate QR code SVG for a unit
        * Format: /scan-unit/{unit_id} - URL yang akan di-scan oleh officer/admin
     */
    public function generateQrCode(Unit $unit): string
    {
        try {
            $qrUrl = route('scan-unit.show', ['unit' => $unit->id], absolute: true);
            
            // Generate QR code
            $qrCode = QrCode::create($qrUrl)
                ->setSize(300)
                ->setMargin(10)
                ->setEncoding(new Encoding('UTF-8'));

            // Write as SVG
            $writer = new SvgWriter();
            $result = $writer->write($qrCode);

            return $result->getDataUri();
        } catch (\Exception $e) {
            Log::error('Failed to generate QR code for unit', [
                'unit_id' => $unit->id,
                'error' => $e->getMessage(),
            ]);
            return '';
        }
    }

    /**
     * Log a scan action untuk unit
        * Polymorphic relation - bisa Officer atau Admin
     */
    public function logScan(Unit $unit, string $actionType, $actor, ?string $notes = null): bool
    {
        try {
            UnitLog::create([
                'unit_id' => $unit->id,
                'actor_type' => get_class($actor),
                'actor_id' => $actor->id,
                'action_type' => $actionType, // 'packed', 'picked_up', 'delivered', etc.
                'notes' => $notes,
            ]);

            // Update last_scanned_by fields
            $unit->update([
                'last_scanned_by_type' => get_class($actor),
                'last_scanned_by_id' => $actor->id,
            ]);

            Log::info("Unit scanned", [
                'unit_id' => $unit->id,
                'serial_number' => $unit->serial_number,
                'actor_type' => get_class($actor),
                'actor_id' => $actor->id,
                'action_type' => $actionType,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to log unit scan', [
                'unit_id' => $unit->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Get unit data untuk QR scan page
     * Include: product spec, current status, last scan, riwayat scan
     */
    public function getUnitDetailData(Unit $unit): array
    {
        return [
            'unit' => [
                'id' => $unit->id,
                'serial_number' => $unit->serial_number,
                'status' => $unit->status,
                'created_at' => $unit->created_at->format('d M Y H:i'),
                'last_maintenance_at' => $unit->last_maintenance_at?->format('d M Y') ?? 'Never',
                'notes' => $unit->notes,
            ],
            'product' => [
                'id' => $unit->product->id,
                'name' => $unit->product->name,
                'desc' => $unit->product->desc,
                'description' => $unit->product->description ?? '',
                'status' => $unit->product->status,
                'price' => $unit->product->price,
                'price_per_day' => $unit->product->price_per_day ?? 0,
            ],
            'last_scan' => $unit->lastScannedBy ? [
                'actor' => $unit->lastScannedBy->name ?? 'Unknown',
                'type' => class_basename($unit->last_scanned_by_type),
                'at' => $unit->logs()->latest()->first()?->created_at->format('d M Y H:i') ?? 'Never',
            ] : null,
            'scan_history' => $unit->logs()
                ->latest()
                ->limit(10)
                ->get()
                ->map(fn($log) => [
                    'action' => $log->action_type,
                    'actor' => optional($log->actor)->name ?? 'Unknown',
                    'actor_type' => class_basename($log->actor_type),
                    'at' => $log->created_at->format('d M Y H:i'),
                    'notes' => $log->notes,
                ])
                ->toArray(),
        ];
    }
}

