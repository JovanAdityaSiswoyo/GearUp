@php
    use App\Enums\OrderStatus;
@endphp

@if(auth()->check())
    @if(auth()->user()->hasRole('officer'))
        <!-- Officer Status Controls -->
        <div class="space-y-2 mt-4 pt-4 border-t">
            <!-- Validate Order -->
            @if($booking->order_status == OrderStatus::DRAFT)
            <button onclick="validateOrder({{ $booking->id }}, 'product')" class="w-full bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-medium py-2 px-3 rounded-lg transition">
                ✓ Validasi Order
            </button>
            @endif

            <!-- Confirm Order -->
            @if($booking->order_status == OrderStatus::AWAITING_VALIDATION)
            <button onclick="confirmOrder({{ $booking->id }}, 'product')" class="w-full bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium py-2 px-3 rounded-lg transition">
                ✓ Konfirmasi Order
            </button>
            @endif

            <!-- Prepare for Pickup -->
            @if($booking->order_status == OrderStatus::CONFIRMED)
            <button onclick="preparePickup({{ $booking->id }}, 'product')" class="w-full bg-green-500 hover:bg-green-600 text-white text-sm font-medium py-2 px-3 rounded-lg transition">
                📦 Siapkan Pengambilan
            </button>
            @endif

            <!-- Schedule Return -->
            @if($booking->order_status == OrderStatus::DELIVERED)
            <button onclick="scheduleReturn({{ $booking->id }}, 'product')" class="w-full bg-purple-500 hover:bg-purple-600 text-white text-sm font-medium py-2 px-3 rounded-lg transition">
                📅 Jadwalkan Penjemputan
            </button>
            @endif

            <!-- Complete Order -->
            @if($booking->order_status == OrderStatus::PENDING_REVIEW)
            <div class="space-y-2">
                <button onclick="completeOrder({{ $booking->id }}, 'product')" class="w-full bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-medium py-2 px-3 rounded-lg transition">
                    ✓ Selesaikan Order
                </button>
                <button onclick="detectIssue({{ $booking->id }}, 'product')" class="w-full bg-red-500 hover:bg-red-600 text-white text-sm font-medium py-2 px-3 rounded-lg transition">
                    ⚠️ Deteksi Masalah
                </button>
            </div>
            @endif

            <!-- Cancel Order -->
            @if($booking->order_status->isActive() && !in_array($booking->order_status, [OrderStatus::OUT_FOR_DELIVERY, OrderStatus::ON_PROCESS_RETURN]))
            <button onclick="cancelOrder({{ $booking->id }}, 'product')" class="w-full bg-gray-500 hover:bg-gray-600 text-white text-sm font-medium py-2 px-3 rounded-lg transition">
                ✕ Batalkan
            </button>
            @endif
        </div>

    @elseif(auth()->user()->hasRole('courier'))
        <!-- Courier Status Controls (Hanya untuk delivery/return) -->
        <div class="space-y-2 mt-4 pt-4 border-t">
            @if($booking->order_status == OrderStatus::READY_FOR_PICKUP && $booking->id_courier == auth()->user()->courier?->id)
            <button onclick="courierPickupDelivery({{ $booking->id }}, 'product')" class="w-full bg-green-500 hover:bg-green-600 text-white text-sm font-medium py-2 px-3 rounded-lg transition">
                🚚 Ambil Barang untuk Dikirim
            </button>
            @endif

            @if($booking->order_status == OrderStatus::OUT_FOR_DELIVERY && $booking->id_courier == auth()->user()->courier?->id)
            <button onclick="courierCompleteDelivery({{ $booking->id }}, 'product')" class="w-full bg-green-600 hover:bg-green-700 text-white text-sm font-medium py-2 px-3 rounded-lg transition">
                ✓ Konfirmasi Pengiriman
            </button>
            @endif

            @if($booking->order_status == OrderStatus::PICKUP_SCHEDULED && $booking->id_courier == auth()->user()->courier?->id)
            <button onclick="courierPickupReturn({{ $booking->id }}, 'product')" class="w-full bg-orange-500 hover:bg-orange-600 text-white text-sm font-medium py-2 px-3 rounded-lg transition">
                📦 Ambil Barang untuk Dikembalikan
            </button>
            @endif

            @if($booking->order_status == OrderStatus::ON_PROCESS_RETURN && $booking->id_courier == auth()->user()->courier?->id)
            <button onclick="courierCompleteReturn({{ $booking->id }}, 'product')" class="w-full bg-orange-600 hover:bg-orange-700 text-white text-sm font-medium py-2 px-3 rounded-lg transition">
                ✓ Konfirmasi Pengembalian
            </button>
            @endif

            @if(!in_array($booking->order_status, [OrderStatus::READY_FOR_PICKUP, OrderStatus::OUT_FOR_DELIVERY, OrderStatus::PICKUP_SCHEDULED, OrderStatus::ON_PROCESS_RETURN]))
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                <p class="text-xs text-blue-800">Menunggu status untuk aksi kurir...</p>
            </div>
            @endif
        </div>
    @endif
