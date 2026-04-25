<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookProduct;
use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf;
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

    public function penalties(Request $request)
    {
        $baseQuery = Payment::query()
            ->where('method', 'penalty')
            ->with('payable')
            ->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $baseQuery->where(function ($query) use ($search) {
                $query->where('provider_ref', 'like', '%' . $search . '%')
                    ->orWhere('id', 'like', '%' . $search . '%')
                    ->orWhere('meta->book_code', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $baseQuery->whereBetween('created_at', [
                $request->start_date . ' 00:00:00',
                $request->end_date . ' 23:59:59',
            ]);
        }

        $unpaidPenalties = (clone $baseQuery)
            ->where('status', 'pending')
            ->paginate(10, ['*'], 'unpaid_page')
            ->withQueryString();

        $paidPenalties = (clone $baseQuery)
            ->where('status', 'paid')
            ->paginate(10, ['*'], 'paid_page')
            ->withQueryString();

        $unpaidPenalties->getCollection()->loadMorph('payable', [
            BookProduct::class => ['user', 'product'],
            Book::class => ['user', 'package'],
        ]);

        $paidPenalties->getCollection()->loadMorph('payable', [
            BookProduct::class => ['user', 'product'],
            Book::class => ['user', 'package'],
        ]);

        $summary = [
            'total_penalties' => Payment::where('method', 'penalty')->count(),
            'unpaid_count' => Payment::where('method', 'penalty')->where('status', 'pending')->count(),
            'paid_count' => Payment::where('method', 'penalty')->where('status', 'paid')->count(),
            'unpaid_amount' => Payment::where('method', 'penalty')->where('status', 'pending')->sum('amount'),
            'paid_amount' => Payment::where('method', 'penalty')->where('status', 'paid')->sum('amount'),
        ];

        return view('officer.payments.penalties', compact('unpaidPenalties', 'paidPenalties', 'summary'));
    }

    public function exportPdf(Request $request)
    {
        $baseQuery = Payment::query()
            ->where('method', 'penalty')
            ->with('payable')
            ->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $baseQuery->where(function ($query) use ($search) {
                $query->where('provider_ref', 'like', '%' . $search . '%')
                    ->orWhere('id', 'like', '%' . $search . '%')
                    ->orWhere('meta->book_code', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $baseQuery->whereBetween('created_at', [
                $request->start_date . ' 00:00:00',
                $request->end_date . ' 23:59:59',
            ]);
        }

        $payments = $baseQuery->get();

        $summary = [
            'total_penalties' => Payment::where('method', 'penalty')->count(),
            'unpaid_count' => Payment::where('method', 'penalty')->where('status', 'pending')->count(),
            'paid_count' => Payment::where('method', 'penalty')->where('status', 'paid')->count(),
            'unpaid_amount' => Payment::where('method', 'penalty')->where('status', 'pending')->sum('amount'),
            'paid_amount' => Payment::where('method', 'penalty')->where('status', 'paid')->sum('amount'),
        ];

        $pdf = Pdf::loadView('officer.payments.penalties-pdf', compact('payments', 'summary'));
        return $pdf->download('penalties-report-' . now()->format('YmdHis') . '.pdf');
    }
}
