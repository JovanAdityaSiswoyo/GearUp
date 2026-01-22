<?php

namespace App\Http\Controllers;

use App\Models\BookProduct;
use App\Models\User;
use App\Models\Product;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $query = BookProduct::with(['user', 'product'])->latest();
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Search by book code or booker name
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('book_code', 'like', '%' . $request->search . '%')
                  ->orWhere('booker_name', 'like', '%' . $request->search . '%')
                  ->orWhere('booker_email', 'like', '%' . $request->search . '%');
            });
        }
        
        $bookings = $query->paginate(5)->withQueryString();
        return view('admin.bookings.index', compact('bookings'));
    }

    public function create()
    {
        $users = User::all();
        $products = Product::all();
        return view('admin.bookings.create', compact('users', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_user' => 'required|exists:users,id',
            'id_product' => 'required|exists:products,id',
            'status' => 'required|in:pending,confirmed,active,completed,cancelled',
            'checkin_appointment_start' => 'required|date',
            'checkout_appointment_end' => 'required|date|after:checkin_appointment_start',
            'amount' => 'required|integer|min:1',
            'booker_name' => 'required|string|max:255',
            'booker_email' => 'required|email|max:255',
            'booker_telp' => 'required|string|max:20',
        ]);

        // Generate book code
        $validated['book_code'] = 'BK-' . strtoupper(uniqid());

        BookProduct::create($validated);

        alert()->success('Success', 'Booking created successfully!');
        return redirect()->route('admin.bookings.index');
    }

    public function edit(BookProduct $booking)
    {
        $users = User::all();
        $products = Product::all();
        return view('admin.bookings.edit', compact('booking', 'users', 'products'));
    }

    public function update(Request $request, BookProduct $booking)
    {
        $validated = $request->validate([
            'id_user' => 'required|exists:users,id',
            'id_product' => 'required|exists:products,id',
            'status' => 'required|in:pending,confirmed,active,completed,cancelled',
            'checkin_appointment_start' => 'required|date',
            'checkout_appointment_end' => 'required|date|after:checkin_appointment_start',
            'amount' => 'required|integer|min:1',
            'booker_name' => 'required|string|max:255',
            'booker_email' => 'required|email|max:255',
            'booker_telp' => 'required|string|max:20',
        ]);

        $booking->update($validated);

        alert()->success('Success', 'Booking updated successfully!');
        return redirect()->route('admin.bookings.index');
    }

    public function destroy(BookProduct $booking)
    {
        $booking->delete();
        alert()->success('Deleted', 'Booking deleted successfully!');
        return redirect()->route('admin.bookings.index');
    }
}
