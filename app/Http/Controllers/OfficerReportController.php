<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BookProduct;
use App\Models\Book;
use App\Enums\OrderStatus;

class OfficerReportController extends Controller
{
    public function print(Request $request)
    {
        $from = $request->input('from');
        $to = $request->input('to');
        $type = $request->input('type', 'all');

        $query = BookProduct::query()->with(['user', 'product', 'courier']);
        
        // Filter by date range
        if ($from) $query->whereDate('created_at', '>=', $from);
        if ($to) $query->whereDate('created_at', '<=', $to);
        
        // Filter by type
        if ($type == 'loan') {
            // Loan: barang yang sudah dikirim ke user
            $query->whereIn('order_status', [
                OrderStatus::DELIVERED->value,
                OrderStatus::OUT_FOR_DELIVERY->value,
                OrderStatus::READY_FOR_PICKUP->value,
            ]);
        } elseif ($type == 'return') {
            // Return: barang yang sudah atau sedang dikembalikan
            $query->whereIn('order_status', [
                OrderStatus::PICKUP_SCHEDULED->value,
                OrderStatus::ON_PROCESS_RETURN->value,
                OrderStatus::PENDING_REVIEW->value,
                OrderStatus::COMPLETED->value,
            ]);
        }

        $reports = $query->orderBy('created_at', 'desc')->paginate(20);
        return view('officer.print-report', compact('reports'));
    }
}
