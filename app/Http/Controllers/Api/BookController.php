<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Book::with('package', 'user', 'detailBooks', 'detailBookProducts');

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('book_code', 'like', "%{$search}%")
                  ->orWhere('booker_name', 'like', "%{$search}%")
                  ->orWhere('booker_email', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%");
            });
        }

        $books = $query->paginate(15);
        return response()->json($books);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_package' => 'required|uuid|exists:packages,id',
            'id_user' => 'required|uuid|exists:users,id',
            'book_code' => 'required|string|max:255|unique:books,book_code',
            'book_date' => 'required|date',
            'checkin_time' => 'required|date',
            'checkout_time' => 'required|date|after:checkin_time',
            'booker_name' => 'required|string|max:255',
            'booker_email' => 'required|email|max:255',
            'booker_telp' => 'required|string|max:255',
            'status' => 'required|string|max:255',
        ]);

        $book = Book::create($validated);

        return response()->json($book->load('package', 'user'), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $book = Book::with('package', 'user', 'detailBooks', 'detailBookProducts')->findOrFail($id);
        return response()->json($book);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $book = Book::findOrFail($id);

        $validated = $request->validate([
            'id_package' => 'sometimes|uuid|exists:packages,id',
            'id_user' => 'sometimes|uuid|exists:users,id',
            'book_code' => 'sometimes|string|max:255|unique:books,book_code,' . $id,
            'book_date' => 'sometimes|date',
            'checkin_time' => 'sometimes|date',
            'checkout_time' => 'sometimes|date|after:checkin_time',
            'booker_name' => 'sometimes|string|max:255',
            'booker_email' => 'sometimes|email|max:255',
            'booker_telp' => 'sometimes|string|max:255',
            'status' => 'sometimes|string|max:255',
        ]);

        $book->update($validated);

        return response()->json($book->load('package', 'user'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $book = Book::findOrFail($id);
        $book->delete();

        return response()->json(['message' => 'Book deleted successfully'], 200);
    }
}
