<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookProduct;
use App\Models\Payment;
use App\Models\Transaction;
use App\Services\MidtransGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class TransactionController extends Controller
{
    public function payPenalty(Request $request, $id, MidtransGateway $midtrans)
    {
        $user = Auth::user();

        $payment = Payment::where('method', 'penalty')
            ->where('id', $id)
            ->where('status', 'pending')
            ->where(function ($query) use ($user) {
                $query->whereHasMorph('payable', [BookProduct::class], function ($bookingQuery) use ($user) {
                    $bookingQuery->where('id_user', $user->id);
                })->orWhereHasMorph('payable', [Book::class], function ($bookingQuery) use ($user) {
                    $bookingQuery->where('id_user', $user->id);
                });
            })
            ->firstOrFail();

        $transaction = $this->createMidtransTransaction($payment, $midtrans);

        return redirect()->route('user.payment.checkout', $transaction->id);
    }

    public function payBooking(Request $request, $type, $id, MidtransGateway $midtrans)
    {
        $user = Auth::user();

        $booking = $type === 'package'
            ? Book::where('id', $id)->where('id_user', $user->id)->firstOrFail()
            : BookProduct::where('id', $id)->where('id_user', $user->id)->firstOrFail();

        $amount = $this->getBookingAmount($booking);
        if ($amount <= 0) {
            return redirect()->back()->with('error', 'Jumlah pembayaran booking tidak valid.');
        }

        $payment = $booking->payments()->firstOrCreate(
            [
                'method' => 'booking',
                'status' => 'pending',
            ],
            [
                'amount'   => $amount,
                'currency' => 'IDR',
                'provider' => 'manual',
                'meta'     => [
                    'booking_type' => $type,
                    'book_code'    => $booking->book_code,
                ],
            ]
        );

        if ($payment->status !== 'pending') {
            $payment->update(['status' => 'pending', 'paid_at' => null, 'failed_at' => null, 'refunded_at' => null]);
        }

        $transaction = $this->createMidtransTransaction(
            $payment,
            $midtrans,
            sprintf('BOOKING-%s', strtoupper(Str::random(6)))
        );

        return redirect()->route('user.payment.checkout', $transaction->id);
    }

    public function checkout(Transaction $transaction)
    {
        $payment = $transaction->payment;

        if (! $payment || Auth::id() !== $payment->payable->id_user) {
            abort(403);
        }

        // Ambil snap token dari response_payload yang sudah disimpan
        $snapToken = data_get($transaction->response_payload, 'snap_token');

        // Kalau token tidak ada (transaksi lama / data rusak), regenerate
        if (! $snapToken) {
            abort(500, 'Snap token tidak ditemukan. Silakan coba lagi.');
        }

        return view('user.payments.checkout', compact('transaction', 'payment', 'snapToken'));
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    private function createMidtransTransaction(
        Payment $payment,
        MidtransGateway $midtrans,
        string $customOrderId = null
    ): Transaction {
        // Cek apakah sudah ada transaksi pending yang belum expired
        $existingTransaction = $payment->transactions()
            ->whereIn('status', ['pending', 'challenge'])
            ->latest()
            ->first();

        if ($existingTransaction && ! $this->isExpired($existingTransaction)) {
            // Pastikan snap_token masih ada, kalau tidak generate ulang
            $snapToken = data_get($existingTransaction->response_payload, 'snap_token');
            if ($snapToken) {
                return $existingTransaction;
            }
        }

        $orderId = $customOrderId ?? sprintf('MIDTRANS-%s', strtoupper(Str::random(8)));
        $orderId = substr($orderId, 0, 50);

        $customer = [
            'first_name' => $payment->payable->booker_name ?? Auth::user()->name,
            'email'      => $payment->payable->booker_email ?? Auth::user()->email,
            'phone'      => $payment->payable->booker_telp ?? Auth::user()->phone ?? null,
        ];

        // Generate Snap token (popup)
        $snapToken = $midtrans->createSnapToken($orderId, $payment->amount, $customer);

        $payment->update([
            'provider'     => 'midtrans',
            'provider_ref' => null,
            'status'       => 'pending',
            'meta'         => array_merge($payment->meta ?? [], [
                'midtrans_order_id' => $orderId,
                'payment_type'      => 'snap',
            ]),
        ]);

        return $payment->transactions()->create([
            'provider'         => 'midtrans',
            'payment_type'     => 'snap',
            'bank'             => null,
            'status'           => 'pending',
            'transaction_id'   => null,
            'order_id'         => $orderId,
            'amount'           => $payment->amount,
            'currency'         => $payment->currency,
            'expires_at'       => now()->addHours(24),
            'request_payload'  => [
                'transaction_details' => [
                    'order_id'     => $orderId,
                    'gross_amount' => $payment->amount,
                ],
                'customer_details' => $customer,
            ],
            'response_payload' => [
                'snap_token' => $snapToken,
            ],
        ]);
    }

    private function getBookingAmount($booking): int
    {
        if ($booking instanceof BookProduct) {
            return (int) round($booking->rental_total * 100);
        }

        if ($booking instanceof Book) {
            $dailyPrice = (float) ($booking->package?->price ?? 0);
            $days       = $this->getRentalDays($booking);

            return (int) round($dailyPrice * $days * 100);
        }

        return 0;
    }

    private function getRentalDays($booking): int
    {
        if (! $booking->checkin_appointment_start || ! $booking->checkout_appointment_end) {
            return 1;
        }

        $start = $booking->checkin_appointment_start->copy()->startOfDay();
        $end   = $booking->checkout_appointment_end->copy()->startOfDay();

        return max(1, $start->diffInDays($end) + 1);
    }

    private function isExpired(Transaction $transaction): bool
    {
        return $transaction->expires_at && $transaction->expires_at->isPast();
    }
}