@php
    use App\Enums\OrderStatus;
@endphp

@php
    $isOfficer = auth('officer')->check() || (auth()->check() && auth()->user() && method_exists(auth()->user(), 'hasRole') && auth()->user()->hasRole('officer'));
@endphp

@if($isOfficer)
    <!-- Officer Status Controls -->
    <div class="flex gap-1 items-center justify-start flex-wrap">
        @if($booking->order_status->value == 'Draft')
        <button onclick="validateOrder(@js((string) $booking->id), '{{ $type }}')" class="bg-yellow-500 hover:bg-yellow-600 text-white text-xs font-medium py-1 px-2 rounded transition whitespace-nowrap">
            Validasi
        </button>
        @endif

        @if($booking->order_status->value == 'Awaiting Validation')
        <button onclick="confirmOrder(@js((string) $booking->id), '{{ $type }}')" class="bg-blue-500 hover:bg-blue-600 text-white text-xs font-medium py-1 px-2 rounded transition whitespace-nowrap">
            Konfirmasi
        </button>
        @endif

        @if($booking->order_status->value == 'Confirmed')
        <button onclick="preparePickup(@js((string) $booking->id), '{{ $type }}')" class="bg-green-500 hover:bg-green-600 text-white text-xs font-medium py-1 px-2 rounded transition whitespace-nowrap">
            Siapkan
        </button>
        @endif

        @if(in_array($booking->order_status->value, ['Ready for Pickup', 'Out for Delivery']))
        <button onclick="handoverToUser(@js((string) $booking->id), '{{ $type }}')" class="bg-cyan-600 hover:bg-cyan-700 text-white text-xs font-medium py-1 px-2 rounded transition whitespace-nowrap">
            Serah ke User
        </button>
        @endif

        @if($booking->order_status->value == 'Delivered')
        <button onclick="scheduleReturn(@js((string) $booking->id), '{{ $type }}')" class="bg-purple-500 hover:bg-purple-600 text-white text-xs font-medium py-1 px-2 rounded transition whitespace-nowrap">
            Jadwalkan Kembali
        </button>
        @endif

        @if(in_array($booking->order_status->value, ['Pickup Scheduled', 'On Process Return']))
        <button onclick="receiveReturn(@js((string) $booking->id), '{{ $type }}')" class="bg-orange-500 hover:bg-orange-600 text-white text-xs font-medium py-1 px-2 rounded transition whitespace-nowrap">
            Terima Kembali
        </button>
        @endif

        @if($booking->order_status->value == 'Pending Review')
        <button onclick="completeOrder(@js((string) $booking->id), '{{ $type }}')" class="bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-medium py-1 px-2 rounded transition whitespace-nowrap">
            Selesai
        </button>
        <button onclick="detectIssue(@js((string) $booking->id), '{{ $type }}')" class="bg-red-500 hover:bg-red-600 text-white text-xs font-medium py-1 px-2 rounded transition whitespace-nowrap">
            Masalah
        </button>
        @endif

        @if(!in_array($booking->order_status->value, ['Completed', 'Cancelled']))
        <button onclick="cancelOrder(@js((string) $booking->id), '{{ $type }}')" class="bg-gray-500 hover:bg-gray-600 text-white text-xs font-medium py-1 px-2 rounded transition whitespace-nowrap">
            Batalkan
        </button>
        @endif
    </div>
@endif

<script>
function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || @js(csrf_token());
}

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
            submitStatusChange(`/officer/book-${type}s/${bookingId}/validate`);
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
            submitStatusChange(`/officer/book-${type}s/${bookingId}/confirm`);
        }
    });
}

function preparePickup(bookingId, type) {
    Swal.fire({
        title: 'Siapkan Pengambilan',
        text: 'Barang akan dipacking dan siap diambil user di lokasi',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#22C55E',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Ya, Siapkan'
    }).then((result) => {
        if (result.isConfirmed) {
            submitStatusChange(`/officer/book-${type}s/${bookingId}/prepare-pickup`);
        }
    });
}

function handoverToUser(bookingId, type) {
    Swal.fire({
        title: 'Serah Terima ke User',
        text: 'Pastikan user sudah hadir di lokasi sebelum melanjutkan',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#0891B2',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Ya, Serahkan'
    }).then((result) => {
        if (result.isConfirmed) {
            submitStatusChange(`/officer/book-${type}s/${bookingId}/handover`);
        }
    });
}

function scheduleReturn(bookingId, type) {
    Swal.fire({
        title: 'Jadwalkan Pengembalian',
        text: 'Tetapkan jadwal user untuk mengembalikan barang ke lokasi',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#A855F7',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Ya, Jadwalkan'
    }).then((result) => {
        if (result.isConfirmed) {
            submitStatusChange(`/officer/book-${type}s/${bookingId}/schedule-return`);
        }
    });
}

function receiveReturn(bookingId, type) {
    Swal.fire({
        title: 'Terima Pengembalian',
        text: 'Barang kembali dari user dan siap masuk tahap review',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#F97316',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Ya, Terima'
    }).then((result) => {
        if (result.isConfirmed) {
            submitStatusChange(`/officer/book-${type}s/${bookingId}/receive-return`);
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
            submitStatusChange(`/officer/book-${type}s/${bookingId}/complete`);
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
            formData.append('_token', getCsrfToken());
            
            fetch(`/officer/book-${type}s/${bookingId}/detect-issue`, {
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
            formData.append('_token', getCsrfToken());
            
            fetch(`/officer/book-${type}s/${bookingId}/cancel`, {
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

function submitStatusChange(url) {
    fetch(url, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': getCsrfToken()
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
