<?php

namespace App\Http\Controllers;

use App\Models\BookProduct;
use App\Models\Book;
use App\Enums\OrderStatus;
use App\Enums\ItemStatus;
use App\Services\ItemStatusTransitionService;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

/**
 * Courier Delivery Management Controller
 */
class CourierDeliveryController extends Controller
{
    protected ItemStatusTransitionService $transitionService;

    public function __construct(ItemStatusTransitionService $transitionService)
    {
        $this->transitionService = $transitionService;
    }

    /**
     * Tampilkan dashboard kurir dengan delivery & return tasks
     */
    public function index(): View
    {
        $courier = auth()->user();

        // Delivery Tasks (Pengiriman)
        $deliveryBookings = collect();
        $activeDeliveries = collect();
        if ($courier) {
            $deliveryBookings = BookProduct::where('id_courier', $courier->id)
                ->whereIn('order_status', [
                    OrderStatus::READY_FOR_PICKUP,
                    OrderStatus::OUT_FOR_DELIVERY
                ])
                ->latest()
                ->get();

            $deliveryBooksBookings = Book::where('id_courier', $courier->id)
                ->whereIn('order_status', [
                    OrderStatus::READY_FOR_PICKUP,
                    OrderStatus::OUT_FOR_DELIVERY
                ])
                ->latest()
                ->get();

            $deliveryBookings = $deliveryBookings->concat($deliveryBooksBookings)->sortByDesc('created_at');
            $activeDeliveries = $deliveryBookings->take(5);
        }

        // Return Tasks (Pengembalian)
        $returnBookings = collect();
        $activeReturns = collect();
        if ($courier) {
            $returnBookings = BookProduct::where('id_courier', $courier->id)
                ->whereIn('order_status', [
                    OrderStatus::PICKUP_SCHEDULED,
                    OrderStatus::ON_PROCESS_RETURN
                ])
                ->latest()
                ->get();

            $returnBooksBookings = Book::where('id_courier', $courier->id)
                ->whereIn('order_status', [
                    OrderStatus::PICKUP_SCHEDULED,
                    OrderStatus::ON_PROCESS_RETURN
                ])
                ->latest()
                ->get();

            $returnBookings = $returnBookings->concat($returnBooksBookings)->sortByDesc('created_at');
            $activeReturns = $returnBookings->take(5);
        }

        // Recent Completed
        $recentCompleted = collect();
        if ($courier) {
            $completedBookProducts = BookProduct::where('id_courier', $courier->id)
                ->whereIn('order_status', [OrderStatus::COMPLETED])
                ->latest()
                ->limit(5)
                ->get();

            $completedBooks = Book::where('id_courier', $courier->id)
                ->whereIn('order_status', [OrderStatus::COMPLETED])
                ->latest()
                ->limit(5)
                ->get();

            $recentCompleted = $completedBookProducts->concat($completedBooks)->sortByDesc('created_at')->take(5);
        }

        $readyForPickupCount = BookProduct::where('id_courier', $courier?->id)
            ->where('order_status', OrderStatus::READY_FOR_PICKUP)
            ->count() + Book::where('id_courier', $courier?->id)
            ->where('order_status', OrderStatus::READY_FOR_PICKUP)
            ->count();

        return view('courier.delivery-management', [
            'deliveryBookings' => $deliveryBookings,
            'readyForPickup' => BookProduct::where('id_courier', $courier?->id)
                ->where('order_status', OrderStatus::READY_FOR_PICKUP)
                ->count() + Book::where('id_courier', $courier?->id)
                ->where('order_status', OrderStatus::READY_FOR_PICKUP)
                ->count(),
            'outForDelivery' => BookProduct::where('id_courier', $courier?->id)
                ->where('order_status', OrderStatus::OUT_FOR_DELIVERY)
                ->count() + Book::where('id_courier', $courier?->id)
                ->where('order_status', OrderStatus::OUT_FOR_DELIVERY)
                ->count(),
            'readyForPickupCount' => $readyForPickupCount,
        ]);
    }

