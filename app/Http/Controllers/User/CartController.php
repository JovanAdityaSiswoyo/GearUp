<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $cart = $this->normalizeCart(session('cart', []));
        session(['cart' => $cart, 'cart_format' => 'v2']);
        $cartProductIds = array_keys($cart);
        $products = Product::whereIn('id', $cartProductIds)->get();
        return view('user.cart.index', compact('products', 'cart'));
    }

    public function add(Request $request, Product $product)
    {
        $validated = $request->validate([
            'quantity' => 'nullable|integer|min:1',
        ]);

        $requestedQty = (int) ($validated['quantity'] ?? 1);
        $cart = $this->normalizeCart(session('cart', []));

        $existingQty = (int) ($cart[$product->id] ?? 0);
        $newQty = $existingQty + $requestedQty;
        $maxQty = max(1, (int) $product->stock);
        $cart[$product->id] = min($newQty, $maxQty);

        session(['cart' => $cart, 'cart_format' => 'v2']);

        return redirect()->route('user.cart.index')->with('success', 'Produk ditambahkan ke cart!');
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = $this->normalizeCart(session('cart', []));
        if (!array_key_exists($product->id, $cart)) {
            return redirect()->route('user.cart.index')->with('error', 'Produk tidak ditemukan di cart.');
        }

        $qty = min((int) $validated['quantity'], max(1, (int) $product->stock));
        $cart[$product->id] = $qty;
        session(['cart' => $cart, 'cart_format' => 'v2']);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Jumlah unit di cart berhasil diperbarui.',
                'data' => [
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'unit_price' => (float) $product->price_per_day,
                    'subtotal' => (float) $product->price_per_day * $qty,
                ],
            ]);
        }

        return redirect()->route('user.cart.index')->with('success', 'Jumlah unit di cart berhasil diperbarui.');
    }

    public function remove(Request $request, Product $product)
    {
        $cart = $this->normalizeCart(session('cart', []));
        unset($cart[$product->id]);
        session(['cart' => $cart, 'cart_format' => 'v2']);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Produk dihapus dari cart!',
                'data' => [
                    'product_id' => $product->id,
                    'total_products' => count($cart),
                    'total_units' => array_sum($cart),
                ],
            ]);
        }

        return redirect()->route('user.cart.index')->with('success', 'Produk dihapus dari cart!');
    }

    public function checkout(Request $request)
    {
        $cart = $this->normalizeCart(session('cart', []));
        if (empty($cart)) {
            return redirect()->route('user.cart.index')->with('error', 'Cart masih kosong.');
        }
        // Simpan cart ke session untuk proses booking massal
        session(['cart_checkout' => $cart]);
        // Kosongkan cart setelah checkout
        session(['cart' => [], 'cart_format' => 'v2']);
        // Redirect ke form booking massal
        return redirect()->route('user.booking.cart');
    }

    private function normalizeCart(array $cart): array
    {
        if (empty($cart)) {
            return [];
        }

        $cartFormat = session('cart_format');
        if ($cartFormat === 'v2') {
            return collect($cart)->mapWithKeys(function ($qty, $productId) {
                return [(string) $productId => max(1, (int) $qty)];
            })->all();
        }

        $values = array_values($cart);
        $isSequentialList = array_keys($cart) === range(0, count($cart) - 1);
        if ($isSequentialList) {
            return $this->normalizeLegacyCartList($values);
        }

        // Legacy carts can have non-sequential numeric keys after old array_filter operations.
        // In pre-v2 format, values are product IDs (uuid/int), so treat as legacy list.
        if (!isset($cartFormat)) {
            return $this->normalizeLegacyCartList($values);
        }

        return collect($cart)->mapWithKeys(function ($qty, $productId) {
            return [(string) $productId => max(1, (int) $qty)];
        })->all();
    }

    private function normalizeLegacyCartList(array $productIds): array
    {
        $normalized = [];
        foreach ($productIds as $productId) {
            $productId = (string) $productId;
            if ($productId !== '') {
                $normalized[$productId] = ((int) ($normalized[$productId] ?? 0)) + 1;
            }
        }

        return $normalized;
    }
}
