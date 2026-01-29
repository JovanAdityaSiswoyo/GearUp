<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BookProduct;
use App\Models\Book;

class OfficerReportController extends Controller
{
    public function print(Request $request)
    {
        $from = $request->input('from');
        $to = $request->input('to');
        $type = $request->input('type', 'all');

        $query = BookProduct::query()->with(['user', 'product']);
        if ($from) $query->whereDate('created_at', '>=', $from);
        if ($to) $query->whereDate('created_at', '<=', $to);
        if ($type == 'loan') $query->where('status', 'approved');
        if ($type == 'return') $query->where('status', 'returned');

        $reports = $query->paginate(5);
        // $reports is now a paginator of BookProduct, so use $report->created_at, $report->user, $report->equipment, $report->status, etc in the view
        return view('officer.print-report', compact('reports'));
    }
}
