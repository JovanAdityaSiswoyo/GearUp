<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DetailBookProduct;
use Illuminate\Http\Request;

class DetailBookProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = DetailBookProduct::with('bookProduct');

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                                $q->where('full_name', 'like', "%{$search}%")
                                    ->orWhere('phone_number', 'like', "%{$search}%")
                                    ->orWhere('emergency_phone_number', 'like', "%{$search}%")
                                    ->orWhere('instagram_handle', 'like', "%{$search}%")
                                    ->orWhere('other_socials', 'like', "%{$search}%");
            });
        }

        $detailBookProducts = $query->paginate(15);
        return response()->json($detailBookProducts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_book_product' => 'required|uuid|exists:book_products,id',
            'full_name' => 'required|string|max:255',
            'instagram_handle' => 'nullable|string|max:255',
            'other_socials' => 'nullable|string|max:255',
            'phone_number' => 'required|string|max:50',
            'emergency_phone_number' => 'required|string|max:50',
            'shipping_method' => 'required|string|in:JNE,GRABSEND,GOSEND,COD,PAXEL',
            'renter_address' => 'required|string',
            'shipping_date' => 'required|date',
            'rental_start_at' => 'required|date',
            'rental_end_at' => 'required|date|after_or_equal:rental_start_at',
            'identity_document_path' => 'required|string|max:255',
        ]);

        $detailBookProduct = DetailBookProduct::create($validated);

        return response()->json($detailBookProduct->load('bookProduct'), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $detailBookProduct = DetailBookProduct::with('bookProduct')->findOrFail($id);
        return response()->json($detailBookProduct);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $detailBookProduct = DetailBookProduct::findOrFail($id);

        $validated = $request->validate([
            'id_book_product' => 'sometimes|uuid|exists:book_products,id',
            'full_name' => 'sometimes|string|max:255',
            'instagram_handle' => 'sometimes|nullable|string|max:255',
            'other_socials' => 'sometimes|nullable|string|max:255',
            'phone_number' => 'sometimes|string|max:50',
            'emergency_phone_number' => 'sometimes|string|max:50',
            'shipping_method' => 'sometimes|string|in:JNE,GRABSEND,GOSEND,COD,PAXEL',
            'renter_address' => 'sometimes|string',
            'shipping_date' => 'sometimes|date',
            'rental_start_at' => 'sometimes|date',
            'rental_end_at' => 'sometimes|date|after_or_equal:rental_start_at',
            'identity_document_path' => 'sometimes|string|max:255',
        ]);

        $detailBookProduct->update($validated);

        return response()->json($detailBookProduct->load('bookProduct'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $detailBookProduct = DetailBookProduct::findOrFail($id);
        $detailBookProduct->delete();

        return response()->json(['message' => 'Detail book product deleted successfully'], 200);
    }
}
