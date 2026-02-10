@extends('layouts.courier')

@section('title', 'History Pengiriman')

@section('content')
<div class="min-h-screen bg-gray-50 py-8 px-4 lg:px-16">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center space-x-3 mb-2">
                <x-heroicon-o-clock class="h-8 w-8 text-blue-600" />
                <h1 class="text-3xl font-bold text-gray-900">History</h1>
            </div>
            <p class="text-gray-600">Lihat semua pengiriman dan pengembalian yang sudah selesai</p>
        </div>

        <!-- Filter & Search -->
        <div class="mb-6 bg-white rounded-lg shadow-sm p-4">
            <form method="GET" action="{{ route('courier.deliveries.history') }}" class="flex flex-col md:flex-row gap-4">
                <!-- Search Box -->
                <div class="flex-1">
                    <div class="relative">
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}" 
                               placeholder="Cari kode booking, nama user, atau nama barang..."
                               class="w-full px-4 py-2 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <x-heroicon-o-magnifying-glass class="absolute left-3 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400" />
                    </div>
                </div>

                <!-- Filter Dropdown -->
                <div class="md:w-64">
                    <select name="filter" 
                            onchange="this.form.submit()"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="all" {{ request('filter', 'all') === 'all' ? 'selected' : '' }}>📋 Semua Status</option>
                        <option value="delivered" {{ request('filter') === 'delivered' ? 'selected' : '' }}>✅ Terkirim</option>
                        <option value="returned" {{ request('filter') === 'returned' ? 'selected' : '' }}>🔄 Dikembalikan</option>
                        <option value="completed" {{ request('filter') === 'completed' ? 'selected' : '' }}>✔️ Selesai</option>
                        <option value="issue" {{ request('filter') === 'issue' ? 'selected' : '' }}>⚠️ Ada Masalah</option>
                    </select>
                </div>

                <!-- Search Button -->
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                    <span class="flex items-center space-x-2">
                        <x-heroicon-o-magnifying-glass class="h-5 w-5" />
                        <span>Cari</span>
                    </span>
                </button>

                <!-- Reset Button -->
                @if(request('search') || request('filter') !== 'all')
                <a href="{{ route('courier.deliveries.history') }}" class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition font-medium text-center">
                    Reset
                </a>
                @endif
            </form>
        </div>

        <!-- History List -->
        <div class="space-y-4">
            @forelse($bookings as $booking)
            <div class="bg-white rounded-lg shadow hover:shadow-lg transition border-l-4 
                @if($booking->order_status === \App\Enums\OrderStatus::DELIVERED) border-green-500
                @elseif($booking->order_status === \App\Enums\OrderStatus::PENDING_REVIEW) border-orange-500
                @elseif($booking->order_status === \App\Enums\OrderStatus::COMPLETED) border-emerald-500
                @elseif($booking->order_status === \App\Enums\OrderStatus::ISSUE_DETECTED) border-red-500
                @else border-gray-500
                @endif">
                <div class="p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
                        <!-- Basic Info -->
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
                                    <p class="text-sm text-gray-600">Kode: <span class="font-mono font-semibold">{{ $booking->book_code }}</span></p>
                                    <p class="text-sm text-gray-600">📍 {{ $booking->booker_name }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Timeline -->
                        <div>
                            <h4 class="text-xs font-semibold text-gray-700 mb-2 uppercase">Timeline</h4>
                            <div class="space-y-1 text-sm">
                                @if($booking->delivery_at)
                                <div>
                                    <span class="text-gray-600">Dikirim:</span>
                                    <p class="font-semibold">{{ $booking->delivery_at->format('d M Y') }}</p>
                                </div>
                                @endif
                                @if($booking->returned_at)
                                <div>
                                    <span class="text-gray-600">Dikembalikan:</span>
                                    <p class="font-semibold">{{ $booking->returned_at->format('d M Y') }}</p>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Order Status -->
                        <div>
                            <h4 class="text-xs font-semibold text-gray-700 mb-2 uppercase">Status Order</h4>
                            <span class="px-3 py-1 inline-block text-xs font-semibold rounded-full 
                                @if($booking->order_status === \App\Enums\OrderStatus::DELIVERED) bg-green-100 text-green-800
                                @elseif($booking->order_status === \App\Enums\OrderStatus::PENDING_REVIEW) bg-orange-100 text-orange-800
                                @elseif($booking->order_status === \App\Enums\OrderStatus::COMPLETED) bg-emerald-100 text-emerald-800
                                @elseif($booking->order_status === \App\Enums\OrderStatus::ISSUE_DETECTED) bg-red-100 text-red-800
                                @endif">
                                {{ $booking->order_status->label() }}
                            </span>
                        </div>

                        <!-- Actions -->
                        <div>
                            <h4 class="text-xs font-semibold text-gray-700 mb-2 uppercase">Aksi</h4>
                            <a href="{{ route('courier.deliveries.show', ['type' => $booking instanceof \App\Models\BookProduct ? 'product' : 'book', 'id' => $booking->id]) }}" 
                               class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-semibold">
                                <x-heroicon-o-eye class="h-4 w-4 mr-2" />
                                Lihat Detail
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="bg-white rounded-lg shadow p-12 text-center">
                <x-heroicon-o-clock class="h-16 w-16 text-gray-300 mx-auto mb-4" />
                <p class="text-gray-500 text-lg font-medium">Tidak ada history pengiriman</p>
                <p class="text-gray-400 text-sm mt-2">History akan muncul setelah pengiriman selesai</p>
            </div>
            @endforelse
        </div>

        <!-- Total Count -->
        @if($bookings->count() > 0)
        <div class="mt-6 text-center">
            <p class="text-gray-500 text-sm">
                Menampilkan <span class="font-semibold text-gray-700">{{ $bookings->count() }}</span> history
            </p>
        </div>
        @endif
    </div>
</div>
@endsection
