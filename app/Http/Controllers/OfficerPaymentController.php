<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;

class OfficerPaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with('payable')->latest();

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
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
            'total_amount' => Payment::where('status', 'paid')->sum('amount'),
        ];

        return view('officer.payments.index', compact('payments', 'stats'));
    }
}
