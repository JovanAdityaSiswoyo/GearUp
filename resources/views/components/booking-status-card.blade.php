@php
    use App\Enums\OrderStatus;
    use App\Enums\ItemStatus;

    $hasDetectedIssue = !empty($booking->issue_condition) || !empty($booking->issue_notes) || !empty($booking->issue_photo);

    $orderBadgeClass = $hasDetectedIssue
        ? 'bg-red-100 text-red-800'
        : match ($booking->order_status) {
            OrderStatus::PENDING => 'bg-yellow-100 text-yellow-800',
            OrderStatus::DIPINJAM => 'bg-blue-100 text-blue-800',
            OrderStatus::SELESAI => 'bg-emerald-100 text-emerald-800',
            default => 'bg-gray-100 text-gray-800',
        };

    $displayOrderLabel = $hasDetectedIssue ? 'Masalah Terdeteksi' : $booking->order_status->label();
    $displayOrderDescription = $hasDetectedIssue
        ? 'Barang terdeteksi bermasalah saat pengembalian dan menunggu penyelesaian denda/tindak lanjut.'
        : $booking->order_status->description();
    $displayOrderPhase = $hasDetectedIssue ? 'Fase Inspeksi Masalah' : $booking->order_status->phase();

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
                {{ $displayOrderLabel }}
            </span>
            <p class="text-xs text-gray-600">{{ $displayOrderDescription }}</p>
            <p class="text-xs font-medium text-gray-700">📍 Fase: {{ $displayOrderPhase }}</p>
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

    @if(!empty($booking->pickup_photo) || !empty($booking->return_photo) || !empty($booking->issue_photo) || !empty($booking->issue_notes))
        <div class="pt-3 border-t">
            <h4 class="text-sm font-semibold text-gray-700 mb-3">Dokumentasi</h4>
            <div class="space-y-3 text-xs text-gray-600">
                @if(!empty($booking->pickup_photo))
                    <div>
                        <p class="font-medium text-gray-700 mb-2">Foto Serah Terima</p>
                        <a href="{{ asset('storage/' . $booking->pickup_photo) }}" target="_blank" rel="noopener noreferrer">
                            <img src="{{ asset('storage/' . $booking->pickup_photo) }}" alt="Foto serah terima" class="w-full max-w-xs rounded-lg border border-gray-200 object-cover">
                        </a>
                    </div>
                @endif

                @if(!empty($booking->return_photo))
                    <div>
                        <p class="font-medium text-gray-700 mb-2">Foto Selesai Pinjam</p>
                        <a href="{{ asset('storage/' . $booking->return_photo) }}" target="_blank" rel="noopener noreferrer">
                            <img src="{{ asset('storage/' . $booking->return_photo) }}" alt="Foto selesai pinjam" class="w-full max-w-xs rounded-lg border border-gray-200 object-cover">
                        </a>
                    </div>
                @endif

                @if(!empty($booking->issue_photo))
                    <div>
                        <p class="font-medium text-gray-700 mb-2">Foto Masalah</p>
                        <a href="{{ asset('storage/' . $booking->issue_photo) }}" target="_blank" rel="noopener noreferrer">
                            <img src="{{ asset('storage/' . $booking->issue_photo) }}" alt="Foto masalah" class="w-full max-w-xs rounded-lg border border-gray-200 object-cover">
                        </a>
                    </div>
                @endif

                @if(!empty($booking->issue_notes))
                    <div class="bg-red-50 border border-red-100 rounded-lg p-3 text-red-800">
                        <p class="font-semibold mb-1">Catatan Masalah</p>
                        <p class="leading-relaxed">{{ $booking->issue_notes }}</p>
                    </div>
                @endif

                @if(!empty($booking->issue_condition) || !empty($booking->fine_amount))
                    @php
                        $conditionLabels = [
                            'rusak_ringan' => 'Rusak Ringan',
                            'rusak_sedang' => 'Rusak Sedang',
                            'rusak_berat' => 'Rusak Berat',
                            'hilang' => 'Hilang',
                        ];
                    @endphp
                    <div class="bg-amber-50 border border-amber-100 rounded-lg p-3 text-amber-900">
                        <p class="font-semibold mb-1">Denda Kerusakan</p>
                        @if(!empty($booking->issue_condition))
                            <p class="leading-relaxed text-xs">Kondisi: {{ $conditionLabels[$booking->issue_condition] ?? $booking->issue_condition }}</p>
                        @endif
                        @if(!empty($booking->fine_percentage))
                            <p class="leading-relaxed text-xs">Persentase: {{ $booking->fine_percentage }}%</p>
                        @endif
                        @if(!empty($booking->fine_amount))
                            <p class="leading-relaxed text-xs font-semibold">Nominal: Rp {{ number_format($booking->fine_amount / 100, 0, ',', '.') }}</p>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>
