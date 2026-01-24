<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $cart = session('cart', []);
        $products = Product::whereIn('id', $cart)->get();
        return view('user.cart.index', compact('products'));
    }

    public function add(Request $request, Product $product)
    {
        $cart = session('cart', []);
        if (!in_array($product->id, $cart)) {
            $cart[] = $product->id;
            session(['cart' => $cart]);
        }
        return redirect()->route('user.cart.index')->with('success', 'Produk ditambahkan ke cart!');
    }

    public function remove(Request $request, Product $product)
    {
        $cart = session('cart', []);
        $cart = array_filter($cart, function($id) use ($product) {
            return $id != $product->id;
        });
        session(['cart' => $cart]);
        return redirect()->route('user.cart.index')->with('success', 'Produk dihapus dari cart!');
    }

    public function checkout(Request $request)
    {
        $cart = session('cart', []);
        if (empty($cart)) {
            return redirect()->route('user.cart.index')->with('error', 'Cart masih kosong.');
        }
        // Simpan cart ke session untuk proses booking massal
        session(['cart_checkout' => $cart]);
        // Kosongkan cart setelah checkout
        session(['cart' => []]);
        // Redirect ke form booking massal
        return redirect()->route('user.booking.cart');
    }
}
