<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['category', 'brand', 'images'])->latest()->paginate(12);
        return view('livewire.home.products', compact('products'));
    }

    public function show(Product $product)
    {
        $product->load(['category', 'brand', 'images']);
        return view('user.product.show', compact('product'));
    }

    public function brandProducts(\App\Models\Brand $brand)
    {
        $products = $brand->products()->with(['category', 'brand', 'images'])->latest()->get();
        return view('user.brand-products', compact('brand', 'products'));
    }
}