    public function dashboard(): View 
    {
        $courier = auth()->user();
        $courierId = $courier?->id;

        // Delivery Tasks (Pengiriman)
        $deliveryBookings = collect();
        $activeDeliveries = collect();
        if ($courierId) {
            $deliveryBookings = BookProduct::where('id_courier', $courierId)
                ->whereIn('order_status', [OrderStatus::READY_FOR_PICKUP, OrderStatus::OUT_FOR_DELIVERY])
                ->latest()->get();

            $deliveryBooksBookings = Book::where('id_courier', $courierId)
                ->whereIn('order_status', [OrderStatus::READY_FOR_PICKUP, OrderStatus::OUT_FOR_DELIVERY])
                ->latest()->get();

            $deliveryBookings = $deliveryBookings->concat($deliveryBooksBookings)->sortByDesc('created_at');
            $activeDeliveries = $deliveryBookings->take(5);
        }

        // Return Tasks (Pengembalian)
        $returnBookings = collect();
        $activeReturns = collect();
        if ($courierId) {
            $returnBookings = BookProduct::where('id_courier', $courierId)
                ->whereIn('order_status', [OrderStatus::PICKUP_SCHEDULED, OrderStatus::ON_PROCESS_RETURN])
                ->latest()->get();

            $returnBooksBookings = Book::where('id_courier', $courierId)
                ->whereIn('order_status', [OrderStatus::PICKUP_SCHEDULED, OrderStatus::ON_PROCESS_RETURN])
                ->latest()->get();

            $returnBookings = $returnBookings->concat($returnBooksBookings)->sortByDesc('created_at');
            $activeReturns = $returnBookings->take(5);
        }

        // Recent Completed
        $recentCompleted = collect();
        if ($courierId) {
            $completedBookProducts = BookProduct::where('id_courier', $courierId)
                ->where('order_status', OrderStatus::COMPLETED)->latest()->limit(5)->get();
            $completedBooks = Book::where('id_courier', $courierId)
                ->where('order_status', OrderStatus::COMPLETED)->latest()->limit(5)->get();
            $recentCompleted = $completedBookProducts->concat($completedBooks)
                ->sortByDesc('created_at')->take(5);
        }

        // 📊 Stats Counts - Dynamic
        if ($courierId) {
            $activeDeliveriesCount = BookProduct::where('id_courier', $courierId)
                    ->whereIn('order_status', [OrderStatus::READY_FOR_PICKUP, OrderStatus::OUT_FOR_DELIVERY])->count()
                + Book::where('id_courier', $courierId)
                    ->whereIn('order_status', [OrderStatus::READY_FOR_PICKUP, OrderStatus::OUT_FOR_DELIVERY])->count();

            $pendingPickupsCount = BookProduct::where('id_courier', $courierId)
                    ->where('order_status', OrderStatus::READY_FOR_PICKUP)->count()
                + Book::where('id_courier', $courierId)
                    ->where('order_status', OrderStatus::READY_FOR_PICKUP)->count();

            $completedTodayCount = BookProduct::where('id_courier', $courierId)
                    ->where('order_status', OrderStatus::COMPLETED)
                    ->whereDate('updated_at', today())->count()
                + Book::where('id_courier', $courierId)
                    ->where('order_status', OrderStatus::COMPLETED)
                    ->whereDate('updated_at', today())->count();

            $returnsCount = BookProduct::where('id_courier', $courierId)
                    ->whereIn('order_status', [OrderStatus::PICKUP_SCHEDULED, OrderStatus::ON_PROCESS_RETURN])->count()
                + Book::where('id_courier', $courierId)
                    ->whereIn('order_status', [OrderStatus::PICKUP_SCHEDULED, OrderStatus::ON_PROCESS_RETURN])->count();
        } else {
            $activeDeliveriesCount = $pendingPickupsCount = $completedTodayCount = $returnsCount = 0;
        }

        return view('courier.dashboard', [
            'deliveryBookings' => $deliveryBookings,
            'activeDeliveries' => $activeDeliveries,
            'activeReturns' => $activeReturns,
            'recentCompleted' => $recentCompleted,
            // 📊 Pass counts to view
            'activeDeliveriesCount' => $activeDeliveriesCount,
            'pendingPickupsCount' => $pendingPickupsCount,
            'completedTodayCount' => $completedTodayCount,
            'returnsCount' => $returnsCount,
        ]);
    }

