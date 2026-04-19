@php
    use App\Enums\OrderStatus;
    use App\Enums\ItemStatus;

    $orderBadgeClass = match ($booking->order_status) {
        OrderStatus::PENDING => 'bg-yellow-100 text-yellow-800',
        OrderStatus::DIPINJAM => 'bg-blue-100 text-blue-800',
        OrderStatus::SELESAI => 'bg-emerald-100 text-emerald-800',
        default => 'bg-gray-100 text-gray-800',
    };

    $itemBadgeClass = match ($booking->item_status) {
        ItemStatus::AVAILABLE => 'bg-green-100 text-green-800',
        ItemStatus::BOOKED => 'bg-yellow-100 text-yellow-800',
        ItemStatus::PACKING => 'bg-blue-100 text-blue-800',
        ItemStatus::PICKED_UP => 'bg-blue-100 text-blue-800',
        ItemStatus::DEPLOYED => 'bg-green-100 text-green-800',
        ItemStatus::RETURNING => 'bg-orange-100 text-orange-800',
        ItemStatus::IN_INSPECTION => 'bg-purple-100 text-purple-800',
        ItemStatus::MAINTENANCE => 'bg-red-100 text-red-800',
        ItemStatus::LOST_SCRAPPED => 'bg-red-200 text-red-900',
        default => 'bg-gray-100 text-gray-800',
    };
@endphp

<div class="space-y-4">
    <!-- Order Status -->
    <div>
        <h4 class="text-sm font-semibold text-gray-700 mb-2">Status Order</h4>
        <div class="flex flex-col items-start space-y-2">
            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $orderBadgeClass }}">
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
            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $itemBadgeClass }}">
                {{ $booking->item_status->label() }}
            </span>
            <p class="text-xs text-gray-600">{{ $booking->item_status->description() }}</p>
            <p class="text-xs font-medium text-gray-700">👤 Tanggung Jawab: {{ $booking->item_status->responsibleRole() }}</p>
        </div>
    </div>

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
