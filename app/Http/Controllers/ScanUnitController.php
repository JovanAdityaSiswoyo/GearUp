<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Services\QrCodeService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ScanUnitController extends Controller
{
    protected QrCodeService $qrCodeService;

    public function __construct(QrCodeService $qrCodeService)
    {
        $this->qrCodeService = $qrCodeService;
    }

    /**
     * Show unit detail dengan action buttons berdasarkan guard (officer/courier/admin)
     * Dimulai dari QR scan
     */
    public function show(Unit $unit): View
    {
        $unit->load('product', 'logs');
        $unitData = $this->qrCodeService->getUnitDetailData($unit);

        return view('scan-unit.show', compact('unit', 'unitData'));
    }

    /**
     * Start packing - Officer scan
     */
    public function startPacking(Unit $unit)
    {
        $officerId = auth()->guard('officer')->id();
        
        if (!$officerId) {
            return redirect()->route('home')->with('error', 'Officer not authenticated');
        }

        // Log scan action
        $this->qrCodeService->logScan(
            $unit,
            'packing_started',
            auth()->guard('officer')->user(),
            'Officer mulai packing'
        );

        // Redirect ke daftar packing
        return redirect()->route('officer.packing.index')
            ->with('success', 'Packing started for unit: ' . $unit->serial_number);
    }

    /**
     * Pickup unit - Courier scan  
     */
    public function pickupUnit(Unit $unit)
    {
        $courierId = auth()->guard('courier')->id();

        if (!$courierId) {
            return redirect()->route('home')->with('error', 'Courier not authenticated');
        }

        // Log scan action
        $this->qrCodeService->logScan(
            $unit,
            'picked_up',
            auth()->guard('courier')->user(),
            'Courier pickup unit'
        );

        return back()->with('success', 'Unit picked up: ' . $unit->serial_number);
    }

    /**
     * View history - Admin/Officer/Courier dapat melihat
     */
    public function history(Unit $unit): View
    {
        $unit->load('logs.actor');
        $history = $unit->logs()
            ->latest()
            ->get()
            ->map(fn($log) => [
                'action' => $log->action_type,
                'actor' => optional($log->actor)->name ?? 'Unknown',
                'actor_type' => class_basename($log->actor_type),
                'at' => $log->created_at->format('d M Y H:i:s'),
                'notes' => $log->notes,
            ])
            ->toArray();

        return view('scan-unit.history', compact('unit', 'history'));
    }

    /**
     * Camera-based QR code scanner (for packing workflows)
     */
    public function camera(Request $request)
    {
        return view('scan-unit.camera');
    }
}
