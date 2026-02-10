@extends('layouts.courier')

@section('title', 'Pengembalian')

@section('content')
<div class="min-h-screen bg-gray-50 py-8 px-4 lg:px-16">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center space-x-3 mb-2">
                <x-heroicon-o-arrow-uturn-left class="h-8 w-8 text-purple-600" />
                <h1 class="text-3xl font-bold text-gray-900">Pengembalian</h1>
            </div>
            <p class="text-gray-600">Kelola pengembalian barang dari pelanggan</p>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm">Pengembalian Dijadwalkan</p>
                        <p class="text-3xl font-bold text-orange-600">{{ $pickupScheduled }}</p>
                    </div>
                    <x-heroicon-o-calendar class="h-8 w-8 text-orange-400" />
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm">Sedang Dikembalikan</p>
                        <p class="text-3xl font-bold text-purple-600">{{ $onProcessReturn }}</p>
                    </div>
                    <x-heroicon-o-arrow-uturn-left class="h-8 w-8 text-purple-400" />
                </div>
            </div>
        </div>

        <!-- Return Tasks -->
        <div class="mb-12">
            <h2 class="text-2xl font-bold text-purple-700 mb-4">🔄 Pengembalian</h2>
            <div class="space-y-4">
                @forelse($returnBookings as $booking)
                <div class="bg-white rounded-lg shadow hover:shadow-lg transition border-l-4 border-purple-500">
                    <div class="p-6">
                        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                            <!-- Info -->
                            <div class="lg:col-span-2">
                                <div class="flex items-start space-x-4">
                                    @if($booking->product && $booking->product->image)
                                        <img src="{{ asset('storage/' . $booking->product->image) }}" alt="{{ $booking->product->name }}" class="w-16 h-16 rounded-lg object-cover">
                                    @elseif($booking->package && $booking->package->image)
                                        <img src="{{ asset('storage/' . $booking->package->image) }}" alt="{{ $booking->package->name_package }}" class="w-16 h-16 rounded-lg object-cover">
                                    @else
                                        <div class="w-16 h-16 rounded-lg bg-gray-200 flex items-center justify-center">
                                            <x-heroicon-o-photo class="h-6 w-6 text-gray-400" />
                                        </div>
                                    @endif
                                    <div class="flex-1">
                                        <h3 class="text-lg font-bold text-gray-900">
                                            {{ $booking->product->name ?? $booking->package->name_package ?? 'Barang Dihapus' }}
                                        </h3>
                                        <p class="text-sm text-gray-600">Kode: <span class="font-mono font-semibold text-purple-600">{{ $booking->book_code }}</span></p>
                                        <p class="text-sm text-gray-600">📍 {{ $booking->booker_name }}</p>
                                        <p class="text-sm text-gray-600">📱 {{ $booking->booker_telp }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Return Info -->
                            <div>
                                <h4 class="text-xs font-semibold text-gray-700 mb-2 uppercase">Info Pengembalian</h4>
                                <div class="space-y-1 text-sm">
                                    <p class="text-gray-600">Tanggal Pengambilan:</p>
                                    <p class="font-semibold">{{ $booking->checkin_appointment_start->format('d M Y') }}</p>
                                    <p class="text-gray-600 mt-2">Tanggal Pengembalian:</p>
                                    <p class="font-semibold">{{ $booking->checkout_appointment_end->format('d M Y') }}</p>
                                </div>
                            </div>

                            <!-- Status & Actions -->
                            <div class="flex flex-col justify-between">
                                <div>
                                    <h4 class="text-xs font-semibold text-gray-700 mb-2 uppercase">Status</h4>
                                    @if($booking->order_status === \App\Enums\OrderStatus::PICKUP_SCHEDULED)
                                        <span class="px-3 py-1 bg-orange-100 text-orange-700 rounded-full text-sm font-semibold inline-block">
                                            Dijadwalkan
                                        </span>
                                    @elseif($booking->order_status === \App\Enums\OrderStatus::ON_PROCESS_RETURN)
                                        <span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-sm font-semibold inline-block">
                                            Dalam Proses
                                        </span>
                                    @endif
                                </div>

                                <!-- Action Buttons -->
                                <div class="mt-4 flex flex-col space-y-2">
                                    <a href="{{ route('courier.deliveries.show', ['type' => $booking instanceof \App\Models\BookProduct ? 'product' : 'book', 'id' => $booking->id]) }}" 
                                       class="inline-flex items-center justify-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition text-sm font-semibold">
                                        <x-heroicon-o-eye class="h-4 w-4 mr-2" />
                                        Lihat Detail
                                    </a>

                                    @if($booking->order_status === \App\Enums\OrderStatus::PICKUP_SCHEDULED)
                                        <button onclick="startReturn({{ $booking->id }}, '{{ $booking instanceof \App\Models\BookProduct ? 'product' : 'book' }}')" 
                                                class="inline-flex items-center justify-center px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition text-sm font-semibold">
                                            <x-heroicon-o-truck class="h-4 w-4 mr-2" />
                                            Mulai Ambil
                                        </button>
                                    @elseif($booking->order_status === \App\Enums\OrderStatus::ON_PROCESS_RETURN)
                                        <button onclick="completeReturn({{ $booking->id }}, '{{ $booking instanceof \App\Models\BookProduct ? 'product' : 'book' }}')" 
                                                class="inline-flex items-center justify-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-sm font-semibold">
                                            <x-heroicon-o-check class="h-4 w-4 mr-2" />
                                            Selesaikan
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="bg-white rounded-lg shadow p-12 text-center">
                    <x-heroicon-o-inbox class="h-16 w-16 text-gray-300 mx-auto mb-4" />
                    <p class="text-gray-500 text-lg">Tidak ada pengembalian saat ini</p>
                    <p class="text-gray-400 text-sm">Pengembalian akan muncul di sini ketika ada barang yang perlu diambil</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function startReturn(bookingId, type) {
    if(confirm('Mulai proses pengambilan barang untuk pengembalian?')) {
        // TODO: Implement API call to update status to ON_PROCESS_RETURN
        console.log('Start return:', bookingId, type);
    }
}

function completeReturn(bookingId, type) {
    if(confirm('Tandai pengembalian ini sebagai selesai?')) {
        // TODO: Implement API call to update status to COMPLETED
        console.log('Complete return:', bookingId, type);
    }
}
</script>
@endpush
@endsection
