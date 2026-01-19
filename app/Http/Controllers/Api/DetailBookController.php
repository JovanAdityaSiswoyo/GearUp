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
                $q->where('participant_name', 'like', "%{$search}%")
                  ->orWhere('participant_email', 'like', "%{$search}%")
                  ->orWhere('participant_telp', 'like', "%{$search}%");
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
            'participant_name' => 'required|string|max:255',
            'participant_email' => 'required|email|max:255',
            'participant_telp' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
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
            'participant_name' => 'sometimes|string|max:255',
            'participant_email' => 'sometimes|email|max:255',
            'participant_telp' => 'sometimes|string|max:255',
            'price' => 'sometimes|numeric|min:0',
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
