<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Package;
use App\Models\Book;
use App\Models\BookProduct;
use App\Models\DetailBook;
use App\Models\Payment;
use App\Enums\OrderStatus;
use App\Enums\ItemStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BookingPackageController extends Controller
{
    public function create($packageId)
    {
        if ($response = $this->blockIfHasOutstandingPenalty()) {
            return $response;
        }

        $package = Package::with(['products'])->findOrFail($packageId);
        return view('user.booking.package', compact('package'));
    }

    public function store(Request $request, $packageId)
    {
        if ($response = $this->blockIfHasOutstandingPenalty()) {
            return $response;
        }

        $package = Package::with(['products'])->findOrFail($packageId);
        $validated = $request->validate([
            'booker_name' => 'required|string|max:255',
            'booker_email' => 'required|email',
            'booker_telp' => 'required|string|max:20',
            'full_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'emergency_phone_number' => 'required|string|max:20',
            'instagram_handle' => 'nullable|string|max:100',
            'other_socials' => 'nullable|string|max:255',
            'renter_address' => 'required|string|max:500',
            'identity_document' => 'required|image|mimes:jpg,jpeg,png|max:4096',
            'rental_start_at' => 'required|date|after_or_equal:today',
            'rental_end_at' => 'required|date|after_or_equal:rental_start_at',
        ]);
        // Simpan detail penyewa ke tabel detail_books
        $identityPath = $request->file('identity_document')->store('identity_docs', 'public');

        DB::transaction(function () use ($package, $validated, $identityPath) {
            foreach ($package->products as $packageProduct) {
                $requestedQty = (int) ($packageProduct->pivot->qty ?? 1);
                $lockedProduct = \App\Models\Product::whereKey($packageProduct->id)->lockForUpdate()->firstOrFail();

                if ((int) $lockedProduct->stock < $requestedQty) {
                    throw ValidationException::withMessages([
                        'package' => "Stok {$lockedProduct->name} tidak cukup. Tersedia {$lockedProduct->stock}, dibutuhkan {$requestedQty}.",
                    ]);
                }

                $lockedProduct->decrement('stock', $requestedQty);
            }

            // Simpan booking package ke tabel book
            $booking = new Book();
            $booking->id_user = Auth::id();
            $booking->id_package = $package->id;
            $booking->book_code = 'BK-' . strtoupper(uniqid());
            $booking->status = 'pending';
            $booking->order_status = OrderStatus::PENDING; // Menunggu validasi officer
            $booking->item_status = ItemStatus::BOOKED; // Item is now booked for this user's dates
            $booking->checkin_appointment_start = $validated['rental_start_at'];
            $booking->checkout_appointment_end = $validated['rental_end_at'];
            $booking->amount = 1;
            $booking->booker_name = $validated['booker_name'];
            $booking->booker_email = $validated['booker_email'];
            $booking->booker_telp = $validated['booker_telp'];
            $booking->save();

            $detail = new DetailBook();
            $detail->id_book = $booking->id;
            $detail->full_name = $validated['full_name'];
            $detail->instagram_handle = $validated['instagram_handle'] ?? null;
            $detail->other_socials = $validated['other_socials'] ?? null;
            $detail->phone_number = $validated['phone_number'];
            $detail->emergency_phone_number = $validated['emergency_phone_number'];
            $detail->renter_address = $validated['renter_address'];
            $detail->rental_start_at = $validated['rental_start_at'];
            $detail->rental_end_at = $validated['rental_end_at'];
            $detail->identity_document_path = $identityPath;
            $detail->save();

            // Simpan detail produk-produk package ke tabel book_package_products
            foreach ($package->products as $packageProduct) {
                $requestedQty = (int) ($packageProduct->pivot->qty ?? 1);
                \App\Models\BookPackageProduct::create([
                    'id' => (string) \Illuminate\Support\Str::uuid(),
                    'id_book' => $booking->id,
                    'id_product' => $packageProduct->id,
                    'qty' => $requestedQty,
                ]);
            }
        });

        // Redirect ke halaman sukses atau detail booking
        return redirect()->route('user.my-booking')->with('success', 'Booking paket berhasil dibuat!');
    }

    private function blockIfHasOutstandingPenalty()
    {
        $count = $this->pendingPenaltyQuery()->count();
        if ($count < 1) {
            return null;
        }

        $pendingTotal = $this->pendingPenaltyQuery()->sum('amount');

        return redirect()
            ->route('user.my-booking')
            ->with('error', sprintf(
                'Anda memiliki %d denda belum dibayar (total Rp %s). Selesaikan pembayaran denda terlebih dahulu sebelum booking lagi.',
                $count,
                number_format($pendingTotal / 100, 0, ',', '.')
            ));
    }

    private function pendingPenaltyQuery()
    {
        return Payment::query()
            ->where('status', 'pending')
            ->where('method', 'penalty')
            ->where(function ($query) {
                $query->whereHasMorph('payable', [BookProduct::class], function ($bookingQuery) {
                    $bookingQuery->where('id_user', Auth::id());
                })->orWhereHasMorph('payable', [Book::class], function ($bookingQuery) {
                    $bookingQuery->where('id_user', Auth::id());
                });
            });
    }
}