    /**
     * Tampilkan halaman pengembalian
     */
    public function returns(): View
    {
        $courier = auth()->user();

        // Return Tasks (Pengembalian)
        $returnBookings = collect();
        if ($courier) {
            $returnBookProducts = BookProduct::where('id_courier', $courier->id)
                ->whereIn('order_status', [
                    OrderStatus::PICKUP_SCHEDULED,
                    OrderStatus::ON_PROCESS_RETURN
                ])
                ->latest()
                ->get();

            $returnBooks = Book::where('id_courier', $courier->id)
                ->whereIn('order_status', [
                    OrderStatus::PICKUP_SCHEDULED,
                    OrderStatus::ON_PROCESS_RETURN
                ])
                ->latest()
                ->get();

            $returnBookings = $returnBookProducts->concat($returnBooks)->sortByDesc('created_at');
        }

        // Count stats
        $pickupScheduled = BookProduct::where('id_courier', $courier?->id)
            ->where('order_status', OrderStatus::PICKUP_SCHEDULED)
            ->count() + Book::where('id_courier', $courier?->id)
            ->where('order_status', OrderStatus::PICKUP_SCHEDULED)
            ->count();

        $onProcessReturn = BookProduct::where('id_courier', $courier?->id)
            ->where('order_status', OrderStatus::ON_PROCESS_RETURN)
            ->count() + Book::where('id_courier', $courier?->id)
            ->where('order_status', OrderStatus::ON_PROCESS_RETURN)
            ->count();

        $readyForPickupCount = BookProduct::where('id_courier', $courier?->id)
            ->where('order_status', OrderStatus::READY_FOR_PICKUP)
            ->count() + Book::where('id_courier', $courier?->id)
            ->where('order_status', OrderStatus::READY_FOR_PICKUP)
            ->count();

        return view('courier.returns-management', [
            'returnBookings' => $returnBookings,
            'pickupScheduled' => $pickupScheduled,
            'onProcessReturn' => $onProcessReturn,
            'readyForPickupCount' => $readyForPickupCount,
        ]);
    }

    /**
     * Tampilkan detail delivery
     */
    public function show(string $type, string $id): View
    {
        $courier = auth()->user();
        
        // Resolve booking berdasarkan type
        if ($type === 'product') {
            $booking = BookProduct::findOrFail($id);
        } elseif ($type === 'package') {
            $booking = Book::findOrFail($id);
        } else {
            abort(404, 'Invalid booking type');
        }
        
        // Validasi bahwa booking ini adalah milik courier
        if ($booking->id_courier !== $courier?->id) {
            abort(403, 'Unauthorized');
        }

        return view('courier.delivery-detail', [
            'booking' => $booking,
            'type' => $type,
        ]);
    }

    /**
     * Tampilkan history pengiriman
     */
    public function history(): View
    {
        $courier = auth()->user();
        $filter = request('filter', 'all');
        $search = request('search');

        $bookProducts = collect();
        $books = collect();

        // Build base queries
        $bookProductQuery = BookProduct::where('id_courier', $courier?->id);
        $bookQuery = Book::where('id_courier', $courier?->id);

        // Apply status filter
        if ($filter === 'all') {
            $bookProductQuery->whereIn('order_status', [
                OrderStatus::DELIVERED,
                OrderStatus::PENDING_REVIEW,
                OrderStatus::COMPLETED,
                OrderStatus::ISSUE_DETECTED
            ]);
            $bookQuery->whereIn('order_status', [
                OrderStatus::DELIVERED,
                OrderStatus::PENDING_REVIEW,
                OrderStatus::COMPLETED,
                OrderStatus::ISSUE_DETECTED
            ]);
        } elseif ($filter === 'delivered') {
            $bookProductQuery->where('order_status', OrderStatus::DELIVERED);
            $bookQuery->where('order_status', OrderStatus::DELIVERED);
        } elseif ($filter === 'returned') {
            $bookProductQuery->where('order_status', OrderStatus::PENDING_REVIEW);
            $bookQuery->where('order_status', OrderStatus::PENDING_REVIEW);
        } elseif ($filter === 'completed') {
            $bookProductQuery->where('order_status', OrderStatus::COMPLETED);
            $bookQuery->where('order_status', OrderStatus::COMPLETED);
        } elseif ($filter === 'issue') {
            $bookProductQuery->where('order_status', OrderStatus::ISSUE_DETECTED);
            $bookQuery->where('order_status', OrderStatus::ISSUE_DETECTED);
        }

        // Apply search filter
        if ($search) {
            $bookProductQuery->where(function($q) use ($search) {
                $q->where('book_code', 'like', '%' . $search . '%')
                  ->orWhere('booker_name', 'like', '%' . $search . '%')
                  ->orWhereHas('product', function($q) use ($search) {
                      $q->where('name', 'like', '%' . $search . '%');
                  });
            });

            $bookQuery->where(function($q) use ($search) {
                $q->where('book_code', 'like', '%' . $search . '%')
                  ->orWhere('booker_name', 'like', '%' . $search . '%')
                  ->orWhereHas('package', function($q) use ($search) {
                      $q->where('name_package', 'like', '%' . $search . '%');
                  });
            });
        }

        $bookProducts = $bookProductQuery->latest()->get();
        $books = $bookQuery->latest()->get();

        // Merge and sort bookings
        $bookings = $bookProducts->concat($books)->sortByDesc('created_at');

        $readyForPickupCount = BookProduct::where('id_courier', $courier?->id)
            ->where('order_status', OrderStatus::READY_FOR_PICKUP)
            ->count() + Book::where('id_courier', $courier?->id)
            ->where('order_status', OrderStatus::READY_FOR_PICKUP)
            ->count();

        return view('courier.delivery-history', [
            'bookings' => $bookings,
            'readyForPickupCount' => $readyForPickupCount,
        ]);
    }

