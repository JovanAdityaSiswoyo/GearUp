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

        <!-- All Bookings -->
        <div>
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-2xl font-bold text-gray-800">Semua Booking</h2>
                @if($searchQuery)
                    <span class="text-sm text-gray-600 bg-blue-100 px-3 py-1 rounded-full">Hasil pencarian: {{ ($bookProducts instanceof \Illuminate\Pagination\Paginator ? $bookProducts->total() : $bookProducts->count()) + ($books instanceof \Illuminate\Pagination\Paginator ? $books->total() : $books->count()) }} ditemukan</span>
                @endif
            </div>
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase w-20">Gambar</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode Booking</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Periode</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Penyewa</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($bookProducts as $booking)
                            @php
                                $orderStatusValue = $booking->order_status->value;
                                $isApproved = isset($approvedProductIds[(string) $booking->id]);
                                $rowBorderClass = match ($orderStatusValue) {
                                    'pending' => 'border-amber-400',
                                    'dipinjam' => 'border-blue-400',
                                    'selesai' => 'border-emerald-400',
                                    default => 'border-gray-400',
                                };
                                $statusBadgeClass = match ($orderStatusValue) {
                                    'pending' => 'bg-amber-100 text-amber-800',
                                    'dipinjam' => 'bg-blue-100 text-blue-800',
                                    'selesai' => 'bg-emerald-100 text-emerald-800',
                                    default => 'bg-gray-100 text-gray-800',
                                };
                                $displayStatusLabel = $isApproved && $orderStatusValue === 'pending'
                                    ? 'Approved'
                                    : $booking->order_status->label();
                                $displayStatusBadgeClass = $isApproved && $orderStatusValue === 'pending'
                                    ? 'bg-emerald-100 text-emerald-800'
                                    : $statusBadgeClass;
                            @endphp
                            <tr class="hover:bg-gray-50 border-l-4 {{ $rowBorderClass }}">
                                <td class="px-4 py-4 text-center">
                                    @if($booking->product && $booking->product->image)
                                        <img src="{{ asset('storage/' . $booking->product->image) }}" alt="{{ $booking->product->name }}" class="w-16 h-16 rounded object-cover mx-auto">
                                    @else
                                        <div class="w-16 h-16 rounded bg-gray-200 flex items-center justify-center mx-auto">
                                            <x-heroicon-o-photo class="h-6 w-6 text-gray-400" />
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-gray-900">{{ $booking->book_code }}</div>
                                    <div class="text-xs text-gray-500">{{ $booking->created_at->format('M d, Y') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        Product
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $booking->product->name ?? 'Produk Dihapus' }}</div>
                                    <div class="text-xs text-gray-500">{{ $booking->product->category->categories ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $booking->checkin_appointment_start->format('M d') }}</div>
                                    <div class="text-xs text-gray-500">s/d {{ $booking->checkout_appointment_end->format('M d, Y') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $booking->booker_name }}</div>
                                    <div class="text-xs text-gray-500">{{ $booking->booker_telp }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="flex flex-col gap-1 items-center">
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $displayStatusBadgeClass }} justify-center">
                                            {{ $displayStatusLabel }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex gap-2 items-center">
                                        <a href="{{ route('officer.bookings.show', ['type' => 'product', 'bookingId' => $booking->id]) }}" class="text-blue-600 hover:text-blue-900 font-medium">Detail</a>
                                        <x-booking-status-actions :booking="$booking" type="product" :isApproved="$isApproved" />
                                    </div>
                                </td>
                            </tr>
                            @empty
                            @endforelse
                            @forelse($books as $booking)
                            @php
                                $orderStatusValue = $booking->order_status->value;
                                $isApproved = isset($approvedPackageIds[(string) $booking->id]);
                                $rowBorderClass = match ($orderStatusValue) {
                                    'pending' => 'border-amber-400',
                                    'dipinjam' => 'border-blue-400',
                                    'selesai' => 'border-emerald-400',
                                    default => 'border-gray-400',
                                };
                                $statusBadgeClass = match ($orderStatusValue) {
                                    'pending' => 'bg-amber-100 text-amber-800',
                                    'dipinjam' => 'bg-blue-100 text-blue-800',
                                    'selesai' => 'bg-emerald-100 text-emerald-800',
                                    default => 'bg-gray-100 text-gray-800',
                                };
                                $displayStatusLabel = $isApproved && $orderStatusValue === 'pending'
                                    ? 'Approved'
                                    : $booking->order_status->label();
                                $displayStatusBadgeClass = $isApproved && $orderStatusValue === 'pending'
                                    ? 'bg-emerald-100 text-emerald-800'
                                    : $statusBadgeClass;
                            @endphp
                            <tr class="hover:bg-gray-50 border-l-4 {{ $rowBorderClass }}">
                                <td class="px-4 py-4 text-center">
                                    @if($booking->package && $booking->package->image)
                                        <img src="{{ asset('storage/' . $booking->package->image) }}" alt="{{ $booking->package->name_package }}" class="w-16 h-16 rounded object-cover mx-auto">
                                    @else
                                        <div class="w-16 h-16 rounded bg-gray-200 flex items-center justify-center mx-auto">
                                            <x-heroicon-o-photo class="h-6 w-6 text-gray-400" />
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-gray-900">{{ $booking->book_code }}</div>
                                    <div class="text-xs text-gray-500">{{ $booking->created_at->format('M d, Y') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                        Package
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $booking->package->name_package ?? 'Paket Dihapus' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $booking->checkin_appointment_start->format('M d') }}</div>
                                    <div class="text-xs text-gray-500">s/d {{ $booking->checkout_appointment_end->format('M d, Y') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $booking->booker_name }}</div>
                                    <div class="text-xs text-gray-500">{{ $booking->booker_telp }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="flex flex-col gap-1 items-center">
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $displayStatusBadgeClass }} justify-center">
                                            {{ $displayStatusLabel }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex gap-2 items-center">
                                        <a href="{{ route('officer.bookings.show', ['type' => 'package', 'bookingId' => $booking->id]) }}" class="text-blue-600 hover:text-blue-900 font-medium">Detail</a>
                                        <x-booking-status-actions :booking="$booking" type="package" :isApproved="$isApproved" />
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="px-6 py-8 text-center">
                                    <x-heroicon-o-inbox class="h-12 w-12 text-gray-300 mx-auto mb-3" />
                                    <p class="text-gray-600">Tidak ada booking</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($bookProducts instanceof \Illuminate\Pagination\Paginator || $books instanceof \Illuminate\Pagination\Paginator)
                    <div class="px-6 py-4 border-t border-gray-200">
                        @if($bookProducts instanceof \Illuminate\Pagination\Paginator)
                            {{ $bookProducts->links('pagination::tailwind', ['pageName' => 'product_page']) }}
                        @elseif($books instanceof \Illuminate\Pagination\Paginator)
                            {{ $books->links('pagination::tailwind', ['pageName' => 'package_page']) }}
                        @endif
                    </div>
                @endif
            </div>
        </div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection
