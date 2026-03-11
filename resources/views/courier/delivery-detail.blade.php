@extends('layouts.courier')

@section('title', 'Detail Pengiriman')

@section('content')
@php
    $status = $booking->order_status->value;
    $itemStatus = $booking->item_status->value;
    $isReady = in_array($status, ['Ready for Pickup', 'Out for Delivery', 'Delivered']);
    $isOut = in_array($status, ['Out for Delivery', 'Delivered']);
    $isDelivered = $status === 'Delivered';
@endphp

<div class="min-h-screen bg-gray-50 py-8 px-4 lg:px-16">
    <div class="max-w-4xl mx-auto">
        <!-- Breadcrumb -->
        <div class="mb-6 flex items-center space-x-2 text-sm text-gray-600">
            <a href="{{ route('courier.deliveries.index') }}" class="hover:text-gray-900">📦 Pengiriman</a>
            <span>→</span>
            <span>Detail Pengiriman</span>
        </div>

        <!-- Header -->
        <div class="flex items-start justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Detail Pengiriman</h1>
                <p class="text-gray-600">Kode Booking: <span class="font-mono font-bold text-green-600">{{ $booking->book_code }}</span></p>
            </div>
            <span class="px-4 py-2 rounded-full text-sm font-semibold {{ 
                $status == 'Ready for Pickup' ? 'bg-yellow-100 text-yellow-800' : (
                $status == 'Out for Delivery' ? 'bg-blue-100 text-blue-800' : (
                $status == 'Delivered' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'))
            }}">
                {{ $booking->order_status->label() }}
            </span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Barang -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">📦 Barang yang Dikirim</h2>
                    <div class="flex items-start space-x-4">
                        @php
                            $productImage = $booking->product?->image;
                            $packageImage = $booking->package?->image;
                            $itemName = $booking->product?->name ?? $booking->package?->name_package ?? 'Barang Dihapus';
                        @endphp
                        
                        @if($productImage)
                            <img src="{{ asset('storage/' . $productImage) }}" alt="{{ $booking->product->name }}" class="w-24 h-24 rounded-lg object-cover">
                        @elseif($packageImage)
                            <img src="{{ asset('storage/' . $packageImage) }}" alt="{{ $booking->package->name_package }}" class="w-24 h-24 rounded-lg object-cover">
                        @else
                            <div class="w-24 h-24 rounded-lg bg-gray-200 flex items-center justify-center">
                                <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                        @endif
                        
                        <div class="flex-1">
                            <h3 class="text-xl font-bold text-gray-900">{{ $itemName }}</h3>
                            @if($booking->product)
                                <p class="text-gray-600">Kategori: {{ $booking->product->category->name ?? 'N/A' }}</p>
                                <p class="text-gray-600">Merek: {{ $booking->product->brand->name ?? 'N/A' }}</p>
                            @endif
                            <p class="text-sm text-gray-600 mt-2">Jumlah: <strong>{{ $booking->amount }} unit</strong></p>
                        </div>
                    </div>
                </div>

                <!-- Penerima -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">👤 Informasi Penerima</h2>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Nama Penerima</p>
                            <p class="text-lg font-semibold text-gray-900">{{ $booking->booker_name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Email</p>
                            <p class="text-lg font-semibold text-gray-900">{{ $booking->booker_email }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Nomor Telepon</p>
                            <p class="text-lg font-semibold text-gray-900">{{ $booking->booker_telp }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Jumlah Unit</p>
                            <p class="text-lg font-semibold text-gray-900">{{ $booking->amount }} unit</p>
                        </div>
                    </div>
                </div>

                <!-- Jadwal -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">📅 Jadwal Sewa</h2>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <p class="text-sm text-gray-600 mb-1">Tanggal Pengambilan</p>
                            <p class="text-lg font-bold text-blue-600">{{ $booking->checkin_appointment_start->format('d M Y') }}</p>
                            <p class="text-xs text-gray-600 mt-1">{{ $booking->checkin_appointment_start->format('H:i') }}</p>
                        </div>
                        <div class="bg-red-50 p-4 rounded-lg">
                            <p class="text-sm text-gray-600 mb-1">Tanggal Pengembalian</p>
                            <p class="text-lg font-bold text-red-600">{{ $booking->checkout_appointment_end->format('d M Y') }}</p>
                            <p class="text-xs text-gray-600 mt-1">{{ $booking->checkout_appointment_end->format('H:i') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Timeline Status -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">📊 Status Timeline</h2>
                    <div class="space-y-4">
                        <!-- Created -->
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center">
                                <svg class="h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold text-gray-900">Booking Dibuat</p>
                                <p class="text-sm text-gray-600">{{ $booking->created_at->format('d M Y H:i') }}</p>
                            </div>
                        </div>

                        <!-- Confirmed -->
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-blue-200 flex items-center justify-center">
                                <svg class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold text-gray-900">Order Terkonfirmasi</p>
                                <p class="text-sm text-gray-600">Menunggu persiapan barang...</p>
                            </div>
                        </div>

                        <!-- Ready for Pickup -->
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0 w-10 h-10 rounded-full {{ $isReady ? 'bg-green-200' : 'bg-gray-200' }} flex items-center justify-center">
                                <svg class="h-5 w-5 {{ $isReady ? 'text-green-600' : 'text-gray-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold text-gray-900">Siap Diambil Kurir</p>
                                <p class="text-sm text-gray-600">Barang sudah dikemas dan siap diambil</p>
                            </div>
                        </div>

                        <!-- Out for Delivery -->
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0 w-10 h-10 rounded-full {{ $isOut ? 'bg-green-200' : 'bg-gray-200' }} flex items-center justify-center">
                                <svg class="h-5 w-5 {{ $isOut ? 'text-green-600' : 'text-gray-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold text-gray-900">Dalam Pengiriman</p>
                                <p class="text-sm text-gray-600">
                                    {{ $booking->delivery_at ? $booking->delivery_at->format('d M Y H:i') : 'Menunggu pengambilan...' }}
                                </p>
                            </div>
                        </div>

                        <!-- Delivered -->
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0 w-10 h-10 rounded-full {{ $isDelivered ? 'bg-green-200' : 'bg-gray-200' }} flex items-center justify-center">
                                <svg class="h-5 w-5 {{ $isDelivered ? 'text-green-600' : 'text-gray-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold text-gray-900">Terkirim</p>
                                <p class="text-sm text-gray-600">Barang sampai ke tangan penerima</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar Actions -->
            <div class="space-y-6">
                <!-- Status Card -->
                <div class="bg-white rounded-lg shadow p-6 sticky top-24">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">📍 Status Saat Ini</h3>
                    
                    <div class="mb-6">
                        <p class="text-xs text-gray-600 mb-2">Order Status</p>
                        <span class="px-3 py-1 inline-block text-sm font-semibold rounded-full {{ 
                            $status == 'Ready for Pickup' ? 'bg-yellow-100 text-yellow-800' : (
                            $status == 'Out for Delivery' ? 'bg-blue-100 text-blue-800' : (
                            $status == 'Delivered' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'))
                        }}">
                            {{ $booking->order_status->label() }}
                        </span>
                    </div>

                    <div class="mb-6">
                        <p class="text-xs text-gray-600 mb-2">Item Status</p>
                        <span class="px-3 py-1 inline-block text-sm font-semibold rounded-full {{ 
                            $itemStatus == 'Packing' ? 'bg-blue-100 text-blue-800' : (
                            $itemStatus == 'Picked-Up' ? 'bg-blue-100 text-blue-800' : (
                            $itemStatus == 'Deployed' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'))
                        }}">
                            {{ $booking->item_status->label() }}
                        </span>
                    </div>

                    <hr class="my-6">

                    <!-- Actions -->
                    <div class="space-y-3">
                        @if($status == 'Ready for Pickup')
                        <button onclick="courierPickupDelivery({{ $booking->id }}, '{{ $booking->product ? 'product' : 'package' }}')" 
                            class="w-full bg-green-500 hover:bg-green-600 text-white font-semibold py-3 px-4 rounded-lg transition">
                            🚚 Ambil Barang untuk Dikirim
                        </button>
                        @elseif($status == 'Out for Delivery')
                        <button onclick="courierCompleteDelivery({{ $booking->id }}, '{{ $booking->product ? 'product' : 'package' }}')" 
                            class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-4 rounded-lg transition">
                            ✓ Konfirmasi Pengiriman
                        </button>
                        @endif
                    </div>
                </div>

                <!-- Info Box -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                    <h4 class="font-semibold text-blue-900 mb-3">ℹ️ Informasi Penting</h4>
                    <ul class="space-y-2 text-sm text-blue-900">
                        <li class="flex items-start space-x-2">
                            <span class="mt-0.5">•</span>
                            <span>Ambil foto saat mengambil barang</span>
                        </li>
                        <li class="flex items-start space-x-2">
                            <span class="mt-0.5">•</span>
                            <span>Pastikan barang dalam kondisi baik</span>
                        </li>
                        <li class="flex items-start space-x-2">
                            <span class="mt-0.5">•</span>
                            <span>Hubungi penerima jika ada kendala</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Back Button -->
        <div class="mt-8">
            <a href="{{ route('courier.deliveries.index') }}" class="inline-flex items-center space-x-2 text-gray-600 hover:text-gray-900 transition">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>Kembali ke Pengiriman</span>
            </a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function openPhotoUploadDialog(title, message, onPhotoSelected) {
    Swal.fire({
        title: title,
        html: `
            <div style="text-align: left;">
                <p style="margin-bottom: 15px; color: #666;">${message}</p>
                <label style="display: block;">
                    <input type="file" id="photoInput" accept="image/*" style="display: none;">
                    <div style="border: 2px dashed #3B82F6; border-radius: 8px; padding: 20px; cursor: pointer; text-align: center; background: #F0F9FF; transition: all 0.3s;">
                        <div id="photoPreview" style="display: none; margin-bottom: 10px;">
                            <img id="previewImage" style="max-width: 200px; max-height: 200px; border-radius: 4px;">
                        </div>
                        <div id="uploadPrompt">
                            <div style="font-size: 24px; margin-bottom: 8px;">📷</div>
                            <p style="margin: 0; color: #3B82F6; font-weight: bold;">Klik untuk memilih foto</p>
                            <p style="margin: 5px 0 0 0; color: #6B7280; font-size: 12px;">atau drag & drop</p>
                        </div>
                    </div>
                </label>
            </div>
        `,
        showCancelButton: true,
        confirmButtonColor: '#22C55E',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Upload Foto',
        preConfirm: () => {
            const fileInput = document.getElementById('photoInput');
            if (!fileInput.files || !fileInput.files[0]) {
                Swal.showValidationMessage('Silakan pilih foto terlebih dahulu');
                return false;
            }
            return fileInput.files[0];
        }
    }).then((result) => {
        if (result.isConfirmed) {
            onPhotoSelected(result.value);
        }
    });

    const photoInput = document.getElementById('photoInput');
    const uploadPrompt = document.getElementById('uploadPrompt');
    const photoPreview = document.getElementById('photoPreview');
    const previewImage = document.getElementById('previewImage');

    photoInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImage.src = e.target.result;
                photoPreview.style.display = 'block';
                uploadPrompt.style.display = 'none';
            };
            reader.readAsDataURL(this.files[0]);
        }
    });

    document.querySelector('[style*="border: 2px dashed"]').addEventListener('click', function() {
        photoInput.click();
    });
}

function courierPickupDelivery(bookingId, type) {
    openPhotoUploadDialog(
        'Foto Pengambilan Barang',
        'Ambil foto saat mengambil barang untuk pengiriman',
        (file) => {
            const formData = new FormData();
            formData.append('photo', file);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
            
            fetch(`/book-${type}s/${bookingId}/courier/pickup-delivery`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            }).then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Berhasil', data.message, 'success').then(() => location.reload());
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            });
        }
    );
}

function courierCompleteDelivery(bookingId, type) {
    openPhotoUploadDialog(
        'Foto Pengiriman',
        'Ambil foto saat barang diserahkan ke user',
        (file) => {
            const formData = new FormData();
            formData.append('photo', file);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
            
            fetch(`/book-${type}s/${bookingId}/courier/complete-delivery`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            }).then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Berhasil', data.message, 'success').then(() => location.reload());
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            });
        }
    );
}
</script>
@endsection
