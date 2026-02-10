@php
    use App\Enums\OrderStatus;
    use App\Enums\ItemStatus;
@endphp

<div class="space-y-4">
    <!-- Order Status -->
    <div>
        <h4 class="text-sm font-semibold text-gray-700 mb-2">Status Order</h4>
        <div class="flex flex-col items-start space-y-2">
            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                @if($booking->order_status == OrderStatus::DRAFT) bg-gray-100 text-gray-800
                @elseif($booking->order_status == OrderStatus::AWAITING_VALIDATION) bg-yellow-100 text-yellow-800
                @elseif($booking->order_status == OrderStatus::CONFIRMED) bg-blue-100 text-blue-800
                @elseif(in_array($booking->order_status, [OrderStatus::READY_FOR_PICKUP, OrderStatus::OUT_FOR_DELIVERY, OrderStatus::DELIVERED])) bg-green-100 text-green-800
                @elseif($booking->order_status == OrderStatus::PICKUP_SCHEDULED) bg-purple-100 text-purple-800
                @elseif(in_array($booking->order_status, [OrderStatus::ON_PROCESS_RETURN, OrderStatus::PENDING_REVIEW])) bg-orange-100 text-orange-800
                @elseif($booking->order_status == OrderStatus::COMPLETED) bg-emerald-100 text-emerald-800
                @elseif($booking->order_status == OrderStatus::ISSUE_DETECTED) bg-red-100 text-red-800
                @elseif($booking->order_status == OrderStatus::CANCELLED) bg-gray-100 text-gray-600
                @endif">
                {{ $booking->order_status->label() }}
            </span>
            <p class="text-xs text-gray-600">{{ $booking->order_status->description() }}</p>
            <p class="text-xs font-medium text-gray-700">📍 Fase: {{ $booking->order_status->phase() }}</p>
        </div>
    </div>

    <!-- Item Status -->
    <div class="pt-3 border-t">
        <h4 class="text-sm font-semibold text-gray-700 mb-2">Status Barang</h4>
        <div class="flex flex-col items-start space-y-2">
            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                @if($booking->item_status == ItemStatus::AVAILABLE) bg-green-100 text-green-800
                @elseif($booking->item_status == ItemStatus::BOOKED) bg-yellow-100 text-yellow-800
                @elseif($booking->item_status == ItemStatus::PACKING) bg-blue-100 text-blue-800
                @elseif($booking->item_status == ItemStatus::PICKED_UP) bg-blue-100 text-blue-800
                @elseif($booking->item_status == ItemStatus::DEPLOYED) bg-green-100 text-green-800
                @elseif($booking->item_status == ItemStatus::RETURNING) bg-orange-100 text-orange-800
                @elseif($booking->item_status == ItemStatus::IN_INSPECTION) bg-purple-100 text-purple-800
                @elseif($booking->item_status == ItemStatus::MAINTENANCE) bg-red-100 text-red-800
                @elseif($booking->item_status == ItemStatus::LOST_SCRAPPED) bg-red-200 text-red-900
                @endif">
                {{ $booking->item_status->label() }}
            </span>
            <p class="text-xs text-gray-600">{{ $booking->item_status->description() }}</p>
            <p class="text-xs font-medium text-gray-700">👤 Tanggung Jawab: {{ $booking->item_status->responsibleRole() }}</p>
        </div>
    </div>

    <!-- Courier Info (jika ada) -->
    @if($booking->courier)
    <div class="pt-3 border-t">
        <h4 class="text-sm font-semibold text-gray-700 mb-2">Kurir</h4>
        <div class="bg-gray-50 p-3 rounded-lg">
            <p class="text-sm font-medium text-gray-900">{{ $booking->courier->name }}</p>
            <p class="text-sm text-gray-600">📱 {{ $booking->courier->phone }}</p>
        </div>
    </div>
    @endif

    <!-- Status Timeline -->
    <div class="pt-3 border-t">
        <h4 class="text-sm font-semibold text-gray-700 mb-3">Timeline</h4>
        <div class="space-y-2 text-xs text-gray-600">
            @if($booking->created_at)
            <div class="flex items-start space-x-2">
                <span class="text-gray-400">📅</span>
                <div>
                    <p class="font-medium text-gray-700">Booking Dibuat</p>
                    <p>{{ $booking->created_at->format('d M Y H:i') }}</p>
                </div>
            </div>
            @endif
            
            @if($booking->delivery_at)
            <div class="flex items-start space-x-2">
                <span class="text-gray-400">🚚</span>
                <div>
                    <p class="font-medium text-gray-700">Pengiriman</p>
                    <p>{{ $booking->delivery_at->format('d M Y H:i') }}</p>
                </div>
            </div>
            @endif
            
            @if($booking->returned_at)
            <div class="flex items-start space-x-2">
                <span class="text-gray-400">↩️</span>
                <div>
                    <p class="font-medium text-gray-700">Dikembalikan</p>
                    <p>{{ $booking->returned_at->format('d M Y H:i') }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
