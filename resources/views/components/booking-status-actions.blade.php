@php
    use App\Enums\OrderStatus;
@endphp

@php
    $isOfficer = auth('officer')->check() || (auth()->check() && auth()->user() && method_exists(auth()->user(), 'hasRole') && auth()->user()->hasRole('officer'));
    $isApproved = $isApproved ?? false;
@endphp

@if($isOfficer)
    <!-- Officer Status Controls -->
    <div class="flex gap-1 items-center justify-start flex-wrap">
        @if($booking->order_status === OrderStatus::PENDING)
            @if(!$isApproved)
                <button onclick="approveBooking(@js((string) $booking->id), '{{ $type }}')" class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium py-1 px-2 rounded transition whitespace-nowrap">
                    Approve
                </button>
            @else
                <button onclick="handoverToUser(@js((string) $booking->id), '{{ $type }}')" class="bg-cyan-600 hover:bg-cyan-700 text-white text-xs font-medium py-1 px-2 rounded transition whitespace-nowrap">
                    Serah ke User
                </button>
            @endif
            <button onclick="cancelOrder(@js((string) $booking->id), '{{ $type }}')" class="bg-gray-500 hover:bg-gray-600 text-white text-xs font-medium py-1 px-2 rounded transition whitespace-nowrap">
                Batalkan
            </button>
        @elseif($booking->order_status === OrderStatus::DIPINJAM)
            <button onclick="completeOrder(@js((string) $booking->id), '{{ $type }}')" class="bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-medium py-1 px-2 rounded transition whitespace-nowrap">
                Selesai
            </button>
            <button onclick="detectIssue(@js((string) $booking->id), '{{ $type }}')" class="bg-red-500 hover:bg-red-600 text-white text-xs font-medium py-1 px-2 rounded transition whitespace-nowrap">
                Masalah
            </button>
        @endif
    </div>
@endif

<script>
function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || @js(csrf_token());
}

function approveBooking(bookingId, type) {
    Swal.fire({
        title: 'Approve Booking',
        text: 'Booking akan di-approve oleh officer.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#2563EB',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Ya, Approve'
    }).then((result) => {
        if (result.isConfirmed) {
            submitStatusChange(`/officer/book-${type}s/${bookingId}/validate`);
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
