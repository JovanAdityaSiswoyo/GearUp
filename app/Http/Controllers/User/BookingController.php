<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\BookProduct;
use App\Models\DetailBookProduct;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    public function create(Product $product)
    {
        // Load relationships
        $product->load(['brand', 'category']);
        
        return view('user.booking.create', compact('product'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_product' => 'required|exists:products,id',
            'amount' => 'required|integer|min:1',
            'booker_name' => 'required|string|max:255',
            'booker_email' => 'required|email|max:255',
            'booker_telp' => 'required|string|max:20',
            
            // Detail book product fields
            'full_name' => 'required|string|max:255',
            'instagram_handle' => 'nullable|string|max:255',
            'other_socials' => 'nullable|string|max:255',
            'phone_number' => 'required|string|max:20',
            'emergency_phone_number' => 'required|string|max:20',
            'shipping_method' => 'required|in:pickup,delivery',
            'renter_address' => 'required|string',
            'shipping_date' => 'required|date|after_or_equal:today',
            'rental_start_at' => 'required|date|after_or_equal:shipping_date',
            'rental_end_at' => 'required|date|after:rental_start_at',
            'identity_document' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Upload identity document
        $identityPath = $request->file('identity_document')->store('identity_documents', 'public');

        // Create book product
        $bookProduct = BookProduct::create([
            'book_code' => 'BK-' . strtoupper(Str::random(8)),
            'id_user' => auth()->id(),
            'id_product' => $validated['id_product'],
            'status' => 'pending',
            'checkin_appointment_start' => $validated['rental_start_at'],
            'checkout_appointment_end' => $validated['rental_end_at'],
            'amount' => $validated['amount'],
            'booker_name' => $validated['booker_name'],
            'booker_email' => $validated['booker_email'],
            'booker_telp' => $validated['booker_telp'],
        ]);

        // Create detail book product
        DetailBookProduct::create([
            'id_book_product' => $bookProduct->id,
            'full_name' => $validated['full_name'],
            'instagram_handle' => $validated['instagram_handle'],
            'other_socials' => $validated['other_socials'],
            'phone_number' => $validated['phone_number'],
            'emergency_phone_number' => $validated['emergency_phone_number'],
            'shipping_method' => $validated['shipping_method'],
            'renter_address' => $validated['renter_address'],
            'shipping_date' => $validated['shipping_date'],
            'rental_start_at' => $validated['rental_start_at'],
            'rental_end_at' => $validated['rental_end_at'],
            'identity_document_path' => $identityPath,
        ]);

        return redirect()->route('user.my-booking')->with('success', 'Booking berhasil dibuat! Kami akan segera menghubungi Anda untuk konfirmasi booking.');
    }

    public function myBooking()
    {
        $bookings = BookProduct::with(['product', 'detailBookProduct'])
            ->where('id_user', auth()->id())
            ->latest()
            ->get();
        
        return view('user.booking.my-booking', compact('bookings'));
    }
}
