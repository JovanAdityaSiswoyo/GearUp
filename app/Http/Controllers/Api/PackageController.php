<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Package::with('products', 'books');

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where('name_package', 'like', "%{$search}%");
        }

        $packages = $query->paginate(15);
        return response()->json($packages);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name_package' => 'required|string|max:255',
            'publish_start' => 'required|date',
            'publish_end' => 'required|date|after:publish_start',
            'product_ids' => 'nullable|array',
            'product_ids.*' => 'uuid|exists:products,id',
        ]);

        $productIds = $validated['product_ids'] ?? [];
        unset($validated['product_ids']);

        $package = Package::create($validated);
        
        if (!empty($productIds)) {
            $package->products()->attach($productIds);
        }

        return response()->json($package->load('products'), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $package = Package::with('products', 'books')->findOrFail($id);
        return response()->json($package);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $package = Package::findOrFail($id);

        $validated = $request->validate([
            'name_package' => 'sometimes|string|max:255',
            'publish_start' => 'sometimes|date',
            'publish_end' => 'sometimes|date|after:publish_start',
            'product_ids' => 'nullable|array',
            'product_ids.*' => 'uuid|exists:products,id',
        ]);

        $productIds = $validated['product_ids'] ?? null;
        unset($validated['product_ids']);

        $package->update($validated);
        
        if ($productIds !== null) {
            $package->products()->sync($productIds);
        }

        return response()->json($package->load('products'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $package = Package::findOrFail($id);
        $package->products()->detach();
        $package->delete();

        return response()->json(['message' => 'Package deleted successfully'], 200);
    }
}
