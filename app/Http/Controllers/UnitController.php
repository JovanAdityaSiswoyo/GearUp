<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\Product;
use App\Models\ActivityLog;
use App\Services\QrCodeService;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    protected QrCodeService $qrCodeService;

    public function __construct(QrCodeService $qrCodeService)
    {
        $this->qrCodeService = $qrCodeService;
    }

    public function index(Request $request)
    {
        $query = Unit::with('product');

        // Filter by product
        if ($request->has('product_id') && $request->product_id) {
            $query->where('id_product', $request->product_id);
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Search by serial number
        if ($request->has('search') && $request->search) {
            $query->where('serial_number', 'like', '%' . $request->search . '%');
        }

        $units = $query->latest()->paginate(10);
        $products = Product::orderBy('name')->get();

        return view('admin.units.index', compact('units', 'products'));
    }

    public function create()
    {
        $products = Product::orderBy('name')->get();
        return view('admin.units.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_product' => 'required|exists:products,id',
            'serial_number' => 'required|string|max:255|unique:units,serial_number',
            'status' => 'required|in:available,booked,deployed,returning,in_inspection,maintenance,lost_scrapped',
            'notes' => 'nullable|string',
            'last_maintenance_at' => 'nullable|date',
        ]);

        $unit = Unit::create($validated);

        // Update product stock
        $product = $unit->product;
        $product->increment('stock');

        // Log activity
        ActivityLog::create([
            'log_name' => 'unit',
            'description' => 'Created unit: ' . $unit->serial_number . ' for product: ' . $product->name,
            'subject_type' => Unit::class,
            'subject_id' => $unit->id,
            'causer_type' => get_class(auth()->user()),
            'causer_id' => auth()->id(),
            'event' => 'created',
            'properties' => json_encode([
                'serial_number' => $unit->serial_number,
                'product_name' => $product->name,
            ]),
        ]);

        alert()->success('Success', 'Unit created successfully!');
        
        // Redirect ke detail unit dengan QR code
        return redirect()->route('admin.units.show', ['unit' => $unit->id]);
    }

    /**
     * Show detail unit dengan QR code
     */
    public function show(Unit $unit)
    {
        $unit->load('product', 'logs.actor');
        $qrCode = $this->qrCodeService->generateQrCode($unit);
        $unitData = $this->qrCodeService->getUnitDetailData($unit);

        return view('admin.units.show', compact('unit', 'qrCode', 'unitData'));
    }

    public function edit(Unit $unit)
    {
        $products = Product::orderBy('name')->get();
        return view('admin.units.edit', compact('unit', 'products'));
    }

    public function update(Request $request, Unit $unit)
    {
        $validated = $request->validate([
            'id_product' => 'required|exists:products,id',
            'serial_number' => 'required|string|max:255|unique:units,serial_number,' . $unit->id,
            'status' => 'required|in:available,booked,deployed,returning,in_inspection,maintenance,lost_scrapped',
            'notes' => 'nullable|string',
            'last_maintenance_at' => 'nullable|date',
        ]);

        $oldProductId = $unit->id_product;
        $unit->update($validated);

        // Update stock if product changed
        if ($oldProductId !== $validated['id_product']) {
            // Decrement old product stock
            Product::find($oldProductId)?->decrement('stock');
            // Increment new product stock
            Product::find($validated['id_product'])?->increment('stock');
        }

        // Log activity
        ActivityLog::create([
            'log_name' => 'unit',
            'description' => 'Updated unit: ' . $unit->serial_number,
            'subject_type' => Unit::class,
            'subject_id' => $unit->id,
            'causer_type' => get_class(auth()->user()),
            'causer_id' => auth()->id(),
            'event' => 'updated',
            'properties' => json_encode([
                'serial_number' => $unit->serial_number,
            ]),
        ]);

        alert()->success('Success', 'Unit updated successfully!');
        return redirect()->route('admin.units.index');
    }

    public function destroy(Unit $unit)
    {
        $serialNumber = $unit->serial_number;
        $productId = $unit->id_product;

        $unit->delete();

        // Decrement product stock
        Product::find($productId)?->decrement('stock');

        // Log activity
        ActivityLog::create([
            'log_name' => 'unit',
            'description' => 'Deleted unit: ' . $serialNumber,
            'subject_type' => Unit::class,
            'subject_id' => null,
            'causer_type' => get_class(auth()->user()),
            'causer_id' => auth()->id(),
            'event' => 'deleted',
            'properties' => json_encode([
                'serial_number' => $serialNumber,
            ]),
        ]);

        alert()->success('Deleted', 'Unit deleted successfully!');
        return redirect()->route('admin.units.index');
    }

    /**
     * Bulk create units for a product
     */
    public function bulkCreate(Request $request, Product $product)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1|max:100',
            'prefix' => 'nullable|string|max:50',
            'status' => 'required|in:available,maintenance,in_inspection',
            'notes' => 'nullable|string',
        ]);

        $created = 0;
        $prefix = $validated['prefix'] ?? strtoupper(substr($product->name, 0, 3));

        for ($i = 1; $i <= $validated['quantity']; $i++) {
            $serialNumber = $prefix . '-' . time() . '-' . str_pad($i, 4, '0', STR_PAD_LEFT);
            
            Unit::create([
                'id_product' => $product->id,
                'serial_number' => $serialNumber,
                'status' => $validated['status'],
                'notes' => $validated['notes'],
            ]);

            $created++;
        }

        // Update product stock
        $product->increment('stock', $created);

        // Log activity
        ActivityLog::create([
            'log_name' => 'unit',
            'description' => "Bulk created {$created} units for product: " . $product->name,
            'subject_type' => Product::class,
            'subject_id' => $product->id,
            'causer_type' => get_class(auth()->user()),
            'causer_id' => auth()->id(),
            'event' => 'bulk_created',
            'properties' => json_encode([
                'quantity' => $created,
                'product_name' => $product->name,
            ]),
        ]);

        alert()->success('Success', "{$created} units created successfully!");
        return back();
    }

    /**
     * Bulk print QR code labels
     */
    public function bulkPrint(Request $request)
    {
        $unitIds = $request->query('unit_ids', []);

        if (empty($unitIds)) {
            return abort(400, 'No units selected');
        }

        $units = Unit::with('product')
            ->whereIn('id', $unitIds)
            ->get();

        if ($units->isEmpty()) {
            return abort(404, 'No units found');
        }

        // Generate QR codes for each unit
        $qrCodes = [];
        foreach ($units as $unit) {
            $url = route('scan-unit.show', $unit->id);
            $qrCodes[$unit->id] = $this->qrCodeService->generateQrCode($unit);
        }

        return view('admin.units.print-labels', compact('units', 'qrCodes'));
    }

    /**
     * Get unit data via API (for QR scanner)
     */
    public function getUnitData(Unit $unit)
    {
        return response()->json([
            'id' => $unit->id,
            'serial_number' => $unit->serial_number,
            'status' => $unit->status,
            'product' => $unit->product ? [
                'id' => $unit->product->id,
                'name' => $unit->product->name,
            ] : null,
        ]);
    }
}

