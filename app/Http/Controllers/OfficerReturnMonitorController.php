<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BookProduct;
use App\Models\Book;

class OfficerReturnMonitorController extends Controller
{
    public function index()
    {
        // Show all items that are currently borrowed (not yet returned)
        $returns = BookProduct::where('status', 'borrowed')
            ->with(['user', 'product'])
            ->orderBy('due_date', 'asc')
            ->paginate(5);
        return view('officer.returns-monitor', compact('returns'));
    }

    public function process($id)
    {
        $return = BookProduct::findOrFail($id);
        $return->status = 'returned';
        $return->returned_at = now();
        $return->save();
        return redirect()->back()->with('success', 'Return processed successfully.');
    }
}
