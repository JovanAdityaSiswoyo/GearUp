@extends('layouts.courier')

@section('title', 'Pengiriman')

@section('content')
<div class="min-h-screen bg-gray-50 py-8 px-4 lg:px-16">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center space-x-3 mb-2">
                <x-heroicon-o-truck class="h-8 w-8 text-green-600" />
                <h1 class="text-3xl font-bold text-gray-900">Pengiriman</h1>
            </div>
            <p class="text-gray-600">Kelola pengiriman barang kepada pelanggan</p>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm">Siap Diambil</p>
                        <p class="text-3xl font-bold text-green-600">{{ $readyForPickup }}</p>
                    </div>
                    <x-heroicon-o-inbox class="h-8 w-8 text-green-400" />
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm">Dalam Pengiriman</p>
                        <p class="text-3xl font-bold text-blue-600">{{ $outForDelivery }}</p>
                    </div>
                    <x-heroicon-o-truck class="h-8 w-8 text-blue-400" />
                </div>
            </div>
        </div>

        <!-- Delivery Tasks -->
        <div class="mb-12">
            <h2 class="text-2xl font-bold text-green-700 mb-4">📦 Pengiriman</h2>
            <div class="space-y-4">
                @forelse($deliveryBookings as $booking)
                <div class="bg-white rounded-lg shadow hover:shadow-lg transition border-l-4 border-green-500">
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
                                        <p class="text-sm text-gray-600">Kode: <span class="font-mono font-semibold text-green-600">{{ $booking->book_code }}</span></p>
                                        <p class="text-sm text-gray-600">📍 {{ $booking->booker_name }}</p>
                                        <p class="text-sm text-gray-600">📱 {{ $booking->booker_telp }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Delivery Info -->
                            <div>
                                <h4 class="text-xs font-semibold text-gray-700 mb-2 uppercase">Info Pengiriman</h4>
                                <div class="space-y-1 text-sm">
                                    <p class="text-gray-600">Tanggal Pengambilan:</p>
                                    <p class="font-semibold">{{ $booking->checkin_appointment_start->format('d M Y') }}</p>
                                    <p class="text-gray-600 mt-2">Perlu Dikembalikan:</p>
                                    <p class="font-semibold">{{ $booking->checkout_appointment_end->format('d M Y') }}</p>
                                </div>
                            </div>

                            <!-- Status & Actions -->
                            <div>
                                <h4 class="text-xs font-semibold text-gray-700 mb-2 uppercase">Status</h4>
                                <p class="text-xs font-semibold mb-3 
                                    @if($booking->order_status->value == 'Ready for Pickup') text-yellow-600
                                    @else text-blue-600
                                    @endif">
                                    {{ $booking->order_status->label() }}
                                </p>
                                <div class="space-y-2">
                                    @if($booking->order_status->value == 'Ready for Pickup')
                                        <button 
                                            onclick="courierPickupDelivery(this.dataset.id, this.dataset.type)" 
                                            data-id="{{ $booking->id }}"
                                            data-type="{{ $booking->product ? 'product' : 'package' }}"
                                            class="w-full bg-green-500 hover:bg-green-600 text-white text-xs font-medium py-2 px-3 rounded transition">
                                            🚚 Ambil Barang
                                        </button>
                                    @elseif($booking->order_status->value == 'Out for Delivery')
                                        <button 
                                            onclick="courierCompleteDelivery(this.dataset.id, this.dataset.type)" 
                                            data-id="{{ $booking->id }}"
                                            data-type="{{ $booking->product ? 'product' : 'package' }}"
                                            class="w-full bg-green-600 hover:bg-green-700 text-white text-xs font-medium py-2 px-3 rounded transition">
                                            ✓ Kirim ke User
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="bg-white rounded-lg shadow p-8 text-center">
                    <x-heroicon-o-inbox class="h-12 w-12 text-gray-300 mx-auto mb-3" />
                    <p class="text-gray-600">Tidak ada pengiriman saat ini</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function openPhotoUploadDialog(title, message, onPhotoSelected) {
    // Generate unique ID untuk menghindari konflik element
    const uniqueId = 'photoInput_' + Date.now();
    
    Swal.fire({
        title: title,
        html: `
            <div style="text-align: left;">
                <p style="margin-bottom: 15px; color: #666;">${message}</p>
                <label style="display: block;">
                    <input type="file" id="${uniqueId}" accept="image/*" style="display: none;">
                    <div id="uploadArea_${uniqueId}" style="border: 2px dashed #3B82F6; border-radius: 8px; padding: 20px; cursor: pointer; text-align: center; background: #F0F9FF; transition: all 0.3s;">
                        <div id="photoPreview_${uniqueId}" style="display: none; margin-bottom: 10px;">
                            <img id="previewImage_${uniqueId}" style="max-width: 200px; max-height: 200px; border-radius: 4px;">
                        </div>
                        <div id="uploadPrompt_${uniqueId}">
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
        didOpen: () => {
            // Setup setelah Swal terbuka
            const fileInput = document.getElementById(uniqueId);
            const uploadArea = document.getElementById(`uploadArea_${uniqueId}`);
            const photoPreview = document.getElementById(`photoPreview_${uniqueId}`);
            const previewImage = document.getElementById(`previewImage_${uniqueId}`);
            const uploadPrompt = document.getElementById(`uploadPrompt_${uniqueId}`);

            // Handle file selection via label (natural behavior)
            fileInput.addEventListener('change', function() {
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

            // Handle click on upload area (only trigger once)
            uploadArea.addEventListener('click', function(e) {
                // Prevent double trigger: only click if not already handled by label
                if (e.target === uploadArea || e.target.closest('#uploadPrompt_' + uniqueId)) {
                    e.preventDefault();
                    e.stopPropagation();
                    fileInput.click();
                }
            });
        },
        preConfirm: () => {
            const fileInput = document.getElementById(uniqueId);
            if (!fileInput.files || !fileInput.files[0]) {
                Swal.showValidationMessage('Silakan pilih foto terlebih dahulu');
                return false;
            }
            return fileInput.files[0];
        },
        didDestroy: () => {
            // Cleanup: reset file input untuk memungkinkan upload file yang sama lagi
            const fileInput = document.getElementById(uniqueId);
            if (fileInput) fileInput.value = '';
        }
    }).then((result) => {
        if (result.isConfirmed && result.value) {
            onPhotoSelected(result.value);
        }
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

function courierPickupReturn(bookingId, type) {
    openPhotoUploadDialog(
        'Foto Penjemputan Kembali',
        'Ambil foto saat mengambil barang kembali dari user',
        (file) => {
            const formData = new FormData();
            formData.append('photo', file);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
            
            fetch(`/book-${type}s/${bookingId}/courier/pickup-return`, {
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

function courierCompleteReturn(bookingId, type) {
    openPhotoUploadDialog(
        'Foto Pengembalian',
        'Ambil foto saat mengembalikan barang ke gudang',
        (file) => {
            const formData = new FormData();
            formData.append('photo', file);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
            
            fetch(`/book-${type}s/${bookingId}/courier/complete-return`, {
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
