<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->latest()->paginate(5);
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        return view('admin.products.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'id_category' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'price_per_day' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Ensure product is tied to the logged-in admin
        $validated['id_admins'] = auth('admin')->id() ?? auth()->id();
        // Backfill legacy columns
        $validated['desc'] = $validated['description'] ?? '';
        $validated['status'] = 'active';
        // Ensure legacy price column is set
        $validated['price'] = $validated['price'] ?? $validated['price_per_day'];

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $product = Product::create($validated);

        // Store multiple images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('product-gallery', 'public');
                $product->images()->create([
                    'image' => $path,
                    'order' => $index,
                ]);
            }
        }

        // Log activity
        ActivityLog::create([
            'log_name' => 'product',
            'description' => 'Created product: ' . $product->name,
            'subject_type' => Product::class,
            'subject_id' => $product->id,
            'causer_type' => get_class(auth()->user()),
            'causer_id' => auth()->id(),
            'event' => 'created',
            'properties' => json_encode(['product_name' => $product->name]),
        ]);

        alert()->success('Success', 'Product created successfully!');
        return redirect()->route('admin.products.index');
    }

    public function edit(Product $product)
    {
        return view('admin.products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'id_category' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'price_per_day' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {
            // Delete old image
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        // Backfill legacy columns
        $validated['desc'] = $validated['description'] ?? $product->desc;
        $validated['status'] = $product->status ?? 'active';
        $validated['price'] = $validated['price'] ?? $validated['price_per_day'];

        $product->update($validated);

        // Store multiple images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('product-gallery', 'public');
                $product->images()->create([
                    'image' => $path,
                    'order' => $index + ($product->images()->count() ?? 0),
                ]);
            }
        }

        // Log activity
        ActivityLog::create([
            'log_name' => 'product',
            'description' => 'Updated product: ' . $product->name,
            'subject_type' => Product::class,
            'subject_id' => $product->id,
            'causer_type' => get_class(auth()->user()),
            'causer_id' => auth()->id(),
            'event' => 'updated',
            'properties' => json_encode(['product_name' => $product->name]),
        ]);

        alert()->success('Success', 'Product updated successfully!');
        return redirect()->route('admin.products.index');
    }

    public function destroy(Product $product)
    {
        $productName = $product->name;
        
        // Delete main image
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        // Delete gallery images
        foreach ($product->images as $image) {
            Storage::disk('public')->delete($image->image);
        }

        $product->delete();

        // Log activity
        ActivityLog::create([
            'log_name' => 'product',
            'description' => 'Deleted product: ' . $productName,
            'subject_type' => Product::class,
            'subject_id' => null,
            'causer_type' => get_class(auth()->user()),
            'causer_id' => auth()->id(),
            'event' => 'deleted',
            'properties' => json_encode(['product_name' => $productName]),
        ]);

        alert()->success('Deleted', 'Product deleted successfully!');
        return redirect()->route('admin.products.index');
    }

    public function addStock(Request $request, Product $product)
    {
        $data = $request->validate([
            'amount' => 'required|integer|min:1',
        ]);

        $product->increment('stock', $data['amount']);

        alert()->success('Success', 'Stock updated');
        return back();
    }
}
