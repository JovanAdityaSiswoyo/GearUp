<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\BookProduct;
use Illuminate\Http\Request;

class ReconciliationController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with(['payable'])->latest();
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter by method
        if ($request->filled('method')) {
            $query->where('method', $request->method);
        }
        
        // Search by provider ref or amount
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('provider_ref', 'like', '%' . $request->search . '%')
                  ->orWhere('amount', 'like', '%' . $request->search . '%');
            });
        }
        
        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        $payments = $query->paginate(10)->withQueryString();
        
        // Calculate summary
        $summary = [
            'total_payments' => Payment::count(),
            'total_amount' => Payment::sum('amount'),
            'paid_amount' => Payment::where('status', 'paid')->sum('amount'),
            'pending_amount' => Payment::where('status', 'pending')->sum('amount'),
            'failed_amount' => Payment::where('status', 'failed')->sum('amount'),
        ];
        
        return view('admin.reconciliation.index', compact('payments', 'summary'));
    }

    public function verify(Request $request, Payment $reconciliation)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,paid,failed,refunded',
            'notes' => 'nullable|string',
        ]);

        $reconciliation->update([
            'status' => $validated['status'],
            'paid_at' => $validated['status'] === 'paid' ? now() : null,
            'failed_at' => $validated['status'] === 'failed' ? now() : null,
            'refunded_at' => $validated['status'] === 'refunded' ? now() : null,
        ]);

        alert()->success('Success', 'Payment status updated!');
        return redirect()->route('admin.reconciliation.index');
    }

    public function report(Request $request)
    {
        $startDate = $request->input('date_from', now()->startOfMonth()->toDateString());
        $endDate = $request->input('date_to', now()->endOfMonth()->toDateString());

        $payments = Payment::whereBetween('created_at', [$startDate, $endDate])
            ->with(['payable'])
            ->get();

        $paymentsByStatus = $payments->groupBy('status');
        $paymentsByMethod = $payments->groupBy('method');
        $paymentsByProvider = $payments->groupBy('provider');

        $report = [
            'period' => "$startDate to $endDate",
            'total_transactions' => $payments->count(),
            'total_amount' => $payments->sum('amount'),
            'by_status' => $payments->groupBy('status')->map->sum('amount')->toArray(),
            'by_method' => $payments->groupBy('method')->map->sum('amount')->toArray(),
            'by_provider' => $payments->groupBy('provider')->map->sum('amount')->toArray(),
        ];

        return view('admin.reconciliation.report', compact('payments', 'report', 'startDate', 'endDate', 'paymentsByStatus', 'paymentsByMethod', 'paymentsByProvider'));
    }

    public function matchBookings(Request $request)
    {
        // Find unmatched bookings and payments
        $bookings = BookProduct::where('status', 'active')
            ->doesntHave('payments')
            ->with(['user', 'product'])
            ->paginate(10);

        return view('admin.reconciliation.match-bookings', compact('bookings'));
    }

    public function createPayment(Request $request, BookProduct $booking)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'method' => 'required|string|max:255',
            'status' => 'required|in:pending,paid,failed',
            'provider' => 'nullable|string|max:255',
            'provider_ref' => 'nullable|string|max:255',
        ]);

        Payment::create([
            'payable_type' => BookProduct::class,
            'payable_id' => $booking->id,
            'amount' => (int)($validated['amount'] * 100), // Convert to cents
            'currency' => 'IDR',
            'status' => $validated['status'],
            'provider' => $validated['provider'] ?? 'manual',
            'provider_ref' => $validated['provider_ref'],
            'method' => $validated['method'],
            'paid_at' => $validated['status'] === 'paid' ? now() : null,
        ]);

        alert()->success('Success', 'Payment recorded for booking!');
        return redirect()->route('admin.reconciliation.match-bookings');
    }
}
