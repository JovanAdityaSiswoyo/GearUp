<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookProduct;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class ReturnController extends Controller
{
    public function index(Request $request)
    {
        $productQuery = BookProduct::with(['user', 'product'])
            ->whereIn('status', ['active', 'completed']);

        $packageQuery = Book::with(['user', 'package'])
            ->whereIn('status', ['active', 'completed']);

        $typeFilter = $request->get('type');

        if ($request->filled('status')) {
            $productQuery->where('status', $request->status);
            $packageQuery->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;

            $productQuery->where(function($q) use ($search) {
                $q->where('book_code', 'like', '%' . $search . '%')
                  ->orWhere('booker_name', 'like', '%' . $search . '%')
                  ->orWhere('booker_email', 'like', '%' . $search . '%')
                  ->orWhere('booker_telp', 'like', '%' . $search . '%')
                  ->orWhereHas('product', function($productQuery) use ($search) {
                      $productQuery->where('name', 'like', '%' . $search . '%');
                  })
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', '%' . $search . '%')
                          ->orWhere('email', 'like', '%' . $search . '%');
                  });
            });

            $packageQuery->where(function($q) use ($search) {
                $q->where('book_code', 'like', '%' . $search . '%')
                  ->orWhere('booker_name', 'like', '%' . $search . '%')
                  ->orWhere('booker_email', 'like', '%' . $search . '%')
                  ->orWhere('booker_telp', 'like', '%' . $search . '%')
                  ->orWhereHas('package', function($packageQuery) use ($search) {
                      $packageQuery->where('name_package', 'like', '%' . $search . '%');
                  })
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', '%' . $search . '%')
                          ->orWhere('email', 'like', '%' . $search . '%');
                  });
            });
        }

        $productReturns = $productQuery->get()->map(function($return) {
            $return->item_type = 'product';
            return $return;
        });

        $packageReturns = $packageQuery->get()->map(function($return) {
            $return->item_type = 'package';
            return $return;
        });

        if ($typeFilter === 'product') {
            $packageReturns = collect();
        }

        if ($typeFilter === 'package') {
            $productReturns = collect();
        }

        $allReturns = $productReturns
            ->concat($packageReturns)
            ->sortByDesc('created_at')
            ->values();

        $perPage = 5;
        $currentPage = (int) $request->get('page', 1);
        $total = $allReturns->count();
        $returns = new LengthAwarePaginator(
            $allReturns->forPage($currentPage, $perPage),
            $total,
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.returns.index', compact('returns'));
    }

    public function show(string $type, string $returnId)
    {
        $return = $this->resolveReturn($type, $returnId);
        return view('admin.returns.show', compact('return'));
    }

    public function process(Request $request, string $type, string $returnId)
    {
        $validated = $request->validate([
            'status' => 'required|in:completed,cancelled',
            'notes' => 'nullable|string',
        ]);

        $return = $this->resolveReturn($type, $returnId);
        $return->update([
            'status' => $validated['status'],
        ]);

        alert()->success('Success', 'Return processed successfully!');
        return redirect()->route('admin.returns.index');
    }

    private function resolveReturn(string $type, string $returnId): Book|BookProduct
    {
        if ($type === 'package') {
            $return = Book::with(['user', 'package', 'detailBooks'])->findOrFail($returnId);
            $return->item_type = 'package';
            return $return;
        }

        $return = BookProduct::with(['user', 'product', 'detailBookProducts'])->findOrFail($returnId);
        $return->item_type = 'product';
        return $return;
    }
}
