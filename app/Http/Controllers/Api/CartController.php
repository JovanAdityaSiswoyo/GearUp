<?php

namespace App\Http\Controllers\Api;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\BookProduct;
use App\Models\Cart;
use App\Models\DetailBookProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CartController extends Controller
{
    /**
     * Get authenticated user's cart.
     */
    public function index(Request $request)
    {
        $carts = Cart::with('product')
            ->where('id_user', $request->user()->id)
            ->latest()
            ->get();

        return response()->json([
            'message' => 'Cart retrieved successfully',
            'data' => $carts,
        ]);
    }

    /**
     * Add or update item in cart.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_product' => 'required|uuid|exists:products,id',
            'quantity' => 'nullable|integer|min:1',
        ]);

        $quantity = $validated['quantity'] ?? 1;

        $cart = Cart::firstOrNew([
            'id_user' => $request->user()->id,
            'id_product' => $validated['id_product'],
        ]);

        $cart->quantity = $cart->exists ? ($cart->quantity + $quantity) : $quantity;
        $cart->save();

        return response()->json([
            'message' => 'Product added to cart successfully',
            'data' => $cart->load('product'),
        ], 201);
    }

    /**
     * Update item quantity in cart.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = Cart::where('id_user', $request->user()->id)->findOrFail($id);
        $cart->update([
            'quantity' => $validated['quantity'],
        ]);

        return response()->json([
            'message' => 'Cart item updated successfully',
            'data' => $cart->load('product'),
        ]);
    }

    /**
     * Remove one item from cart.
     */
    public function destroy(Request $request, string $id)
    {
        $cart = Cart::where('id_user', $request->user()->id)->findOrFail($id);
        $cart->delete();

        return response()->json([
            'message' => 'Cart item removed successfully',
        ]);
    }

    /**
     * Clear authenticated user's cart.
     */
    public function clear(Request $request)
    {
        Cart::where('id_user', $request->user()->id)->delete();

        return response()->json([
            'message' => 'Cart cleared successfully',
        ]);
    }

    /**
     * Checkout cart and create booking records.
     */
    public function checkout(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated',
            ], 401);
        }

        $validated = $request->validate([
            'booker_name' => 'required|string|max:255',
            'booker_email' => 'required|email|max:255',
            'booker_telp' => 'required|string|max:20',
            'full_name' => 'required|string|max:255',
            'instagram_handle' => 'nullable|string|max:255',
            'other_socials' => 'nullable|string|max:255',
            'phone_number' => 'required|string|max:20',
            'emergency_phone_number' => 'required|string|max:20',
            'renter_address' => 'required|string',
            'rental_start_at' => 'required|date|after_or_equal:today',
            'rental_end_at' => 'required|date|after:rental_start_at',
            'identity_document_path' => 'required|string|max:255',
        ]);

        $carts = Cart::with('product')->where('id_user', $user->id)->get();

        if ($carts->isEmpty()) {
            return response()->json([
                'message' => 'Cart masih kosong',
            ], 422);
        }

        $bookings = DB::transaction(function () use ($carts, $validated, $user) {
            $createdBookings = [];

            foreach ($carts as $cart) {
                $booking = BookProduct::create([
                    'book_code' => 'BK-' . strtoupper(Str::random(8)),
                    'id_user' => $user->id,
                    'id_product' => $cart->id_product,
                    'status' => 'pending',
                    'order_status' => OrderStatus::PENDING,
                    'checkin_appointment_start' => $validated['rental_start_at'],
                    'checkout_appointment_end' => $validated['rental_end_at'],
                    'amount' => $cart->quantity,
                    'booker_name' => $validated['booker_name'],
                    'booker_email' => $validated['booker_email'],
                    'booker_telp' => $validated['booker_telp'],
                ]);

                DetailBookProduct::create([
                    'id_book_product' => $booking->id,
                    'full_name' => $validated['full_name'],
                    'instagram_handle' => $validated['instagram_handle'] ?? null,
                    'other_socials' => $validated['other_socials'] ?? null,
                    'phone_number' => $validated['phone_number'],
                    'emergency_phone_number' => $validated['emergency_phone_number'],
                    'renter_address' => $validated['renter_address'],
                    'rental_start_at' => $validated['rental_start_at'],
                    'rental_end_at' => $validated['rental_end_at'],
                    'identity_document_path' => $validated['identity_document_path'],
                ]);

                $createdBookings[] = $booking->load('product', 'detailBookProduct');
            }

            Cart::where('id_user', $user->id)->delete();

            return $createdBookings;
        });

        return response()->json([
            'message' => 'Checkout cart berhasil',
            'data' => $bookings,
        ], 201);
    }
}
