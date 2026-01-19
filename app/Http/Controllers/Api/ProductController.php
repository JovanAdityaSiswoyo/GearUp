<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Product::with('admin', 'category', 'packages');

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('desc', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%");
            });
        }

        $products = $query->paginate(15);
        return response()->json($products);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_admins' => 'required|uuid|exists:admins,id',
            'id_category' => 'required|uuid|exists:categories,id',
            'name' => 'required|string|max:255',
            'desc' => 'nullable|string',
            'status' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
        ]);

        $product = Product::create($validated);

        return response()->json($product->load('admin', 'category'), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::with('admin', 'category', 'packages')->findOrFail($id);
        return response()->json($product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'id_admins' => 'sometimes|uuid|exists:admins,id',
            'id_category' => 'sometimes|uuid|exists:categories,id',
            'name' => 'sometimes|string|max:255',
            'desc' => 'nullable|string',
            'status' => 'sometimes|string|max:255',
            'price' => 'sometimes|numeric|min:0',
        ]);

        $product->update($validated);

        return response()->json($product->load('admin', 'category'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return response()->json(['message' => 'Product deleted successfully'], 200);
    }
}
