<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookProduct;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class FineController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $baseQuery = Payment::where('method', 'penalty')
            ->where(function ($query) use ($user) {
                $query->whereHasMorph('payable', [BookProduct::class], function ($bookingQuery) use ($user) {
                    $bookingQuery->where('id_user', $user->id);
                })->orWhereHasMorph('payable', [Book::class], function ($bookingQuery) use ($user) {
                    $bookingQuery->where('id_user', $user->id);
                });
            });

        $query = (clone $baseQuery)->with('payable');

        // Filter by date range
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [
                $request->start_date . ' 00:00:00',
                $request->end_date . ' 23:59:59'
            ]);
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'pending') {
                $query->where('status', 'pending');
            } elseif ($request->status === 'paid') {
                $query->where('status', 'paid');
            }
        }

        $fines = $query->latest()->paginate(12);

        // Load polymorphic relationships
        $fines->loadMorph('payable', [
            BookProduct::class => ['product'],
            Book::class => ['package']
        ]);

        $totalFines = (clone $baseQuery)->count();
        $totalPending = (clone $baseQuery)->where('status', 'pending')->count();
        $totalPaid = (clone $baseQuery)->where('status', 'paid')->count();
        $totalAmount = (clone $baseQuery)->where('status', 'pending')->sum('amount') / 100;

        return view('user.fines.fines', compact(
            'fines',
            'totalFines',
            'totalPending',
            'totalPaid',
            'totalAmount'
        ));
    }

    public function verify(Request $request, $id)
    {
        $user = Auth::user();

        $fine = Payment::where('method', 'penalty')
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

        $fine->update(['status' => 'paid']);

        return redirect()->back()->with('success', 'Denda berhasil diverifikasi dan dibayar.');
    }

    public function pay(Request $request, $id)
    {
        $user = Auth::user();

        $fine = Payment::where('method', 'penalty')
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

        // Here you would integrate with payment gateway
        // For now, just mark as paid
        $fine->update(['status' => 'paid']);

        return redirect()->back()->with('success', 'Denda berhasil dibayar.');
    }

    public function exportPdf(Request $request)
    {
        $user = Auth::user();

        $query = Payment::where('method', 'penalty')
            ->where(function ($query) use ($user) {
                $query->whereHasMorph('payable', [BookProduct::class], function ($bookingQuery) use ($user) {
                    $bookingQuery->where('id_user', $user->id);
                })->orWhereHasMorph('payable', [Book::class], function ($bookingQuery) use ($user) {
                    $bookingQuery->where('id_user', $user->id);
                });
            })
            ->with('payable');

        // Apply same filters as index
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [
                $request->start_date . ' 00:00:00',
                $request->end_date . ' 23:59:59'
            ]);
        }

        if ($request->filled('status')) {
            if ($request->status === 'pending') {
                $query->where('status', 'pending');
            } elseif ($request->status === 'paid') {
                $query->where('status', 'paid');
            }
        }

        $fines = $query->latest()->get();

        // Load polymorphic relationships
        $fines->loadMorph('payable', [
            BookProduct::class => ['product'],
            Book::class => ['package']
        ]);

        $pdf = Pdf::loadView('user.fines.pdf', compact('fines', 'user'));

        return $pdf->download('denda-' . $user->name . '.pdf');
    }
}