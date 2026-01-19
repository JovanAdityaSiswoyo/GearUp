<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BookProduct;
use Illuminate\Http\Request;

class BookProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = BookProduct::with('user', 'product', 'detailBookProducts');

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('book_code', 'like', "%{$search}%")
                  ->orWhere('booker_name', 'like', "%{$search}%")
                  ->orWhere('booker_email', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%");
            });
        }

        $bookProducts = $query->paginate(15);
        return response()->json($bookProducts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_user' => 'required|uuid|exists:users,id',
            'id_product' => 'required|uuid|exists:products,id',
            'book_code' => 'required|string|max:255|unique:book_products,book_code',
            'book_date' => 'required|date',
            'checkin_time' => 'required|date',
            'checkout_time' => 'required|date|after:checkin_time',
            'booker_name' => 'required|string|max:255',
            'booker_email' => 'required|email|max:255',
            'booker_telp' => 'required|string|max:255',
            'status' => 'required|string|max:255',
        ]);

        $bookProduct = BookProduct::create($validated);

        return response()->json($bookProduct->load('user', 'product'), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $bookProduct = BookProduct::with('user', 'product', 'detailBookProducts')->findOrFail($id);
        return response()->json($bookProduct);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $bookProduct = BookProduct::findOrFail($id);

        $validated = $request->validate([
            'id_user' => 'sometimes|uuid|exists:users,id',
            'id_product' => 'sometimes|uuid|exists:products,id',
            'book_code' => 'sometimes|string|max:255|unique:book_products,book_code,' . $id,
            'book_date' => 'sometimes|date',
            'checkin_time' => 'sometimes|date',
            'checkout_time' => 'sometimes|date|after:checkin_time',
            'booker_name' => 'sometimes|string|max:255',
            'booker_email' => 'sometimes|email|max:255',
            'booker_telp' => 'sometimes|string|max:255',
            'status' => 'sometimes|string|max:255',
        ]);

        $bookProduct->update($validated);

        return response()->json($bookProduct->load('user', 'product'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $bookProduct = BookProduct::findOrFail($id);
        $bookProduct->delete();

        return response()->json(['message' => 'Book product deleted successfully'], 200);
    }
}
