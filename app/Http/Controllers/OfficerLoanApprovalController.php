<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BookProduct;
use App\Models\Book;

class OfficerLoanApprovalController extends Controller
{
    public function index()
    {
        $pendingLoans = BookProduct::where('status', 'pending')->with(['user', 'product'])->paginate(5);
        return view('officer.loan-approvals', compact('pendingLoans'));
    }

    public function approve($id)
    {
        $loan = BookProduct::findOrFail($id);
        $loan->status = 'approved';
        $loan->save();
        return redirect()->back()->with('success', 'Loan approved successfully.');
    }

    public function reject($id)
    {
        $loan = BookProduct::findOrFail($id);
        $loan->status = 'rejected';
        $loan->save();
        return redirect()->back()->with('error', 'Loan rejected.');
    }
}
