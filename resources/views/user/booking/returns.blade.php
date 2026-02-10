<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengembalian Saya - GearUp</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-50">
    @include('layouts.app')
    @include('sweetalert::alert')

    <div class="min-h-screen flex flex-col">
        <main class="flex-1 px-6 lg:px-16 py-8">
            <div class="max-w-6xl mx-auto">
                <div class="mb-8">
                    <div class="flex items-center space-x-3 mb-2">
                        <x-heroicon-o-arrow-uturn-left class="h-8 w-8 text-orange-500" />
                        <h1 class="text-3xl lg:text-4xl font-bold text-gray-900">Pengembalian Saya</h1>
                    </div>
                    <p class="text-gray-600">Kelola jadwal pengembalian dan pantau progres pengembalian alat camping Anda.</p>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6 mb-8 border border-orange-100">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <div class="lg:col-span-2">
                            <h2 class="text-xl font-bold text-gray-900 mb-2">Panduan Pengembalian</h2>
                            <p class="text-gray-600 mb-4">Ikuti langkah berikut untuk pengembalian yang lancar.</p>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                <div class="bg-orange-50 border border-orange-100 rounded-lg p-4">
                                    <p class="font-semibold text-orange-700 mb-1">1. Cek jadwal</p>
                                    <p class="text-gray-600">Lihat status penjemputan di kartu booking.</p>
                                </div>
                                <div class="bg-orange-50 border border-orange-100 rounded-lg p-4">
                                    <p class="font-semibold text-orange-700 mb-1">2. Siapkan barang</p>
                                    <p class="text-gray-600">Bersihkan dan pastikan lengkap sebelum kurir datang.</p>
                                </div>
                                <div class="bg-orange-50 border border-orange-100 rounded-lg p-4">
                                    <p class="font-semibold text-orange-700 mb-1">3. Serahkan ke kurir</p>
                                    <p class="text-gray-600">Kurir akan mengambil dan memproses pengembalian.</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-900 text-white rounded-lg p-5">
                            <p class="text-sm text-gray-300">Butuh bantuan cepat?</p>
                            <p class="text-lg font-semibold mt-2">Hubungi WhatsApp</p>
                            <a href="https://wa.me/6287812000155" class="mt-4 inline-flex items-center space-x-2 bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition">
                                <x-heroicon-o-phone class="h-5 w-5" />
                                <span>0878 1200 0155</span>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="space-y-12">
                    <div>
                        <h2 class="text-2xl font-bold text-orange-700 mb-4">Perlu Dikembalikan</h2>
                        @if($productReturnNeeded->isEmpty() && $packageReturnNeeded->isEmpty())
                            <div class="bg-white rounded-xl shadow-sm p-6 text-center text-gray-600">
                                Belum ada barang yang perlu dikembalikan.
                            </div>
                        @else
                            <div class="space-y-6">
                                @foreach($packageReturnNeeded as $booking)
                                    <div class="bg-white rounded-xl shadow-sm hover:shadow-lg transition overflow-hidden border-l-4 border-orange-400">
                                        <div class="p-6">
                                            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                                                <div class="md:col-span-2">
                                                    <div class="flex items-start space-x-4">
                                                        @if($booking->package && $booking->package->image)
                                                            <img src="{{ asset('storage/' . $booking->package->image) }}" alt="{{ $booking->package->name_package }}" class="w-20 h-20 rounded-lg object-cover">
                                                        @else
                                                            <div class="w-20 h-20 rounded-lg bg-gray-200 flex items-center justify-center">
                                                                <x-heroicon-o-photo class="h-8 w-8 text-gray-400" />
                                                            </div>
                                                        @endif
                                                        <div class="flex-1">
                                                            <h3 class="text-lg font-bold text-gray-900">{{ $booking->package->name_package ?? 'Paket Dihapus' }}</h3>
                                                            <p class="text-sm text-gray-600 mb-2">Booking Code: <span class="font-mono font-semibold text-orange-600">{{ $booking->book_code }}</span></p>
                                                            <p class="text-sm text-gray-600">Penyewa: {{ $booking->booker_name }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div>
                                                    <h4 class="text-sm font-semibold text-gray-700 mb-3">Detail Pengembalian</h4>
                                                    <div class="space-y-2 text-sm">
                                                        <div>
                                                            <span class="text-gray-600">Akhir Sewa:</span>
                                                            <p class="font-semibold text-gray-900">{{ $booking->checkout_appointment_end?->format('d M Y') ?? '-' }}</p>
                                                        </div>
                                                        <div>
                                                            <span class="text-gray-600">Metode Pengiriman:</span>
                                                            <p class="font-semibold text-gray-900">{{ ucfirst($booking->detailBooks?->first()?->shipping_method ?? 'pickup') }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div>
                                                    <x-booking-status-card :booking="$booking" />
                                                </div>
                                            </div>
                                            @if($booking->courier)
                                                <div class="mt-6 pt-6 border-t">
                                                    <p class="text-sm text-gray-600">Kurir: <span class="font-semibold text-gray-900">{{ $booking->courier->name }}</span> ({{ $booking->courier->phone }})</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach

                                @foreach($productReturnNeeded as $booking)
                                    <div class="bg-white rounded-xl shadow-sm hover:shadow-lg transition overflow-hidden border-l-4 border-orange-400">
                                        <div class="p-6">
                                            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                                                <div class="md:col-span-2">
                                                    <div class="flex items-start space-x-4">
                                                        @if($booking->product && $booking->product->image)
                                                            <img src="{{ asset('storage/' . $booking->product->image) }}" alt="{{ $booking->product->name }}" class="w-20 h-20 rounded-lg object-cover">
                                                        @else
                                                            <div class="w-20 h-20 rounded-lg bg-gray-200 flex items-center justify-center">
                                                                <x-heroicon-o-photo class="h-8 w-8 text-gray-400" />
                                                            </div>
                                                        @endif
                                                        <div class="flex-1">
                                                            <h3 class="text-lg font-bold text-gray-900">{{ $booking->product->name ?? 'Produk Dihapus' }}</h3>
                                                            <p class="text-sm text-gray-600 mb-2">Booking Code: <span class="font-mono font-semibold text-orange-600">{{ $booking->book_code }}</span></p>
                                                            <p class="text-sm text-gray-600">Penyewa: {{ $booking->detailBookProduct?->full_name ?? $booking->booker_name }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div>
                                                    <h4 class="text-sm font-semibold text-gray-700 mb-3">Detail Pengembalian</h4>
                                                    <div class="space-y-2 text-sm">
                                                        <div>
                                                            <span class="text-gray-600">Akhir Sewa:</span>
                                                            <p class="font-semibold text-gray-900">{{ $booking->detailBookProduct?->rental_end_at?->format('d M Y') ?? $booking->checkout_appointment_end?->format('d M Y') ?? '-' }}</p>
                                                        </div>
                                                        <div>
                                                            <span class="text-gray-600">Metode Pengiriman:</span>
                                                            <p class="font-semibold text-gray-900">{{ ucfirst($booking->detailBookProduct?->shipping_method ?? 'pickup') }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div>
                                                    <x-booking-status-card :booking="$booking" />
                                                </div>
                                            </div>
                                            @if($booking->courier)
                                                <div class="mt-6 pt-6 border-t">
                                                    <p class="text-sm text-gray-600">Kurir: <span class="font-semibold text-gray-900">{{ $booking->courier->name }}</span> ({{ $booking->courier->phone }})</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div>
                        <h2 class="text-2xl font-bold text-orange-700 mb-4">Sedang Diproses</h2>
                        @if($productReturnInProcess->isEmpty() && $packageReturnInProcess->isEmpty())
                            <div class="bg-white rounded-xl shadow-sm p-6 text-center text-gray-600">
                                Belum ada pengembalian yang sedang diproses.
                            </div>
                        @else
                            <div class="space-y-6">
                                @foreach($packageReturnInProcess as $booking)
                                    <div class="bg-white rounded-xl shadow-sm hover:shadow-lg transition overflow-hidden border-l-4 border-orange-500">
                                        <div class="p-6">
                                            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                                                <div class="md:col-span-2">
                                                    <div class="flex items-start space-x-4">
                                                        @if($booking->package && $booking->package->image)
                                                            <img src="{{ asset('storage/' . $booking->package->image) }}" alt="{{ $booking->package->name_package }}" class="w-20 h-20 rounded-lg object-cover">
                                                        @else
                                                            <div class="w-20 h-20 rounded-lg bg-gray-200 flex items-center justify-center">
                                                                <x-heroicon-o-photo class="h-8 w-8 text-gray-400" />
                                                            </div>
                                                        @endif
                                                        <div class="flex-1">
                                                            <h3 class="text-lg font-bold text-gray-900">{{ $booking->package->name_package ?? 'Paket Dihapus' }}</h3>
                                                            <p class="text-sm text-gray-600 mb-2">Booking Code: <span class="font-mono font-semibold text-orange-600">{{ $booking->book_code }}</span></p>
                                                            <p class="text-sm text-gray-600">Penyewa: {{ $booking->booker_name }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div>
                                                    <h4 class="text-sm font-semibold text-gray-700 mb-3">Detail Pengembalian</h4>
                                                    <div class="space-y-2 text-sm">
                                                        <div>
                                                            <span class="text-gray-600">Akhir Sewa:</span>
                                                            <p class="font-semibold text-gray-900">{{ $booking->checkout_appointment_end?->format('d M Y') ?? '-' }}</p>
                                                        </div>
                                                        <div>
                                                            <span class="text-gray-600">Metode Pengiriman:</span>
                                                            <p class="font-semibold text-gray-900">{{ ucfirst($booking->detailBooks?->first()?->shipping_method ?? 'pickup') }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div>
                                                    <x-booking-status-card :booking="$booking" />
                                                </div>
                                            </div>
                                            @if($booking->courier)
                                                <div class="mt-6 pt-6 border-t">
                                                    <p class="text-sm text-gray-600">Kurir: <span class="font-semibold text-gray-900">{{ $booking->courier->name }}</span> ({{ $booking->courier->phone }})</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach

                                @foreach($productReturnInProcess as $booking)
                                    <div class="bg-white rounded-xl shadow-sm hover:shadow-lg transition overflow-hidden border-l-4 border-orange-500">
                                        <div class="p-6">
                                            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                                                <div class="md:col-span-2">
                                                    <div class="flex items-start space-x-4">
                                                        @if($booking->product && $booking->product->image)
                                                            <img src="{{ asset('storage/' . $booking->product->image) }}" alt="{{ $booking->product->name }}" class="w-20 h-20 rounded-lg object-cover">
                                                        @else
                                                            <div class="w-20 h-20 rounded-lg bg-gray-200 flex items-center justify-center">
                                                                <x-heroicon-o-photo class="h-8 w-8 text-gray-400" />
                                                            </div>
                                                        @endif
                                                        <div class="flex-1">
                                                            <h3 class="text-lg font-bold text-gray-900">{{ $booking->product->name ?? 'Produk Dihapus' }}</h3>
                                                            <p class="text-sm text-gray-600 mb-2">Booking Code: <span class="font-mono font-semibold text-orange-600">{{ $booking->book_code }}</span></p>
                                                            <p class="text-sm text-gray-600">Penyewa: {{ $booking->detailBookProduct?->full_name ?? $booking->booker_name }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div>
                                                    <h4 class="text-sm font-semibold text-gray-700 mb-3">Detail Pengembalian</h4>
                                                    <div class="space-y-2 text-sm">
                                                        <div>
                                                            <span class="text-gray-600">Akhir Sewa:</span>
                                                            <p class="font-semibold text-gray-900">{{ $booking->detailBookProduct?->rental_end_at?->format('d M Y') ?? $booking->checkout_appointment_end?->format('d M Y') ?? '-' }}</p>
                                                        </div>
                                                        <div>
                                                            <span class="text-gray-600">Metode Pengiriman:</span>
                                                            <p class="font-semibold text-gray-900">{{ ucfirst($booking->detailBookProduct?->shipping_method ?? 'pickup') }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div>
                                                    <x-booking-status-card :booking="$booking" />
                                                </div>
                                            </div>
                                            @if($booking->courier)
                                                <div class="mt-6 pt-6 border-t">
                                                    <p class="text-sm text-gray-600">Kurir: <span class="font-semibold text-gray-900">{{ $booking->courier->name }}</span> ({{ $booking->courier->phone }})</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div>
                        <h2 class="text-2xl font-bold text-emerald-700 mb-4">Riwayat Pengembalian</h2>
                        @if($productReturnCompleted->isEmpty() && $packageReturnCompleted->isEmpty())
                            <div class="bg-white rounded-xl shadow-sm p-6 text-center text-gray-600">
                                Belum ada pengembalian yang selesai.
                            </div>
                        @else
                            <div class="space-y-6">
                                @foreach($packageReturnCompleted as $booking)
                                    <div class="bg-white rounded-xl shadow-sm hover:shadow-lg transition overflow-hidden border-l-4 border-emerald-400">
                                        <div class="p-6">
                                            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                                                <div class="md:col-span-2">
                                                    <div class="flex items-start space-x-4">
                                                        @if($booking->package && $booking->package->image)
                                                            <img src="{{ asset('storage/' . $booking->package->image) }}" alt="{{ $booking->package->name_package }}" class="w-20 h-20 rounded-lg object-cover">
                                                        @else
                                                            <div class="w-20 h-20 rounded-lg bg-gray-200 flex items-center justify-center">
                                                                <x-heroicon-o-photo class="h-8 w-8 text-gray-400" />
                                                            </div>
                                                        @endif
                                                        <div class="flex-1">
                                                            <h3 class="text-lg font-bold text-gray-900">{{ $booking->package->name_package ?? 'Paket Dihapus' }}</h3>
                                                            <p class="text-sm text-gray-600 mb-2">Booking Code: <span class="font-mono font-semibold text-emerald-600">{{ $booking->book_code }}</span></p>
                                                            <p class="text-sm text-gray-600">Penyewa: {{ $booking->booker_name }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div>
                                                    <h4 class="text-sm font-semibold text-gray-700 mb-3">Detail Pengembalian</h4>
                                                    <div class="space-y-2 text-sm">
                                                        <div>
                                                            <span class="text-gray-600">Tanggal Kembali:</span>
                                                            <p class="font-semibold text-gray-900">{{ $booking->returned_at?->format('d M Y') ?? '-' }}</p>
                                                        </div>
                                                        <div>
                                                            <span class="text-gray-600">Metode Pengiriman:</span>
                                                            <p class="font-semibold text-gray-900">{{ ucfirst($booking->detailBooks?->first()?->shipping_method ?? 'pickup') }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div>
                                                    <x-booking-status-card :booking="$booking" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                                @foreach($productReturnCompleted as $booking)
                                    <div class="bg-white rounded-xl shadow-sm hover:shadow-lg transition overflow-hidden border-l-4 border-emerald-400">
                                        <div class="p-6">
                                            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                                                <div class="md:col-span-2">
                                                    <div class="flex items-start space-x-4">
                                                        @if($booking->product && $booking->product->image)
                                                            <img src="{{ asset('storage/' . $booking->product->image) }}" alt="{{ $booking->product->name }}" class="w-20 h-20 rounded-lg object-cover">
                                                        @else
                                                            <div class="w-20 h-20 rounded-lg bg-gray-200 flex items-center justify-center">
                                                                <x-heroicon-o-photo class="h-8 w-8 text-gray-400" />
                                                            </div>
                                                        @endif
                                                        <div class="flex-1">
                                                            <h3 class="text-lg font-bold text-gray-900">{{ $booking->product->name ?? 'Produk Dihapus' }}</h3>
                                                            <p class="text-sm text-gray-600 mb-2">Booking Code: <span class="font-mono font-semibold text-emerald-600">{{ $booking->book_code }}</span></p>
                                                            <p class="text-sm text-gray-600">Penyewa: {{ $booking->detailBookProduct?->full_name ?? $booking->booker_name }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div>
                                                    <h4 class="text-sm font-semibold text-gray-700 mb-3">Detail Pengembalian</h4>
                                                    <div class="space-y-2 text-sm">
                                                        <div>
                                                            <span class="text-gray-600">Tanggal Kembali:</span>
                                                            <p class="font-semibold text-gray-900">{{ $booking->returned_at?->format('d M Y') ?? '-' }}</p>
                                                        </div>
                                                        <div>
                                                            <span class="text-gray-600">Metode Pengiriman:</span>
                                                            <p class="font-semibold text-gray-900">{{ ucfirst($booking->detailBookProduct?->shipping_method ?? 'pickup') }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div>
                                                    <x-booking-status-card :booking="$booking" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <div class="mt-8">
                    <a href="{{ route('profile.show') }}" class="inline-flex items-center space-x-2 text-gray-600 hover:text-gray-800 transition">
                        <x-heroicon-o-arrow-left class="h-5 w-5" />
                        <span>Kembali ke Profil</span>
                    </a>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
