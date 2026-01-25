
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Saya - GearUp</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-50">
    @include('layouts.app')
    @include('sweetalert::alert')

    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Booking Berhasil!',
                    html: `<div style="text-align: left; color: #666; margin-top: 10px; line-height: 1.6;">
                        <p style="margin-bottom: 10px;">{{ session('success') }}</p>
                        <div style="background: #f0fdf4; padding: 12px; border-radius: 8px; border-left: 4px solid #22c55e;">
                            <p style="margin: 0; font-size: 14px;"><strong>📋 Booking Code:</strong> Lihat di daftar booking Anda</p>
                            <p style="margin: 8px 0 0 0; font-size: 14px;"><strong>📞 Hubungi Kami:</strong> 0877 7603 4179</p>
                        </div>
                    </div>`,
                    confirmButtonText: 'Lihat Booking Saya',
                    confirmButtonColor: '#22c55e',
                    allowOutsideClick: false
                });
            });
        </script>
    @endif

    <div class="min-h-screen flex flex-col">
        <!-- Header/Navbar -->
        

        <!-- Main Content -->
        <main class="flex-1 px-6 lg:px-16 py-8">

            <div class="max-w-6xl mx-auto">
                <!-- Page Header -->
                <div class="mb-8">
                    <div class="flex items-center space-x-3 mb-2">
                        <x-heroicon-o-calendar class="h-8 w-8 text-green-600" />
                        <h1 class="text-3xl lg:text-4xl font-bold text-gray-900">Booking Saya</h1>
                    </div>
                    <p class="text-gray-600">Lihat semua booking dan status penyewaan Anda</p>
                </div>

                @if((isset($packageBookings) && $packageBookings->isNotEmpty()) || ($bookings && $bookings->isNotEmpty()))
                    <!-- Bookings Grid -->
                    <div class="space-y-12">
                        @if(isset($packageBookings) && $packageBookings->isNotEmpty())
                        <div>
                            <h2 class="text-2xl font-bold text-blue-700 mb-4">History Booking Paket</h2>
                            <div class="space-y-6">
                                @foreach($packageBookings as $booking)
                                    <div class="bg-white rounded-xl shadow-sm hover:shadow-lg transition overflow-hidden border-l-4 border-blue-500">
                                        <div class="p-6">
                                            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                                                <!-- Package Info -->
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
                                                            <p class="text-sm text-gray-600 mb-2">Booking Code: <span class="font-mono font-semibold text-green-600">{{ $booking->book_code }}</span></p>
                                                            <p class="text-sm text-gray-600">Penyewa: {{ $booking->booker_name }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Booking Details -->
                                                <div>
                                                    <h4 class="text-sm font-semibold text-gray-700 mb-3">Detail Sewa Paket</h4>
                                                    <div class="space-y-2 text-sm">
                                                        <div>
                                                            <span class="text-gray-600">Mulai:</span>
                                                            <p class="font-semibold text-gray-900">{{ $booking->checkin_appointment_start?->format('d M Y') }}</p>
                                                        </div>
                                                        <div>
                                                            <span class="text-gray-600">Berakhir:</span>
                                                            <p class="font-semibold text-gray-900">{{ $booking->checkout_appointment_end?->format('d M Y') }}</p>
                                                        </div>
                                                        <div>
                                                            <span class="text-gray-600">Jumlah Unit:</span>
                                                            <p class="font-semibold text-gray-900">1 paket</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Status & Actions -->
                                                <div>
                                                    <h4 class="text-sm font-semibold text-gray-700 mb-3">Status</h4>
                                                    <div class="flex flex-col items-start space-y-3">
                                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                            {{ ucfirst($booking->status) }}
                                                        </span>
                                                        @if($booking->status === 'pending')
                                                            <p class="text-xs text-gray-600">Menunggu konfirmasi...</p>
                                                        @elseif($booking->status === 'confirmed')
                                                            <p class="text-xs text-gray-600">Siap untuk diambil</p>
                                                        @elseif($booking->status === 'active')
                                                            <p class="text-xs text-green-600 font-medium">Sedang disewa</p>
                                                        @elseif($booking->status === 'completed')
                                                            <p class="text-xs text-gray-600">Penyewaan selesai</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Contact Info -->
                                            <div class="mt-6 pt-6 border-t">
                                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                                    <div>
                                                        <p class="text-xs text-gray-600 mb-1">Kontak Pemesan</p>
                                                        <p class="text-sm font-semibold text-gray-900">{{ $booking->booker_email }}</p>
                                                    </div>
                                                    <div>
                                                        <p class="text-xs text-gray-600 mb-1">Telepon</p>
                                                        <p class="text-sm font-semibold text-gray-900">{{ $booking->booker_telp }}</p>
                                                    </div>
                                                    @if($booking->detailBooks && $booking->detailBooks->count())
                                                        <div>
                                                            <p class="text-xs text-gray-600 mb-1">Metode Pengiriman</p>
                                                            <p class="text-sm font-semibold text-gray-900">{{ ucfirst($booking->detailBooks->first()->shipping_method) }}</p>
                                                        </div>
                                                        <div>
                                                            <p class="text-xs text-gray-600 mb-1">Tanggal Pengiriman</p>
                                                            <p class="text-sm font-semibold text-gray-900">{{ $booking->detailBooks->first()->shipping_date?->format('d M Y') }}</p>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        @if($bookings && $bookings->isNotEmpty())
                        <div>
                            <h2 class="text-2xl font-bold text-green-700 mb-4">History Booking Produk</h2>
                            <div class="space-y-6">
                                @foreach($bookings as $booking)
                                    <div class="bg-white rounded-xl shadow-sm hover:shadow-lg transition overflow-hidden border-l-4 
                                        @if($booking->status === 'pending') border-yellow-500
                                        @elseif($booking->status === 'confirmed') border-blue-500
                                        @elseif($booking->status === 'active') border-green-500
                                        @elseif($booking->status === 'completed') border-gray-500
                                        @else border-red-500
                                        @endif">
                                        <div class="p-6">
                                            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                                                <!-- Product Info -->
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
                                                            <h3 class="text-lg font-bold text-gray-900">{{ $booking->product->name ?? 'Product Deleted' }}</h3>
                                                            <p class="text-sm text-gray-600 mb-2">Booking Code: <span class="font-mono font-semibold text-green-600">{{ $booking->book_code }}</span></p>
                                                            <p class="text-sm text-gray-600">Penyewa: {{ $booking->detailBookProduct?->full_name ?? $booking->booker_name }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Booking Details -->
                                                <div>
                                                    <h4 class="text-sm font-semibold text-gray-700 mb-3">Detail Sewa</h4>
                                                    <div class="space-y-2 text-sm">
                                                        <div>
                                                            <span class="text-gray-600">Mulai:</span>
                                                            <p class="font-semibold text-gray-900">{{ $booking->detailBookProduct?->rental_start_at?->format('d M Y') ?? $booking->checkin_appointment_start->format('d M Y') }}</p>
                                                        </div>
                                                        <div>
                                                            <span class="text-gray-600">Berakhir:</span>
                                                            <p class="font-semibold text-gray-900">{{ $booking->detailBookProduct?->rental_end_at?->format('d M Y') ?? $booking->checkout_appointment_end->format('d M Y') }}</p>
                                                        </div>
                                                        <div>
                                                            <span class="text-gray-600">Jumlah Unit:</span>
                                                            <p class="font-semibold text-gray-900">{{ $booking->amount }} unit</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Status & Actions -->
                                                <div>
                                                    <h4 class="text-sm font-semibold text-gray-700 mb-3">Status</h4>
                                                    <div class="flex flex-col items-start space-y-3">
                                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                            @if($booking->status === 'pending') bg-yellow-100 text-yellow-800
                                                            @elseif($booking->status === 'confirmed') bg-blue-100 text-blue-800
                                                            @elseif($booking->status === 'active') bg-green-100 text-green-800
                                                            @elseif($booking->status === 'completed') bg-gray-100 text-gray-800
                                                            @else bg-red-100 text-red-800
                                                            @endif">
                                                            {{ ucfirst($booking->status) }}
                                                        </span>
                                                        @if($booking->status === 'pending')
                                                            <p class="text-xs text-gray-600">Menunggu konfirmasi...</p>
                                                        @elseif($booking->status === 'confirmed')
                                                            <p class="text-xs text-gray-600">Siap untuk diambil</p>
                                                        @elseif($booking->status === 'active')
                                                            <p class="text-xs text-green-600 font-medium">Sedang disewa</p>
                                                        @elseif($booking->status === 'completed')
                                                            <p class="text-xs text-gray-600">Penyewaan selesai</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Contact Info -->
                                            <div class="mt-6 pt-6 border-t">
                                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                                    <div>
                                                        <p class="text-xs text-gray-600 mb-1">Kontak Pemesan</p>
                                                        <p class="text-sm font-semibold text-gray-900">{{ $booking->booker_email }}</p>
                                                    </div>
                                                    <div>
                                                        <p class="text-xs text-gray-600 mb-1">Telepon</p>
                                                        <p class="text-sm font-semibold text-gray-900">{{ $booking->booker_telp }}</p>
                                                    </div>
                                                    @if($booking->detailBookProduct)
                                                        <div>
                                                            <p class="text-xs text-gray-600 mb-1">Metode Pengiriman</p>
                                                            <p class="text-sm font-semibold text-gray-900">{{ ucfirst($booking->detailBookProduct->shipping_method) }}</p>
                                                        </div>
                                                        <div>
                                                            <p class="text-xs text-gray-600 mb-1">Tanggal Pengiriman</p>
                                                            <p class="text-sm font-semibold text-gray-900">{{ $booking->detailBookProduct->shipping_date->format('d M Y') }}</p>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                @else
                    <!-- Empty State -->
                    <div class="bg-white rounded-xl shadow-sm p-12 text-center">
                        <x-heroicon-o-inbox class="h-16 w-16 text-gray-300 mx-auto mb-4" />
                        <h3 class="text-xl font-semibold text-gray-800 mb-2">Belum Ada Booking</h3>
                        <p class="text-gray-600 mb-6">Anda belum membuat booking apapun. Mari cari gear yang menarik!</p>
                        <a href="{{ route('home') }}" class="inline-flex items-center space-x-2 bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg transition">
                            <x-heroicon-o-arrow-left class="h-5 w-5" />
                            <span>Kembali ke Home</span>
                        </a>
                    </div>
                @endif

                <!-- Back Button -->
                <div class="mt-8">
                    <a href="{{ route('profile.show') }}" class="inline-flex items-center space-x-2 text-gray-600 hover:text-gray-800 transition">
                        <x-heroicon-o-arrow-left class="h-5 w-5" />
                        <span>Kembali ke Profil</span>
                    </a>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-black text-white py-16 px-6 lg:px-16 relative mt-16">
            <div class="max-w-7xl mx-auto">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-16">
                    <!-- Logo and Company Info -->
                    <div>
                        <img src="/gallery/Gearup.png" alt="GearUp Logo" class="h-40 w-auto mb-6">
                        
                        <div class="space-y-4 text-gray-400 text-sm">
                            <div class="flex items-start space-x-2">
                                <x-heroicon-o-map-pin class="h-5 w-5 flex-shrink-0 mt-0.5" />
                                <p>Jl. H. Jian No.38, RT.9/RW.3, Cipete Utara,<br>Kec. Kby. Baru, Kota Jakarta Selatan,<br>Daerah Khusus Ibukota Jakarta 12150</p>
                            </div>
                            
                            <div class="flex items-center space-x-2">
                                <x-heroicon-o-clock class="h-5 w-5 flex-shrink-0" />
                                <p>Setiap hari, 09:00 - 20:00 WIB</p>
                            </div>
                            
                            <div class="flex items-center space-x-2">
                                <x-heroicon-o-envelope class="h-5 w-5 flex-shrink-0" />
                                <p>gearup@gmail.com</p>
                            </div>
                        </div>
                    </div>

                    <!-- Informasi -->
                    <div>
                        <h3 class="text-xl font-semibold mb-6">Informasi</h3>
                        <ul class="space-y-3 text-gray-400">
                            <li><a href="#" class="hover:text-white transition">Cara sewa</a></li>
                            <li><a href="#" class="hover:text-white transition">Cara jadi member</a></li>
                            <li><a href="#" class="hover:text-white transition">Cara pengembalian</a></li>
                            <li><a href="#" class="hover:text-white transition">Syarat dan Ketentuan</a></li>
                        </ul>
                    </div>

                    <!-- Tentang GearUp -->
                    <div>
                        <h3 class="text-xl font-semibold mb-6">Tentang GearUp</h3>
                        <ul class="space-y-3 text-gray-400">
                            <li><a href="#" class="hover:text-white transition">Tentang Kami</a></li>
                        </ul>
                    </div>

                    <!-- Layanan Bantuan -->
                    <div>
                        <h3 class="text-xl font-semibold mb-6">Layanan Bantuan</h3>
                        <div class="text-gray-400 mb-4">
                            <p>Kontak Kami</p>
                        </div>
                        <div class="flex items-center space-x-2 mb-2">
                            <x-heroicon-o-phone class="h-5 w-5 text-gray-400" />
                            <span class="text-sm text-gray-400">WhatsApp Kami</span>
                        </div>
                        <a href="https://wa.me/6287812000155" class="text-green-500 text-2xl font-bold hover:text-green-400 transition">0878 1200 0155</a>
                    </div>
                </div>

                <!-- Bottom Bar -->
                <div class="border-t border-gray-800 pt-8">
                    <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                        <p class="text-gray-400 text-sm">© 2026 GearUp. All rights reserved</p>
                        
                        <div class="flex items-center space-x-4">
                            <span class="text-gray-400 text-sm">Follow us</span>
                            <a href="#" class="text-gray-400 hover:text-white transition">
                                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                            </a>
                            <a href="#" class="text-gray-400 hover:text-white transition">
                                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                            </a>
                            <a href="#" class="text-gray-400 hover:text-white transition">
                                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</body>
</html>