@endif

<script>
function validateOrder(bookingId, type) {
    Swal.fire({
        title: 'Validasi Order',
        text: 'Pastikan barang tersedia sebelum memvalidasi',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#EAB308',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Ya, Validasi'
    }).then((result) => {
        if (result.isConfirmed) {
            submitStatusChange(`/book-${type}s/${bookingId}/validate`);
        }
    });
}

function confirmOrder(bookingId, type) {
    Swal.fire({
        title: 'Konfirmasi Order',
        text: 'Order akan dikonfirmasi dan stok akan dipotong',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3B82F6',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Ya, Konfirmasi'
    }).then((result) => {
        if (result.isConfirmed) {
            submitStatusChange(`/book-${type}s/${bookingId}/confirm`);
        }
    });
}

function preparePickup(bookingId, type) {
    Swal.fire({
        title: 'Siapkan Pengambilan',
        text: 'Barang akan dipacking dan siap diambil kurir',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#22C55E',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Ya, Siapkan'
    }).then((result) => {
        if (result.isConfirmed) {
            submitStatusChange(`/book-${type}s/${bookingId}/prepare-pickup`);
        }
    });
}

function scheduleReturn(bookingId, type) {
    Swal.fire({
        title: 'Jadwalkan Penjemputan',
        text: 'Kurir akan dijadwalkan untuk mengambil barang',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#A855F7',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Ya, Jadwalkan'
    }).then((result) => {
        if (result.isConfirmed) {
            submitStatusChange(`/book-${type}s/${bookingId}/schedule-return`);
        }
    });
}

function completeOrder(bookingId, type) {
    Swal.fire({
        title: 'Selesaikan Order',
        html: '<p>Barang sudah dikembalikan dengan lengkap dan tanpa masalah</p>',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10B981',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Ya, Selesaikan'
    }).then((result) => {
        if (result.isConfirmed) {
            submitStatusChange(`/book-${type}s/${bookingId}/complete`);
        }
    });
}

function detectIssue(bookingId, type) {
    Swal.fire({
        title: 'Deteksi Masalah',
        html: `
            <div style="text-align: left;">
                <label style="display: block; margin-bottom: 10px;">
                    <span style="font-weight: bold; color: #333;">Catatan Masalah:</span>
                    <textarea id="issue_notes" style="width: 100%; margin-top: 8px; padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-family: inherit;" 
                        placeholder="Jelaskan masalah yang ditemukan..." rows="4"></textarea>
                </label>
            </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#EF4444',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Ya, Catat Masalah'
    }).then((result) => {
        if (result.isConfirmed) {
            const notes = document.getElementById('issue_notes').value;
            if (!notes.trim()) {
                Swal.fire('Error', 'Catatan masalah harus diisi', 'error');
                return;
            }
            
            const formData = new FormData();
            formData.append('issue_notes', notes);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
            
            fetch(`/book-${type}s/${bookingId}/detect-issue`, {
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
    });
}

function cancelOrder(bookingId, type) {
    Swal.fire({
        title: 'Batalkan Order',
        html: `
            <div style="text-align: left;">
                <label style="display: block; margin-bottom: 10px;">
                    <span style="font-weight: bold; color: #333;">Alasan Pembatalan:</span>
                    <textarea id="cancel_reason" style="width: 100%; margin-top: 8px; padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-family: inherit;" 
                        placeholder="Jelaskan alasan pembatalan..." rows="3"></textarea>
                </label>
            </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#6B7280',
        cancelButtonColor: '#D1D5DB',
        confirmButtonText: 'Ya, Batalkan'
    }).then((result) => {
        if (result.isConfirmed) {
            const reason = document.getElementById('cancel_reason').value;
            if (!reason.trim()) {
                Swal.fire('Error', 'Alasan pembatalan harus diisi', 'error');
                return;
            }
            
            const formData = new FormData();
            formData.append('reason', reason);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
            
            fetch(`/book-${type}s/${bookingId}/cancel`, {
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

    // Setup file input
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

    // Make the whole div clickable
    document.querySelector('[style*="border: 2px dashed"]').addEventListener('click', function() {
        photoInput.click();
    });
}

function submitStatusChange(url) {
    fetch(url, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    }).then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire('Berhasil', data.message, 'success').then(() => location.reload());
        } else {
            Swal.fire('Error', data.message, 'error');
        }
    })
    .catch(error => {
        Swal.fire('Error', 'Terjadi kesalahan: ' + error.message, 'error');
    });
}
</script>
