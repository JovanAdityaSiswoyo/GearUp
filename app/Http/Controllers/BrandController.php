<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $brands = Brand::latest()->paginate(10);
        return view('admin.brands.index', compact('brands'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.brands.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:brands,name',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('brands', 'public');
        }

        $brand = Brand::create($validated);

        // Log activity
        ActivityLog::create([
            'log_name' => 'brand',
            'description' => 'Created brand: ' . $brand->name,
            'subject_type' => Brand::class,
            'subject_id' => $brand->id,
            'causer_type' => get_class(auth()->user()),
            'causer_id' => auth()->id(),
            'event' => 'created',
            'properties' => json_encode(['brand_name' => $brand->name]),
        ]);

        alert()->success('Success', 'Brand created successfully!');
        return redirect()->route('admin.brands.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Brand $brand)
    {
        return view('admin.brands.show', compact('brand'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Brand $brand)
    {
        return view('admin.brands.edit', compact('brand'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Brand $brand)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:brands,name,' . $brand->id,
            'description' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            if ($brand->logo) {
                Storage::disk('public')->delete($brand->logo);
            }
            $validated['logo'] = $request->file('logo')->store('brands', 'public');
        }

        $brand->update($validated);

        // Log activity
        ActivityLog::create([
            'log_name' => 'brand',
            'description' => 'Updated brand: ' . $brand->name,
            'subject_type' => Brand::class,
            'subject_id' => $brand->id,
            'causer_type' => get_class(auth()->user()),
            'causer_id' => auth()->id(),
            'event' => 'updated',
            'properties' => json_encode(['brand_name' => $brand->name]),
        ]);

        alert()->success('Success', 'Brand updated successfully!');
        return redirect()->route('admin.brands.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Brand $brand)
    {
        $brandName = $brand->name;
        
        if ($brand->logo) {
            Storage::disk('public')->delete($brand->logo);
        }

        $brand->delete();

        // Log activity
        ActivityLog::create([
            'log_name' => 'brand',
            'description' => 'Deleted brand: ' . $brandName,
            'subject_type' => Brand::class,
            'subject_id' => null,
            'causer_type' => get_class(auth()->user()),
            'causer_id' => auth()->id(),
            'event' => 'deleted',
            'properties' => json_encode(['brand_name' => $brandName]),
        ]);

        alert()->success('Deleted', 'Brand deleted successfully!');
        return redirect()->route('admin.brands.index');
    }
}