    /**
     * Handle pickup delivery for BookProduct
     */
    public function pickupDelivery($id)
    {
        try {
            $booking = BookProduct::findOrFail($id);
            
            if ($booking->id_courier !== auth()->user()?->id) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }

            // Use transition service with validation
            $this->transitionService->transitionItemStatus(
                $booking,
                ItemStatus::PICKED_UP,
                ['courier_id' => auth()->user()->id]
            );

            // Sync order status
            $this->transitionService->syncOrderStatus($booking, ItemStatus::PICKED_UP);

            // Save photo if provided
            if (request()->hasFile('photo')) {
                $photo = request()->file('photo');
                $path = $photo->store('courier/pickup', 'public');
                $booking->pickup_photo = $path;
                $booking->save();
            }

            return response()->json(['success' => true, 'message' => 'Barang berhasil diambil untuk pengiriman']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Handle complete delivery for BookProduct
     */
    public function completeDelivery($id)
    {
        try {
            $booking = BookProduct::findOrFail($id);
            
            if ($booking->id_courier !== auth()->user()?->id) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }

            // Use transition service with validation
            $this->transitionService->transitionItemStatus(
                $booking,
                ItemStatus::DEPLOYED
            );

            // Sync order status
            $this->transitionService->syncOrderStatus($booking, ItemStatus::DEPLOYED);

            // Save photo if provided
            if (request()->hasFile('photo')) {
                $photo = request()->file('photo');
                $path = $photo->store('courier/delivery', 'public');
                $booking->delivery_photo = $path;
                $booking->save();
            }

            return response()->json(['success' => true, 'message' => 'Pengiriman berhasil diselesaikan']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Handle pickup return for BookProduct
     */
    public function pickupReturn($id)
    {
        try {
            $booking = BookProduct::findOrFail($id);
            
            if ($booking->id_courier !== auth()->user()?->id) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }

            if (request()->hasFile('photo')) {
                $photo = request()->file('photo');
                $path = $photo->store('courier/return-pickup', 'public');
                $booking->return_pickup_photo = $path;
            }

            $this->transitionService->transitionItemStatus($booking, ItemStatus::RETURNING);
            $this->transitionService->syncOrderStatus($booking, ItemStatus::RETURNING);

            return response()->json(['success' => true, 'message' => 'Barang berhasil diambil untuk pengembalian']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Handle complete return for BookProduct
     */
    public function completeReturn($id)
    {
        try {
            $booking = BookProduct::findOrFail($id);
            
            if ($booking->id_courier !== auth()->user()?->id) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }

            if (request()->hasFile('photo')) {
                $photo = request()->file('photo');
                $path = $photo->store('courier/return-complete', 'public');
                $booking->return_complete_photo = $path;
            }

            $this->transitionService->transitionItemStatus($booking, ItemStatus::IN_INSPECTION);
            $this->transitionService->syncOrderStatus($booking, ItemStatus::IN_INSPECTION);
            $booking->returned_at = now();
            $booking->save();

            return response()->json(['success' => true, 'message' => 'Pengembalian berhasil diselesaikan']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Package (Book) versions of the same methods
     */
    public function pickupDeliveryPackage($id)  
    {
        try {
            $booking = Book::findOrFail($id);
            
            if ($booking->id_courier !== auth()->user()?->id) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }

            if (request()->hasFile('photo')) {
                $photo = request()->file('photo');
                $path = $photo->store('courier/pickup', 'public');
                $booking->pickup_photo = $path;
            }

            $this->transitionService->transitionItemStatus($booking, ItemStatus::PICKED_UP);
            $this->transitionService->syncOrderStatus($booking, ItemStatus::PICKED_UP);

            return response()->json(['success' => true, 'message' => 'Pengiriman berhasil diambil kurir']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function completeDeliveryPackage($id)
    {
        try {
            $booking = Book::findOrFail($id);
            
            if ($booking->id_courier !== auth()->user()?->id) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Unauthorized'
                ], 403);
            }

            // Kalau mau konsisten dengan product, bisa pakai transition service juga
            $this->transitionService->transitionItemStatus(
                $booking,
                ItemStatus::DEPLOYED
            );

            $this->transitionService->syncOrderStatus(
                $booking, 
                ItemStatus::DEPLOYED
            );

            // Save photo
            if (request()->hasFile('photo')) {
                $photo = request()->file('photo');
                $path = $photo->store('courier/delivery', 'public');
                $booking->delivery_photo = $path;
                $booking->save();
            }

            return response()->json([
                'success' => true, 
                'message' => 'Pengiriman package berhasil diselesaikan'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function pickupReturnPackage($id)
    {
        try {
            $booking = Book::findOrFail($id);
            
            if ($booking->id_courier !== auth()->user()?->id) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }

            if (request()->hasFile('photo')) {
                $photo = request()->file('photo');
                $path = $photo->store('courier/return-pickup', 'public');
                $booking->return_pickup_photo = $path;
            }

            $this->transitionService->transitionItemStatus($booking, ItemStatus::RETURNING);
            $this->transitionService->syncOrderStatus($booking, ItemStatus::RETURNING);

            return response()->json(['success' => true, 'message' => 'Pengambilan barang untuk pengembalian berhasil dimulai']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Tampilkan halaman Route Map untuk batching deliveries
     */
    public function routeMap(): View
    {
        $courier = auth()->user();
        
        return view('courier.route-map', compact('courier'));
    }

    /**
     * Ambil data untuk route map (deliveries & returns grouped by area)
     */
    public function routeMapData(): JsonResponse
    {
        $courier = auth()->user();
        
        if (!$courier) {
            return response()->json(['error' => 'Courier not found'], 404);
        }

        // Get Outbound Deliveries (Ready for Pickup + Out for Delivery)
        $deliveries = BookProduct::where('id_courier', $courier->id)
            ->whereIn('order_status', [
                OrderStatus::READY_FOR_PICKUP,
                OrderStatus::OUT_FOR_DELIVERY
            ])
            ->with(['detailBookProduct.product', 'detailBookProduct.user'])
            ->get()
            ->map(function($booking) {
                $detail = $booking->detailBookProduct;
                return [
                    'id' => $booking->id,
                    'type' => 'delivery',
                    'booking_type' => 'product',
                    'booking_code' => $booking->booking_code,
                    'status' => $booking->order_status->value,
                    'status_label' => $booking->order_status->getLabel(),
                    'customer_name' => $detail->user->name ?? 'N/A',
                    'customer_phone' => $detail->user->phone ?? 'N/A',
                    'address' => $detail->renter_address ?? 'Alamat tidak tersedia',
                    'item_name' => $detail->product->name ?? 'N/A',
                    'shipping_date' => $detail->shipping_date ?? null,
                    'rental_start' => $detail->rental_start_at ?? null,
                    'priority' => $booking->order_status === OrderStatus::OUT_FOR_DELIVERY ? 'high' : 'normal'
                ];
            });

        // Get Outbound Deliveries from Books
        $deliveriesBooks = Book::where('id_courier', $courier->id)
            ->whereIn('order_status', [
                OrderStatus::READY_FOR_PICKUP,
                OrderStatus::OUT_FOR_DELIVERY
            ])
            ->with(['detailBook.user'])
            ->get()
            ->map(function($booking) {
                $detail = $booking->detailBook;
                return [
                    'id' => $booking->id,
                    'type' => 'delivery',
                    'booking_type' => 'package',
                    'booking_code' => $booking->booking_code,
                    'status' => $booking->order_status->value,
                    'status_label' => $booking->order_status->getLabel(),
                    'customer_name' => $detail->user->name ?? 'N/A',
                    'customer_phone' => $detail->user->phone ?? 'N/A',
                    'address' => $detail->renter_address ?? 'Alamat tidak tersedia',
                    'item_name' => 'Paket - ' . ($booking->booking_code ?? 'N/A'),
                    'shipping_date' => $detail->shipping_date ?? null,
                    'rental_start' => $detail->rental_start_at ?? null,
                    'priority' => $booking->order_status === OrderStatus::OUT_FOR_DELIVERY ? 'high' : 'normal'
                ];
            });

        // Get Inbound Returns (Pickup Scheduled + On Process Return)
        $returns = BookProduct::where('id_courier', $courier->id)
            ->whereIn('order_status', [
                OrderStatus::PICKUP_SCHEDULED,
                OrderStatus::ON_PROCESS_RETURN
            ])
            ->with(['detailBookProduct.product', 'detailBookProduct.user'])
            ->get()
            ->map(function($booking) {
                $detail = $booking->detailBookProduct;
                return [
                    'id' => $booking->id,
                    'type' => 'return',
                    'booking_type' => 'product',
                    'booking_code' => $booking->booking_code,
                    'status' => $booking->order_status->value,
                    'status_label' => $booking->order_status->getLabel(),
                    'customer_name' => $detail->user->name ?? 'N/A',
                    'customer_phone' => $detail->user->phone ?? 'N/A',
                    'address' => $detail->renter_address ?? 'Alamat tidak tersedia',
                    'item_name' => $detail->product->name ?? 'N/A',
                    'checkout_end' => $detail->checkout_appointment_end ?? null,
                    'priority' => $booking->order_status === OrderStatus::ON_PROCESS_RETURN ? 'high' : 'normal'
                ];
            });

        // Get Inbound Returns from Books
        $returnsBooks = Book::where('id_courier', $courier->id)
            ->whereIn('order_status', [
                OrderStatus::PICKUP_SCHEDULED,
                OrderStatus::ON_PROCESS_RETURN
            ])
            ->with(['detailBook.user'])
            ->get()
            ->map(function($booking) {
                $detail = $booking->detailBook;
                return [
                    'id' => $booking->id,
                    'type' => 'return',
                    'booking_type' => 'package',
                    'booking_code' => $booking->booking_code,
                    'status' => $booking->order_status->value,
                    'status_label' => $booking->order_status->getLabel(),
                    'customer_name' => $detail->user->name ?? 'N/A',
                    'customer_phone' => $detail->user->phone ?? 'N/A',
                    'address' => $detail->renter_address ?? 'Alamat tidak tersedia',
                    'item_name' => 'Paket - ' . ($booking->booking_code ?? 'N/A'),
                    'checkout_end' => $detail->checkout_appointment_end ?? null,
                    'priority' => $booking->order_status === OrderStatus::ON_PROCESS_RETURN ? 'high' : 'normal'
                ];
            });

        // Merge all collections
        $allTasks = $deliveries
            ->concat($deliveriesBooks)
            ->concat($returns)
            ->concat($returnsBooks);

        // Group by address/area (simple grouping by exact address match)
        $grouped = $allTasks->groupBy('address')->map(function($group, $address) {
            return [
                'address' => $address,
                'count' => $group->count(),
                'tasks' => $group->values(),
                'has_delivery' => $group->where('type', 'delivery')->isNotEmpty(),
                'has_return' => $group->where('type', 'return')->isNotEmpty(),
            ];
        })->values();

        return response()->json([
            'total_deliveries' => $deliveries->count() + $deliveriesBooks->count(),
            'total_returns' => $returns->count() + $returnsBooks->count(),
            'total_tasks' => $allTasks->count(),
            'grouped_by_area' => $grouped,
            'all_tasks' => $allTasks->values()
        ]);
    }
}
