<?php

namespace App\Http\Controllers;

use App\Models\BookProduct;
use Illuminate\Http\Request;

class ReturnController extends Controller
{
    public function index(Request $request)
    {
        $query = BookProduct::with(['user', 'product'])
            ->whereIn('status', ['active', 'completed'])
            ->latest();
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Search
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('book_code', 'like', '%' . $request->search . '%')
                  ->orWhere('booker_name', 'like', '%' . $request->search . '%');
            });
        }
        
        $returns = $query->paginate(5)->withQueryString();
        return view('admin.returns.index', compact('returns'));
    }

    public function show(BookProduct $return)
    {
        $return->load(['user', 'product', 'detailBookProducts']);
        return view('admin.returns.show', compact('return'));
    }

    public function process(Request $request, BookProduct $return)
    {
        $validated = $request->validate([
            'status' => 'required|in:completed,cancelled',
            'notes' => 'nullable|string',
        ]);

        $return->update([
            'status' => $validated['status'],
        ]);

        alert()->success('Success', 'Return processed successfully!');
        return redirect()->route('admin.returns.index');
    }
}
