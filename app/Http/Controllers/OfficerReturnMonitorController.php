<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BookProduct;
use App\Models\Book;
use App\Enums\OrderStatus;
use App\Enums\ItemStatus;
use App\Services\ItemStatusTransitionService;

class OfficerReturnMonitorController extends Controller
{
    protected ItemStatusTransitionService $transitionService;

    public function __construct(ItemStatusTransitionService $transitionService)
    {
        $this->transitionService = $transitionService;
    }

    public function index()
    {
        // Show all items in return process (delivered to user, scheduled pickup, returning, or pending review)
        $bookProducts = BookProduct::whereIn('order_status', [
                OrderStatus::DELIVERED,
                OrderStatus::PICKUP_SCHEDULED,
                OrderStatus::ON_PROCESS_RETURN,
                OrderStatus::PENDING_REVIEW
            ])
            ->with(['user', 'product', 'courier'])
            ->orderBy('checkout_appointment_end', 'asc')
            ->get();

        $books = Book::whereIn('order_status', [
                OrderStatus::DELIVERED,
                OrderStatus::PICKUP_SCHEDULED,
                OrderStatus::ON_PROCESS_RETURN,
                OrderStatus::PENDING_REVIEW
            ])
            ->with(['user', 'package', 'courier'])
            ->orderBy('checkout_appointment_end', 'asc')
            ->get();

        // Merge both collections
        $returns = $bookProducts->concat($books)->sortBy('checkout_appointment_end');

        // Paginate manually
        $perPage = 10;
        $currentPage = \Illuminate\Pagination\Paginator::resolveCurrentPage();
        $currentItems = $returns->slice(($currentPage - 1) * $perPage, $perPage)->values();
        $returns = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentItems,
            $returns->count(),
            $perPage,
            $currentPage,
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
        );

        return view('officer.returns-monitor', compact('returns'));
    }

    public function process($id)
    {
        // Try to find in BookProduct first
        $return = BookProduct::find($id);
        
        if (!$return) {
            // If not found, try Book
            $return = Book::findOrFail($id);
        }

        // Officer can only process items in PENDING_REVIEW status
        if ($return->order_status !== OrderStatus::PENDING_REVIEW) {
            return redirect()->back()->with('error', 'Item must be in Pending Review status to process return.');
        }

        // Mark as completed using transition service
        $this->transitionService->transitionItemStatus($return, ItemStatus::AVAILABLE);
        $this->transitionService->syncOrderStatus($return, ItemStatus::AVAILABLE);
        $return->returned_at = now();
        $return->save();

        return redirect()->back()->with('success', 'Return processed successfully. Item is now available for booking.');
    }
}
