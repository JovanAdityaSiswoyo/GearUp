@extends('layouts.courier')

@section('title', 'Courier Dashboard')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Dashboard Courier</h1>
            <p class="text-gray-600 mt-2">Kelola pengiriman dan pengembalian barang rental</p>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Ready for Pickup -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Siap Diambil</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['readyForPickup'] ?? 0 }}</p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-lg">
                        <x-heroicon-o-archive-box class="h-8 w-8 text-blue-600" />
                    </div>
                </div>
            </div>

            <!-- Out for Delivery -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Dalam Pengiriman</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['outForDelivery'] ?? 0 }}</p>
                    </div>
                    <div class="bg-orange-100 p-3 rounded-lg">
                        <x-heroicon-o-truck class="h-8 w-8 text-orange-600" />
                    </div>
                </div>
            </div>

            <!-- Return Scheduled -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Pengembalian Dijadwal</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['returnScheduled'] ?? 0 }}</p>
                    </div>
                    <div class="bg-purple-100 p-3 rounded-lg">
                        <x-heroicon-o-calendar class="h-8 w-8 text-purple-600" />
                    </div>
                </div>
            </div>

            <!-- On Process Return -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Dalam Pengembalian</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['onProcessReturn'] ?? 0 }}</p>
                    </div>
                    <div class="bg-red-100 p-3 rounded-lg">
                        <x-heroicon-o-arrow-uturn-left class="h-8 w-8 text-red-600" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- Active Deliveries -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <x-heroicon-o-truck class="h-5 w-5 mr-2 text-blue-600" />
                        Pengiriman Aktif
                    </h2>
                    <div class="space-y-2">
                        @forelse($activeDeliveries ?? [] as $delivery)
                            <a href="{{ route('courier.deliveries.show', ['type' => class_basename($delivery), 'id' => $delivery->id]) }}" class="flex items-center justify-between p-3 hover:bg-gray-50 rounded border border-gray-200">
                                <div class="flex-1">
                                    <p class="font-medium text-gray-900">{{ $delivery->product?->nama ?? $delivery->package?->nama }}</p>
                                    <p class="text-sm text-gray-500">{{ $delivery->book_code }}</p>
                                </div>
                                <x-heroicon-o-chevron-right class="h-5 w-5 text-gray-400" />
                            </a>
                        @empty
                            <p class="text-gray-500 text-center py-4">Tidak ada pengiriman aktif</p>
                        @endforelse
                    </div>
                    <a href="{{ route('courier.deliveries.index') }}" class="mt-4 w-full py-2 px-4 bg-blue-600 text-white rounded-lg text-center hover:bg-blue-700 transition">
                        Lihat Semua
                    </a>
                </div>
            </div>

            <!-- Active Returns -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <x-heroicon-o-arrow-uturn-left class="h-5 w-5 mr-2 text-red-600" />
                        Pengembalian Aktif
                    </h2>
                    <div class="space-y-2">
                        @forelse($activeReturns ?? [] as $return)
                            <a href="{{ route('courier.deliveries.show', ['type' => class_basename($return), 'id' => $return->id]) }}" class="flex items-center justify-between p-3 hover:bg-gray-50 rounded border border-gray-200">
                                <div class="flex-1">
                                    <p class="font-medium text-gray-900">{{ $return->product?->nama ?? $return->package?->nama }}</p>
                                    <p class="text-sm text-gray-500">{{ $return->book_code }}</p>
                                </div>
                                <x-heroicon-o-chevron-right class="h-5 w-5 text-gray-400" />
                            </a>
                        @empty
                            <p class="text-gray-500 text-center py-4">Tidak ada pengembalian aktif</p>
                        @endforelse
                    </div>
                    <a href="{{ route('courier.deliveries.index') }}" class="mt-4 w-full py-2 px-4 bg-red-600 text-white rounded-lg text-center hover:bg-red-700 transition">
                        Lihat Semua
                    </a>
                </div>
            </div>
        </div>

        <!-- Recent Completed -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <x-heroicon-o-check-circle class="h-5 w-5 mr-2 text-green-600" />
                    Pengiriman Terakhir Selesai
                </h2>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="text-left py-3 px-4 font-semibold text-gray-700">Kode Booking</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-700">Item</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-700">Tipe</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-700">Status Pengiriman</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-700">Tanggal Selesai</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentCompleted ?? [] as $item)
                                <tr class="border-b border-gray-200 hover:bg-gray-50">
                                    <td class="py-3 px-4 font-medium text-gray-900">{{ $item->book_code }}</td>
                                    <td class="py-3 px-4">{{ $item->product?->nama ?? $item->package?->nama }}</td>
                                    <td class="py-3 px-4">
                                        <span class="text-sm px-2 py-1 bg-gray-100 text-gray-800 rounded">
                                            {{ class_basename($item) }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="text-sm px-2 py-1 bg-green-100 text-green-800 rounded">
                                            {{ $item->order_status->label() }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-sm text-gray-600">
                                        {{ $item->returned_at?->format('d M Y H:i') ?? $item->delivery_at?->format('d M Y H:i') ?? '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-gray-500">
                                        Belum ada pengiriman yang selesai
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
