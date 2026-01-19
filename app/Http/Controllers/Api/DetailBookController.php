<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DetailBook;
use Illuminate\Http\Request;

class DetailBookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = DetailBook::with('book');

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

        $detailBooks = $query->paginate(15);
        return response()->json($detailBooks);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_book' => 'required|uuid|exists:books,id',
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

        $detailBook = DetailBook::create($validated);

        return response()->json($detailBook->load('book'), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $detailBook = DetailBook::with('book')->findOrFail($id);
        return response()->json($detailBook);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $detailBook = DetailBook::findOrFail($id);

        $validated = $request->validate([
            'id_book' => 'sometimes|uuid|exists:books,id',
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

        $detailBook->update($validated);

        return response()->json($detailBook->load('book'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $detailBook = DetailBook::findOrFail($id);
        $detailBook->delete();

        return response()->json(['message' => 'Detail book deleted successfully'], 200);
    }
}
