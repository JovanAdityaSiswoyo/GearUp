<?php

namespace App\Http\Controllers;

use App\Models\BookProduct;
use App\Models\Book;
use App\Models\Courier;
use App\Enums\OrderStatus;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class OfficerAssignCourierController extends Controller
{
    /**
     * Tampilkan halaman assign courier
     */
    public function index(Request $request): View
    {
        $tab = $request->get('tab', 'delivery');
        
        // Get all couriers
        $couriers = Courier::orderBy('name')->get();
        
        // Bookings yang perlu pengiriman (Ready for Pickup atau belum ada courier)
        $needDeliveryProducts = BookProduct::where(function($q) {
            $q->where('order_status', OrderStatus::READY_FOR_PICKUP)
              ->orWhere(function($q2) {
                  $q2->where('order_status', OrderStatus::CONFIRMED)
                     ->whereNull('id_courier');
              });
        })
        ->with(['product'])
        ->orderBy('created_at', 'desc')
        ->get();
        
        $needDeliveryBooks = Book::where(function($q) {
            $q->where('order_status', OrderStatus::READY_FOR_PICKUP)
              ->orWhere(function($q2) {
                  $q2->where('order_status', OrderStatus::CONFIRMED)
                     ->whereNull('id_courier');
              });
        })
        ->with(['package'])
        ->orderBy('created_at', 'desc')
        ->get();
        
        $needDelivery = $needDeliveryProducts->concat($needDeliveryBooks)->sortByDesc('created_at');
        
        // Bookings yang perlu penjemputan kembali (Delivered dan belum ada courier untuk return)
        $needReturnProducts = BookProduct::where('order_status', OrderStatus::DELIVERED)
            ->with(['product'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        $needReturnBooks = Book::where('order_status', OrderStatus::DELIVERED)
            ->with(['package'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        $needReturn = $needReturnProducts->concat($needReturnBooks)->sortByDesc('created_at');
        
        return view('officer.assign-courier', [
            'tab' => $tab,
            'couriers' => $couriers,
            'needDelivery' => $needDelivery,
            'needReturn' => $needReturn,
        ]);
    }
    
    /**
     * Proses assign courier
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'booking_id' => 'required|string',
            'booking_type' => 'required|in:product,package',
            'courier_id' => 'required|exists:couriers,id',
            'assignment_type' => 'required|in:delivery,return',
        ]);
        
        // Get booking
        if ($request->booking_type === 'product') {
            $booking = BookProduct::findOrFail($request->booking_id);
        } else {
            $booking = Book::findOrFail($request->booking_id);
        }
        
        // Assign courier
        $booking->id_courier = $request->courier_id;
        $booking->save();
        
        $courier = Courier::find($request->courier_id);
        $type = $request->assignment_type === 'delivery' ? 'pengiriman' : 'penjemputan';
        
        return redirect()
            ->route('officer.assign-courier.index', ['tab' => $request->assignment_type === 'delivery' ? 'delivery' : 'return'])
            ->with('success', "Courier {$courier->name} berhasil di-assign untuk {$type} booking {$booking->book_code}");
    }
}
