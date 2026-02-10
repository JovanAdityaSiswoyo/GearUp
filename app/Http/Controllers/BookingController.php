<?php

namespace App\Http\Controllers;

use App\Models\BookProduct;
use App\Models\Book;
use App\Models\User;
use App\Models\Product;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $type = $request->input('type');

        // Query BookProduct (individual products)
        $productQuery = BookProduct::with(['user', 'product.category'])
            ->when($search, function($q) use ($search) {
                $q->where(function($query) use ($search) {
                    $query->where('book_code', 'like', "%{$search}%")
                        ->orWhere('booker_name', 'like', "%{$search}%")
                        ->orWhere('booker_email', 'like', "%{$search}%")
                        ->orWhere('booker_telp', 'like', "%{$search}%")
                        ->orWhereHas('user', function($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%")
                              ->orWhere('email', 'like', "%{$search}%");
                        });
                });
            })
            ->when($status, function($q) use ($status) {
                $q->where('status', $status);
            });

        // Query Book (packages)
        $packageQuery = Book::with(['user', 'package'])
            ->when($search, function($q) use ($search) {
                $q->where(function($query) use ($search) {
                    $query->where('book_code', 'like', "%{$search}%")
                        ->orWhere('booker_name', 'like', "%{$search}%")
                        ->orWhere('booker_email', 'like', "%{$search}%")
                        ->orWhere('booker_telp', 'like', "%{$search}%")
                        ->orWhereHas('user', function($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%")
                              ->orWhere('email', 'like', "%{$search}%");
                        });
                });
            })
            ->when($status, function($q) use ($status) {
                $q->where('status', $status);
            });

        // Filter by type
        $products = ($type === 'package') ? collect() : $productQuery->get()->map(function($item) {
            $item->item_type = 'product';
            return $item;
        });

        $packages = ($type === 'product') ? collect() : $packageQuery->get()->map(function($item) {
            $item->item_type = 'package';
            return $item;
        });

        // Merge and sort
        $merged = $products->merge($packages)->sortByDesc('created_at');

        // Manual pagination
        $perPage = 5;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $merged->slice(($currentPage - 1) * $perPage, $perPage)->values();
        
        $bookings = new LengthAwarePaginator(
            $currentItems,
            $merged->count(),
            $perPage,
            $currentPage,
            ['path' => LengthAwarePaginator::resolveCurrentPath(), 'query' => $request->query()]
        );

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

    public function edit($type, $bookingId)
    {
        $booking = $this->resolveBooking($type, $bookingId);
        $booking->item_type = $type;
        $users = User::all();
        
        if ($type === 'product') {
            $products = Product::all();
            return view('admin.bookings.edit', compact('booking', 'users', 'products'));
        } else {
            $packages = Package::all();
            return view('admin.bookings.edit', compact('booking', 'users', 'packages'));
        }
    }

    public function update(Request $request, $type, $bookingId)
    {
        $validated = $request->validate([
            'id_user' => 'required|exists:users,id',
            'status' => 'required|in:pending,confirmed,active,completed,cancelled',
            'checkin_appointment_start' => 'required|date',
            'checkout_appointment_end' => 'required|date|after:checkin_appointment_start',
            'amount' => 'required|integer|min:1',
            'booker_name' => 'required|string|max:255',
            'booker_email' => 'required|email|max:255',
            'booker_telp' => 'required|string|max:20',
        ]);

        if ($type === 'product') {
            $validated['id_product'] = $request->validate(['id_product' => 'required|exists:products,id'])['id_product'];
        } else {
            $validated['id_package'] = $request->validate(['id_package' => 'required|exists:packages,id'])['id_package'];
        }

        $booking = $this->resolveBooking($type, $bookingId);
        $booking->update($validated);

        alert()->success('Success', 'Booking updated successfully!');
        return redirect()->route('admin.bookings.index');
    }

    public function show($type, $bookingId)
    {
        $booking = $this->resolveBooking($type, $bookingId);
        $booking->item_type = $type;
        
        if ($type === 'product') {
            $booking->load('user', 'product.category', 'product.brand');
        } else {
            $booking->load('user', 'package');
        }
        
        return view('admin.bookings.show', compact('booking'));
    }

    public function destroy($type, $bookingId)
    {
        $booking = $this->resolveBooking($type, $bookingId);
        $booking->delete();
        alert()->success('Deleted', 'Booking deleted successfully!');
        return redirect()->route('admin.bookings.index');
    }

    /**
     * Update booking data via AJAX (from detail modal)
     */
    public function updateData(Request $request, $type, $bookingId)
    {
        try {
            $validated = $request->validate([
                'status' => 'required|in:pending,confirmed,active,completed,cancelled',
            ]);

            $booking = $this->resolveBooking($type, $bookingId);
            $booking->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Booking status updated successfully',
                'data' => ['status' => ucfirst($booking->status)]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Resolve booking by type
     */
    private function resolveBooking($type, $id)
    {
        if ($type === 'product') {
            return BookProduct::findOrFail($id);
        } else {
            return Book::findOrFail($id);
        }
    }
}
