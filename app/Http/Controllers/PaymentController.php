<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Book;
use App\Models\BookProduct;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with('payable')->latest();

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by payment method
        if ($request->filled('method')) {
            $query->where('method', $request->method);
        }

        // Search by provider reference
        if ($request->filled('search')) {
            $query->where('provider_ref', 'like', '%' . $request->search . '%');
        }

        $payments = $query->paginate(10);

        // Get statistics
        $stats = [
            'total' => Payment::count(),
            'paid' => Payment::where('status', 'paid')->count(),
            'pending' => Payment::where('status', 'pending')->count(),
            'failed' => Payment::where('status', 'failed')->count(),
            'refunded' => Payment::where('status', 'refunded')->count(),
            'total_amount' => Payment::where('status', 'paid')->sum('amount'),
        ];

        return view('admin.payments.index', compact('payments', 'stats'));
    }

    public function show($id)
    {
        $payment = Payment::with('payable')->findOrFail($id);
        return view('admin.payments.show', compact('payment'));
    }

    public function verify(Request $request, $id)
    {
        $payment = Payment::findOrFail($id);

        if ($payment->status !== 'pending') {
            alert()->warning('Warning', 'Only pending payments can be verified.');
            return back();
        }

        $payment->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        // Update booking status if applicable
        if ($payment->payable_type === Book::class) {
            $payment->payable->update(['status' => 'approved']);
        } elseif ($payment->payable_type === BookProduct::class) {
            $payment->payable->update(['status' => 'approved']);
        }

        alert()->success('Success', 'Payment verified successfully!');
        return back();
    }
}
