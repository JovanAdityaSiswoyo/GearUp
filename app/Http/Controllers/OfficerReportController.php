<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BookProduct;
use App\Models\Book;
use App\Enums\OrderStatus;
use Illuminate\Pagination\Paginator;

class OfficerReportController extends Controller
{
    public function print(Request $request)
    {
        $from = $request->input('from');
        $to = $request->input('to');
        $type = $request->input('type', 'all');

        // Query BookProduct
        $bookProductQuery = BookProduct::query()->with(['user', 'product', 'courier']);
        
        // Query Book (Package)
        $bookQuery = Book::query()->with(['user', 'package', 'courier']);
        
        // Filter by date range
        if ($from) {
            $bookProductQuery->whereDate('created_at', '>=', $from);
            $bookQuery->whereDate('created_at', '>=', $from);
        }
        if ($to) {
            $bookProductQuery->whereDate('created_at', '<=', $to);
            $bookQuery->whereDate('created_at', '<=', $to);
        }
        
        // Filter by type
        if ($type == 'loan') {
            // Loan: barang yang sudah dikirim ke user
            $statuses = [
                OrderStatus::DELIVERED->value,
                OrderStatus::OUT_FOR_DELIVERY->value,
                OrderStatus::READY_FOR_PICKUP->value,
            ];
            $bookProductQuery->whereIn('order_status', $statuses);
            $bookQuery->whereIn('order_status', $statuses);
        } elseif ($type == 'return') {
            // Return: barang yang sudah atau sedang dikembalikan
            $statuses = [
                OrderStatus::PICKUP_SCHEDULED->value,
                OrderStatus::ON_PROCESS_RETURN->value,
                OrderStatus::PENDING_REVIEW->value,
                OrderStatus::COMPLETED->value,
            ];
            $bookProductQuery->whereIn('order_status', $statuses);
            $bookQuery->whereIn('order_status', $statuses);
        }

        // Get all results
        $bookProducts = $bookProductQuery->orderBy('created_at', 'desc')->get();
        $books = $bookQuery->orderBy('created_at', 'desc')->get();
        
        // Merge collections
        $allReports = collect()
            ->merge($bookProducts)
            ->merge($books)
            ->sortByDesc('created_at')
            ->values();
        
        // Manual pagination
        $page = $request->get('page', 1);
        $perPage = 20;
        $total = $allReports->count();
        $items = $allReports->slice(($page - 1) * $perPage, $perPage);
        
        $reports = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );
        
        return view('officer.print-report', compact('reports'));
    }
}
