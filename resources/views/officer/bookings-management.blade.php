@extends('layouts.officer')

@section('title', 'Booking Management')

@section('content')
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center space-x-3 mb-2">
                <x-heroicon-o-clipboard-document-check class="h-8 w-8 text-blue-600" />
                <h1 class="text-3xl font-bold text-gray-900">Manajemen Booking</h1>
            </div>
            <p class="text-gray-600">Kelola semua booking produk dan paket</p>
        </div>

        <!-- Search Bar -->
        <div class="mb-6 bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-sm font-semibold text-gray-700 mb-3 uppercase">Pencarian Booking</h3>
            <form method="GET" class="flex gap-3">
                <div class="flex-1 flex gap-3">
                    <select name="search_type" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="all" {{ $searchType === 'all' ? 'selected' : '' }}>Cari Semua</option>
                        <option value="booking_id" {{ $searchType === 'booking_id' ? 'selected' : '' }}>ID Booking</option>
                        <option value="user_name" {{ $searchType === 'user_name' ? 'selected' : '' }}>Nama User</option>
                        <option value="product_name" {{ $searchType === 'product_name' ? 'selected' : '' }}>Nama Produk/Paket</option>
                    </select>
                    <input type="text" name="search_query" placeholder="Masukkan kata kunci pencarian..." 
                        class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                        value="{{ $searchQuery }}">
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition flex items-center gap-2">
                        <x-heroicon-o-magnifying-glass class="h-5 w-5" />
                        Cari
                    </button>
                </div>
                @if($searchQuery)
                    <a href="{{ route('officer.bookings.index') }}" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 font-medium transition">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        <!-- Product Bookings -->
        <div class="mb-12">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-2xl font-bold text-green-700">Booking Produk</h2>
                @if($searchQuery)
                    <span class="text-sm text-gray-600 bg-blue-100 px-3 py-1 rounded-full">Hasil pencarian: {{ $bookProducts->count() }} ditemukan</span>
                @endif
            </div>
            <div class="space-y-4">
                @forelse($bookProducts as $booking)
                <div class="bg-white rounded-lg shadow hover:shadow-lg transition border-l-4 
                    @if($booking->order_status->value == 'Draft') border-gray-400
                    @elseif($booking->order_status->value == 'Awaiting Validation') border-yellow-400
                    @elseif($booking->order_status->value == 'Confirmed') border-blue-400
                    @elseif(in_array($booking->order_status->value, ['Ready for Pickup', 'Out for Delivery', 'Delivered'])) border-green-400
                    @elseif(in_array($booking->order_status->value, ['Pickup Scheduled', 'On Process Return', 'Pending Review'])) border-orange-400
                    @elseif($booking->order_status->value == 'Completed') border-emerald-400
                    @elseif($booking->order_status->value == 'Issue Detected') border-red-400
                    @else border-gray-400
                    @endif">
                    <div class="p-6">
                        <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
                            <!-- Basic Info -->
                            <div class="lg:col-span-2">
                                <div class="flex items-start space-x-4">
                                    @if($booking->product && $booking->product->image)
                                        <img src="{{ asset('storage/' . $booking->product->image) }}" alt="{{ $booking->product->name }}" class="w-16 h-16 rounded-lg object-cover">
                                    @else
                                        <div class="w-16 h-16 rounded-lg bg-gray-200 flex items-center justify-center">
                                            <x-heroicon-o-photo class="h-6 w-6 text-gray-400" />
                                        </div>
                                    @endif
                                    <div class="flex-1">
                                        <h3 class="text-lg font-bold text-gray-900">{{ $booking->product->name ?? 'Produk Dihapus' }}</h3>
                                        <p class="text-sm text-gray-600">Kode: <span class="font-mono font-semibold text-green-600">{{ $booking->book_code }}</span></p>
                                        <p class="text-sm text-gray-600">Kategori: <strong>{{ $booking->product->category->name ?? '-' }}</strong></p>
                                        <p class="text-sm text-gray-600">Penyewa: <strong>{{ $booking->booker_name }}</strong></p>
                                        <p class="text-sm text-gray-600">{{ $booking->booker_telp }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Dates -->
                            <div>
                                <h4 class="text-xs font-semibold text-gray-700 mb-2 uppercase">Periode Sewa</h4>
                                <div class="space-y-1 text-sm">
                                    <div>
                                        <span class="text-gray-600">Mulai:</span>
                                        <p class="font-semibold">{{ $booking->checkin_appointment_start->format('d M Y') }}</p>
                                    </div>
                                    <div>
                                        <span class="text-gray-600">Berakhir:</span>
                                        <p class="font-semibold">{{ $booking->checkout_appointment_end->format('d M Y') }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Status -->
                            <div>
                                <h4 class="text-xs font-semibold text-gray-700 mb-2 uppercase">Status</h4>
                                <div class="space-y-2">
                                    <span class="px-2 py-1 inline-block text-xs font-semibold rounded 
                                        @if($booking->order_status->value == 'Draft') bg-gray-100 text-gray-800
                                        @elseif($booking->order_status->value == 'Awaiting Validation') bg-yellow-100 text-yellow-800
                                        @elseif($booking->order_status->value == 'Confirmed') bg-blue-100 text-blue-800
                                        @elseif(in_array($booking->order_status->value, ['Ready for Pickup', 'Out for Delivery', 'Delivered'])) bg-green-100 text-green-800
                                        @elseif(in_array($booking->order_status->value, ['Pickup Scheduled', 'On Process Return', 'Pending Review'])) bg-orange-100 text-orange-800
                                        @elseif($booking->order_status->value == 'Completed') bg-emerald-100 text-emerald-800
                                        @elseif($booking->order_status->value == 'Issue Detected') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ $booking->order_status->label() }}
                                    </span>
                                    <p class="text-xs text-gray-600 mt-2">{{ $booking->item_status->label() }}</p>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div>
                                <h4 class="text-xs font-semibold text-gray-700 mb-2 uppercase">Aksi</h4>
                                <div class="space-y-2">
                                    <a href="{{ route('officer.bookings.show', ['type' => 'product', 'bookingId' => $booking->id]) }}" class="w-full bg-blue-500 hover:bg-blue-600 text-white text-xs font-medium py-2 px-3 rounded transition block text-center">
                                        👁️ Lihat Detail
                                    </a>
                                    <x-booking-status-actions :booking="$booking" type="product" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="bg-white rounded-lg shadow p-8 text-center">
                    <x-heroicon-o-inbox class="h-12 w-12 text-gray-300 mx-auto mb-3" />
                    <p class="text-gray-600">Tidak ada booking produk</p>
                </div>
                @endforelse
            </div>
            @if($bookProducts instanceof \Illuminate\Pagination\Paginator)
                <div class="mt-6">
                    {{ $bookProducts->links('pagination::tailwind', ['pageName' => 'product_page']) }}
                </div>
            @endif
        </div>

        <!-- Package Bookings -->
        <div>
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-2xl font-bold text-blue-700">Booking Paket</h2>
                @if($searchQuery)
                    <span class="text-sm text-gray-600 bg-blue-100 px-3 py-1 rounded-full">Hasil pencarian: {{ $books->count() }} ditemukan</span>
                @endif
            </div>
            <div class="space-y-4">
                @forelse($books as $booking)
                <div class="bg-white rounded-lg shadow hover:shadow-lg transition border-l-4 
                    @if($booking->order_status->value == 'Draft') border-gray-400
                    @elseif($booking->order_status->value == 'Awaiting Validation') border-yellow-400
                    @elseif($booking->order_status->value == 'Confirmed') border-blue-400
                    @elseif(in_array($booking->order_status->value, ['Ready for Pickup', 'Out for Delivery', 'Delivered'])) border-green-400
                    @elseif(in_array($booking->order_status->value, ['Pickup Scheduled', 'On Process Return', 'Pending Review'])) border-orange-400
                    @elseif($booking->order_status->value == 'Completed') border-emerald-400
                    @elseif($booking->order_status->value == 'Issue Detected') border-red-400
                    @else border-gray-400
                    @endif">
                    <div class="p-6">
                        <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
                            <!-- Basic Info -->
                            <div class="lg:col-span-2">
                                <div class="flex items-start space-x-4">
                                    @if($booking->package && $booking->package->image)
                                        <img src="{{ asset('storage/' . $booking->package->image) }}" alt="{{ $booking->package->name_package }}" class="w-16 h-16 rounded-lg object-cover">
                                    @else
                                        <div class="w-16 h-16 rounded-lg bg-gray-200 flex items-center justify-center">
                                            <x-heroicon-o-photo class="h-6 w-6 text-gray-400" />
                                        </div>
                                    @endif
                                    <div class="flex-1">
                                        <h3 class="text-lg font-bold text-gray-900">{{ $booking->package->name_package ?? 'Paket Dihapus' }}</h3>
                                        <p class="text-sm text-gray-600">Kode: <span class="font-mono font-semibold text-blue-600">{{ $booking->book_code }}</span></p>
                                        <p class="text-sm text-gray-600">Penyewa: <strong>{{ $booking->booker_name }}</strong></p>
                                        <p class="text-sm text-gray-600">{{ $booking->booker_telp }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Dates -->
                            <div>
                                <h4 class="text-xs font-semibold text-gray-700 mb-2 uppercase">Periode Sewa</h4>
                                <div class="space-y-1 text-sm">
                                    <div>
                                        <span class="text-gray-600">Mulai:</span>
                                        <p class="font-semibold">{{ $booking->checkin_appointment_start->format('d M Y') }}</p>
                                    </div>
                                    <div>
                                        <span class="text-gray-600">Berakhir:</span>
                                        <p class="font-semibold">{{ $booking->checkout_appointment_end->format('d M Y') }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Status -->
                            <div>
                                <h4 class="text-xs font-semibold text-gray-700 mb-2 uppercase">Status</h4>
                                <div class="space-y-2">
                                    <span class="px-2 py-1 inline-block text-xs font-semibold rounded 
                                        @if($booking->order_status->value == 'Draft') bg-gray-100 text-gray-800
                                        @elseif($booking->order_status->value == 'Awaiting Validation') bg-yellow-100 text-yellow-800
                                        @elseif($booking->order_status->value == 'Confirmed') bg-blue-100 text-blue-800
                                        @elseif(in_array($booking->order_status->value, ['Ready for Pickup', 'Out for Delivery', 'Delivered'])) bg-green-100 text-green-800
                                        @elseif(in_array($booking->order_status->value, ['Pickup Scheduled', 'On Process Return', 'Pending Review'])) bg-orange-100 text-orange-800
                                        @elseif($booking->order_status->value == 'Completed') bg-emerald-100 text-emerald-800
                                        @elseif($booking->order_status->value == 'Issue Detected') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ $booking->order_status->label() }}
                                    </span>
                                    <p class="text-xs text-gray-600 mt-2">{{ $booking->item_status->label() }}</p>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div>
                                <h4 class="text-xs font-semibold text-gray-700 mb-2 uppercase">Aksi</h4>
                                <div class="space-y-2">
                                    <a href="{{ route('officer.bookings.show', ['type' => 'package', 'bookingId' => $booking->id]) }}" class="w-full bg-blue-500 hover:bg-blue-600 text-white text-xs font-medium py-2 px-3 rounded transition block text-center">
                                        👁️ Lihat Detail
                                    </a>
                                    <x-booking-status-actions :booking="$booking" type="package" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="bg-white rounded-lg shadow p-8 text-center">
                    <x-heroicon-o-inbox class="h-12 w-12 text-gray-300 mx-auto mb-3" />
                    <p class="text-gray-600">Tidak ada booking paket</p>
                </div>
                @endforelse
            </div>
            @if($books instanceof \Illuminate\Pagination\Paginator)
                <div class="mt-6">
                    {{ $books->links('pagination::tailwind', ['pageName' => 'package_page']) }}
                </div>
            @endif
        </div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection
