@extends('layouts.officer')

@section('title', 'Assign Courier')

@section('content')
<div class="min-h-screen bg-gray-50 py-8 px-4 lg:px-16">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center space-x-3 mb-2">
                <x-heroicon-o-truck class="h-8 w-8 text-blue-600" />
                <h1 class="text-3xl font-bold text-gray-900">Assign Courier</h1>
            </div>
            <p class="text-gray-600">Assign courier untuk pengiriman dan penjemputan barang</p>
        </div>

        <!-- Tabs -->
        <div class="mb-6 border-b border-gray-200">
            <nav class="-mb-px flex space-x-8">
                <a href="?tab=delivery" class="@if($tab === 'delivery') border-blue-500 text-blue-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    📦 Perlu Pengiriman ({{ $needDelivery->count() }})
                </a>
                <a href="?tab=return" class="@if($tab === 'return') border-blue-500 text-blue-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    🔄 Perlu Penjemputan ({{ $needReturn->count() }})
                </a>
            </nav>
        </div>

        @if($tab === 'delivery')
            <!-- Delivery Assignments -->
            <div class="grid grid-cols-1 gap-4">
                @forelse($needDelivery as $booking)
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Booking Info -->
                        <div class="lg:col-span-2">
                            <div class="flex items-start space-x-4">
                                @if($booking->product && $booking->product->image)
                                    <img src="{{ asset('storage/' . $booking->product->image) }}" alt="{{ $booking->product->name }}" class="w-20 h-20 rounded-lg object-cover">
                                @elseif($booking->package && $booking->package->image)
                                    <img src="{{ asset('storage/' . $booking->package->image) }}" alt="{{ $booking->package->name_package }}" class="w-20 h-20 rounded-lg object-cover">
                                @else
                                    <div class="w-20 h-20 rounded-lg bg-gray-200 flex items-center justify-center">
                                        <x-heroicon-o-photo class="h-8 w-8 text-gray-400" />
                                    </div>
                                @endif
                                <div class="flex-1">
                                    <h3 class="text-lg font-bold text-gray-900">
                                        {{ $booking->product->name ?? $booking->package->name_package ?? 'Barang Dihapus' }}
                                    </h3>
                                    <p class="text-sm text-gray-600">Kode: <span class="font-mono font-semibold text-blue-600">{{ $booking->book_code }}</span></p>
                                    <p class="text-sm text-gray-600">📍 {{ $booking->booker_name }}</p>
                                    <p class="text-sm text-gray-600">📱 {{ $booking->booker_telp }}</p>
                                    <p class="text-xs text-gray-500 mt-2">Status: <span class="font-semibold">{{ $booking->order_status->label() }}</span></p>
                                    @if($booking->id_courier)
                                        <p class="text-xs text-green-600 mt-1">✓ Courier sudah di-assign</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Assign Form -->
                        <div>
                            <form action="{{ route('officer.assign-courier.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="booking_id" value="{{ $booking->id }}">
                                <input type="hidden" name="booking_type" value="{{ $booking instanceof \App\Models\BookProduct ? 'product' : 'package' }}">
                                <input type="hidden" name="assignment_type" value="delivery">
                                
                                <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Courier</label>
                                <select name="courier_id" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                                    <option value="">-- Pilih Courier --</option>
                                    @foreach($couriers as $courier)
                                        <option value="{{ $courier->id }}" @if($booking->id_courier === $courier->id) selected @endif>
                                            {{ $courier->name }}
                                        </option>
                                    @endforeach
                                </select>
                                
                                <button type="submit" class="w-full mt-3 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition">
                                    Assign Courier
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @empty
                <div class="bg-white rounded-lg shadow p-8 text-center">
                    <x-heroicon-o-inbox class="h-12 w-12 text-gray-300 mx-auto mb-3" />
                    <p class="text-gray-600">Tidak ada booking yang perlu assign courier untuk pengiriman</p>
                </div>
                @endforelse
            </div>
        @else
            <!-- Return Assignments -->
            <div class="grid grid-cols-1 gap-4">
                @forelse($needReturn as $booking)
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Booking Info -->
                        <div class="lg:col-span-2">
                            <div class="flex items-start space-x-4">
                                @if($booking->product && $booking->product->image)
                                    <img src="{{ asset('storage/' . $booking->product->image) }}" alt="{{ $booking->product->name }}" class="w-20 h-20 rounded-lg object-cover">
                                @elseif($booking->package && $booking->package->image)
                                    <img src="{{ asset('storage/' . $booking->package->image) }}" alt="{{ $booking->package->name_package }}" class="w-20 h-20 rounded-lg object-cover">
                                @else
                                    <div class="w-20 h-20 rounded-lg bg-gray-200 flex items-center justify-center">
                                        <x-heroicon-o-photo class="h-8 w-8 text-gray-400" />
                                    </div>
                                @endif
                                <div class="flex-1">
                                    <h3 class="text-lg font-bold text-gray-900">
                                        {{ $booking->product->name ?? $booking->package->name_package ?? 'Barang Dihapus' }}
                                    </h3>
                                    <p class="text-sm text-gray-600">Kode: <span class="font-mono font-semibold text-blue-600">{{ $booking->book_code }}</span></p>
                                    <p class="text-sm text-gray-600">📍 {{ $booking->booker_name }}</p>
                                    <p class="text-sm text-gray-600">📱 {{ $booking->booker_telp }}</p>
                                    <p class="text-xs text-gray-500 mt-2">Status: <span class="font-semibold">{{ $booking->order_status->label() }}</span></p>
                                    @if($booking->id_courier)
                                        <p class="text-xs text-green-600 mt-1">✓ Courier sudah di-assign</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Assign Form -->
                        <div>
                            <form action="{{ route('officer.assign-courier.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="booking_id" value="{{ $booking->id }}">
                                <input type="hidden" name="booking_type" value="{{ $booking instanceof \App\Models\BookProduct ? 'product' : 'package' }}">
                                <input type="hidden" name="assignment_type" value="return">
                                
                                <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Courier</label>
                                <select name="courier_id" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                                    <option value="">-- Pilih Courier --</option>
                                    @foreach($couriers as $courier)
                                        <option value="{{ $courier->id }}" @if($booking->id_courier === $courier->id) selected @endif>
                                            {{ $courier->name }}
                                        </option>
                                    @endforeach
                                </select>
                                
                                <button type="submit" class="w-full mt-3 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition">
                                    Assign Courier
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @empty
                <div class="bg-white rounded-lg shadow p-8 text-center">
                    <x-heroicon-o-inbox class="h-12 w-12 text-gray-300 mx-auto mb-3" />
                    <p class="text-gray-600">Tidak ada booking yang perlu assign courier untuk penjemputan</p>
                </div>
                @endforelse
            </div>
        @endif
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: '{{ session('success') }}',
        timer: 2000,
        showConfirmButton: false
    });
</script>
@endif

@if(session('error'))
<script>
    Swal.fire({
        icon: 'error',
        title: 'Error!',
        text: '{{ session('error') }}',
        confirmButtonColor: '#3B82F6'
    });
</script>
@endif
@endsection
