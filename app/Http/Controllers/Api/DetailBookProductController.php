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
                $q->where('participant_name', 'like', "%{$search}%")
                  ->orWhere('participant_email', 'like', "%{$search}%")
                  ->orWhere('participant_telp', 'like', "%{$search}%");
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
            'participant_name' => 'required|string|max:255',
            'participant_email' => 'required|email|max:255',
            'participant_telp' => 'required|string|max:255',
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
            'participant_name' => 'sometimes|string|max:255',
            'participant_email' => 'sometimes|email|max:255',
            'participant_telp' => 'sometimes|string|max:255',
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
