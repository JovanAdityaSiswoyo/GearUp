<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\Product;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PackageController extends Controller
{
    public function index()
    {
        $packages = Package::with('products')->latest()->paginate(3);
        return view('admin.packages.index', compact('packages'));
    }

    public function create()
    {
        return view('admin.packages.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_publish' => 'required|date_format:Y-m-d\TH:i',
            'end_publish' => 'required|date_format:Y-m-d\TH:i|after:start_publish',
            'price' => 'required|numeric|min:0',
            'upsell' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'products' => 'required|array|min:1',
            'products.*' => 'exists:products,id',
        ]);

        // Calculate duration from start and end dates
        $startDate = \Carbon\Carbon::createFromFormat('Y-m-d\TH:i', $validated['start_publish']);
        $endDate = \Carbon\Carbon::createFromFormat('Y-m-d\TH:i', $validated['end_publish']);
        $durationDays = $startDate->diffInDays($endDate) + 1;

        $upsell = $validated['upsell'] ?? 0;
        $finalPrice = $validated['price'] + $upsell;

        $packageData = [
            'name_package' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'duration_days' => $durationDays,
            'price' => $finalPrice,
            'upsell' => $upsell,
            'start_publish' => $startDate,
            'end_publish' => $endDate,
        ];

        if ($request->hasFile('image')) {
            $packageData['image'] = $request->file('image')->store('packages', 'public');
        }

        $package = Package::create($packageData);

        // Store multiple images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('package-gallery', 'public');
                $package->images()->create([
                    'image' => $path,
                    'order' => $index,
                ]);
            }
        }

        // Sync products to package
        $package->products()->sync($validated['products']);

        // Log activity
        ActivityLog::create([
            'log_name' => 'package',
            'description' => 'Created package: ' . $package->name_package,
            'subject_type' => Package::class,
            'subject_id' => $package->id,
            'causer_type' => get_class(auth()->user()),
            'causer_id' => auth()->id(),
            'event' => 'created',
            'properties' => json_encode(['package_name' => $package->name_package]),
        ]);

        alert()->success('Success', 'Package created successfully!');
        return redirect()->route('admin.packages.index');
    }

    public function edit(Package $package)
    {
        $package->load('products');
        return view('admin.packages.edit', compact('package'));
    }

    public function update(Request $request, Package $package)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_publish' => 'required|date_format:Y-m-d\TH:i',
            'end_publish' => 'required|date_format:Y-m-d\TH:i|after:start_publish',
            'price' => 'required|numeric|min:0',
            'upsell' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'products' => 'required|array|min:1',
            'products.*' => 'exists:products,id',
        ]);

        // Calculate duration from start and end dates
        $startDate = \Carbon\Carbon::createFromFormat('Y-m-d\TH:i', $validated['start_publish']);
        $endDate = \Carbon\Carbon::createFromFormat('Y-m-d\TH:i', $validated['end_publish']);
        $durationDays = $startDate->diffInDays($endDate) + 1;

        $upsell = $validated['upsell'] ?? ($package->upsell ?? 0);
        $finalPrice = $validated['price'] + $upsell;

        $packageData = [
            'name_package' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'duration_days' => $durationDays,
            'price' => $finalPrice,
            'upsell' => $upsell,
            'start_publish' => $startDate,
            'end_publish' => $endDate,
        ];

        if ($request->hasFile('image')) {
            // Delete old image
            if ($package->image) {
                Storage::disk('public')->delete($package->image);
            }
            $packageData['image'] = $request->file('image')->store('packages', 'public');
        }

        $package->update($packageData);

        // Store multiple images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('package-gallery', 'public');
                $package->images()->create([
                    'image' => $path,
                    'order' => $index + ($package->images()->count() ?? 0),
                ]);
            }
        }

        // Sync products to package
        $package->products()->sync($validated['products']);

        // Log activity
        ActivityLog::create([
            'log_name' => 'package',
            'description' => 'Updated package: ' . $package->name_package,
            'subject_type' => Package::class,
            'subject_id' => $package->id,
            'causer_type' => get_class(auth()->user()),
            'causer_id' => auth()->id(),
            'event' => 'updated',
            'properties' => json_encode(['package_name' => $package->name_package]),
        ]);

        alert()->success('Success', 'Package updated successfully!');
        return redirect()->route('admin.packages.index');
    }

    public function destroy(Package $package)
    {
        $packageName = $package->name_package;
        
        // Delete main image
        if ($package->image) {
            Storage::disk('public')->delete($package->image);
        }

        // Delete gallery images
        foreach ($package->images as $image) {
            Storage::disk('public')->delete($image->image);
        }
        
        $package->delete();
        
        // Log activity
        ActivityLog::create([
            'log_name' => 'package',
            'description' => 'Deleted package: ' . $packageName,
            'subject_type' => Package::class,
            'subject_id' => null,
            'causer_type' => get_class(auth()->user()),
            'causer_id' => auth()->id(),
            'event' => 'deleted',
            'properties' => json_encode(['package_name' => $packageName]),
        ]);
        
        alert()->success('Deleted', 'Package deleted successfully!');
        return redirect()->route('admin.packages.index');
    }
}
