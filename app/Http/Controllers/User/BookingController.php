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
    public function cartBooking(Request $request)
    {
        if ($request->isMethod('post')) {
            $validated = $request->validate([
                'products' => 'required|array|min:1',
                'products.*' => 'exists:products,id',
                'amount' => 'required|integer|min:1',
                'booker_name' => 'required|string|max:255',
                'booker_email' => 'required|email|max:255',
                'booker_telp' => 'required|string|max:20',
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

            $identityPath = $request->file('identity_document')->store('identity_documents', 'public');

            foreach ($validated['products'] as $productId) {
                $bookProduct = BookProduct::create([
                    'book_code' => 'BK-' . strtoupper(Str::random(8)),
                    'id_user' => auth()->id(),
                    'id_product' => $productId,
                    'status' => 'pending',
                    'checkin_appointment_start' => $validated['rental_start_at'],
                    'checkout_appointment_end' => $validated['rental_end_at'],
                    'amount' => $validated['amount'],
                    'booker_name' => $validated['booker_name'],
                    'booker_email' => $validated['booker_email'],
                    'booker_telp' => $validated['booker_telp'],
                ]);

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
            }

            // Kosongkan session cart_checkout setelah booking
            session()->forget('cart_checkout');

            return redirect()->route('user.my-booking')->with('success', 'Booking untuk semua produk berhasil dibuat! Kami akan segera menghubungi Anda untuk konfirmasi booking.');
        }

        $cart = session('cart_checkout', []);
        if (empty($cart)) {
            return redirect()->route('user.cart.index')->with('error', 'Cart kosong.');
        }
        $products = Product::whereIn('id', $cart)->get();
        // Tampilkan form booking massal
        return view('user.booking.cart-booking', compact('products'));
    }

    public function create(Request $request, Product $product = null)
    {
        // Cek apakah ada parameter products[] (array) di query string
        $productIds = $request->input('products');
        if ($productIds) {
            $products = Product::whereIn('id', $productIds)->get();
        } elseif ($product) {
            $products = collect([$product]);
        } else {
            $products = collect();
        }
        return view('user.booking.create', compact('products'));
    }

    public function createMulti(Request $request)
    {
        $productIds = $request->input('products', []);
        $products = Product::whereIn('id', $productIds)->get();
        return view('user.booking.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'products' => 'required|array|min:1',
            'products.*' => 'exists:products,id',
            'amount' => 'required|array',
            'amount.*' => 'required|integer|min:1',
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

        foreach ($validated['products'] as $productId) {
            $amount = $validated['amount'][$productId] ?? 1;
            $bookProduct = BookProduct::create([
                'book_code' => 'BK-' . strtoupper(Str::random(8)),
                'id_user' => auth()->id(),
                'id_product' => $productId,
                'status' => 'pending',
                'checkin_appointment_start' => $validated['rental_start_at'],
                'checkout_appointment_end' => $validated['rental_end_at'],
                'amount' => $amount,
                'booker_name' => $validated['booker_name'],
                'booker_email' => $validated['booker_email'],
                'booker_telp' => $validated['booker_telp'],
            ]);

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
        }

        return redirect()->route('user.my-booking')->with('success', 'Booking untuk semua produk berhasil dibuat! Kami akan segera menghubungi Anda untuk konfirmasi booking.');
    }

    public function myBooking()
    {
        $bookings = BookProduct::with(['product', 'detailBookProduct'])
            ->where('id_user', auth()->id())
            ->latest()
            ->get();

        // Ambil riwayat booking package user
        $packageBookings = \App\Models\Book::with(['package', 'detailBooks'])
            ->where('id_user', auth()->id())
            ->latest()
            ->get();

        return view('user.booking.my-booking', compact('bookings', 'packageBookings'));
    }
}
