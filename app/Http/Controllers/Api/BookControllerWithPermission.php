<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;

/**
 * BookController dengan Spatie Permission Implementation
 * 
 * Contoh implementation dari Spatie Laravel Permission
 * untuk mengelola akses ke endpoints Book
 */
class BookControllerWithPermission extends Controller
{
    /**
     * Display a listing of the resource.
     * 
     * Permission: list-books
     * Roles: admin, officer, user
     */
    public function index(Request $request)
    {
        // Middleware sudah mengecek permission 'list-books'
        
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
        
        return response()->json([
            'success' => true,
            'data' => $books
        ]);
    }

    /**
     * Store a newly created resource in storage.
     * 
     * Permission: create-book
     * Roles: super-admin, admin (via permission)
     */
    public function store(Request $request)
    {
        // Double check permission (middleware juga mengecek)
        if (!$request->user()->hasPermissionTo('create-book')) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to create books'
            ], 403);
        }

        try {
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

            return response()->json([
                'success' => true,
                'message' => 'Book created successfully',
                'data' => $book
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating book: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     * 
     * Permission: read-book
     * Roles: admin, officer, user
     */
    public function show(Book $book)
    {
        // Middleware sudah mengecek permission 'read-book'
        
        $book->load('package', 'user', 'detailBooks', 'detailBookProducts');

        return response()->json([
            'success' => true,
            'data' => $book
        ]);
    }

    /**
     * Update the specified resource in storage.
     * 
     * Permission: update-book
     * Roles: super-admin, admin (via permission)
     */
    public function update(Request $request, Book $book)
    {
        // Check permission
        if (!$request->user()->hasPermissionTo('update-book')) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to update books'
            ], 403);
        }

        try {
            $validated = $request->validate([
                'id_package' => 'nullable|uuid|exists:packages,id',
                'id_user' => 'nullable|uuid|exists:users,id',
                'book_code' => 'nullable|string|max:255|unique:books,book_code,' . $book->id,
                'book_date' => 'nullable|date',
                'checkin_time' => 'nullable|date',
                'checkout_time' => 'nullable|date|after:checkin_time',
                'booker_name' => 'nullable|string|max:255',
                'booker_email' => 'nullable|email|max:255',
                'booker_telp' => 'nullable|string|max:255',
                'status' => 'nullable|string|max:255',
            ]);

            $book->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Book updated successfully',
                'data' => $book
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating book: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     * 
     * Permission: delete-book
     * Roles: super-admin only
     */
    public function destroy(Request $request, Book $book)
    {
        // Check if user has delete-book permission
        if (!$request->user()->hasPermissionTo('delete-book')) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to delete books'
            ], 403);
        }

        // Additional check: Only super-admin can delete
        if (!$request->user()->hasRole('super-admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Only super-admin can delete books'
            ], 403);
        }

        try {
            $bookCode = $book->book_code;
            $book->delete();

            return response()->json([
                'success' => true,
                'message' => 'Book ' . $bookCode . ' deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting book: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk delete books (admin/super-admin only)
     * 
     * Permission: delete-book
     * Roles: super-admin
     */
    public function bulkDelete(Request $request)
    {
        // Check permission
        if (!$request->user()->hasPermissionTo('delete-book')) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to delete books'
            ], 403);
        }

        try {
            $validated = $request->validate([
                'ids' => 'required|array',
                'ids.*' => 'uuid|exists:books,id'
            ]);

            $deleted = Book::whereIn('id', $validated['ids'])->delete();

            return response()->json([
                'success' => true,
                'message' => $deleted . ' books deleted successfully',
                'deleted_count' => $deleted
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting books: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user's role and permissions
     * 
     * Useful for frontend to determine what UI elements to show
     */
    public function getAuthUserPermissions(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'data' => [
                'user_id' => $user->id,
                'roles' => $user->getRoleNames(),
                'permissions' => $user->getPermissionNames(),
                'has_super_admin' => $user->hasRole('super-admin'),
                'can_create_book' => $user->hasPermissionTo('create-book'),
                'can_update_book' => $user->hasPermissionTo('update-book'),
                'can_delete_book' => $user->hasPermissionTo('delete-book'),
                'can_list_books' => $user->hasPermissionTo('list-books'),
                'can_read_book' => $user->hasPermissionTo('read-book'),
            ]
        ]);
    }
}
