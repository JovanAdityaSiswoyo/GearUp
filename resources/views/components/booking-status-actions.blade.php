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
    submitStatusChangeWithPhoto(`/officer/book-${type}s/${bookingId}/handover`, {
        title: 'Serah Terima ke User',
        text: 'Ambil atau unggah foto saat barang diserahkan ke user.',
        confirmText: 'Ya, Serahkan',
        fieldName: 'handover_photo',
        photoRequiredMessage: 'Foto serah terima wajib diunggah.'
    });
}


function completeOrder(bookingId, type) {
    submitStatusChangeWithPhoto(`/officer/book-${type}s/${bookingId}/complete`, {
        title: 'Selesaikan Order',
        text: 'Unggah foto bukti saat barang selesai dipinjam dan dikembalikan.',
        confirmText: 'Ya, Selesaikan',
        fieldName: 'return_photo',
        photoRequiredMessage: 'Foto selesai pinjam wajib diunggah.'
    });
}

function detectIssue(bookingId, type) {
    submitStatusChangeWithPhoto(`/officer/book-${type}s/${bookingId}/detect-issue`, {
        title: 'Deteksi Masalah',
        text: 'Masukkan catatan masalah dan foto bukti kerusakan atau temuan.',
        confirmText: 'Ya, Catat Masalah',
        fieldName: 'issue_photo',
        notesFieldName: 'issue_notes',
        notesRequired: true,
        photoRequiredMessage: 'Foto masalah wajib diunggah.',
        notesRequiredMessage: 'Catatan masalah harus diisi.'
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

function submitStatusChangeWithPhoto(url, options) {
    Swal.fire({
        title: options.title,
        html: `
            <div style="text-align: left;">
                <p style="margin-bottom: 12px; color: #4b5563; font-size: 14px;">${options.text ?? ''}</p>
                ${options.notesRequired ? `
                    <label style="display: block; margin-bottom: 14px;">
                        <span style="font-weight: 600; color: #374151;">Catatan Masalah:</span>
                        <textarea id="swal-notes" style="width: 100%; margin-top: 8px; padding: 10px; border: 1px solid #d1d5db; border-radius: 8px; font-family: inherit;" placeholder="Jelaskan masalah yang ditemukan..." rows="4"></textarea>
                    </label>
                ` : ''}
                <label style="display: block; margin-bottom: 10px;">
                    <span style="font-weight: 600; color: #374151;">Foto Bukti:</span>
                    <input id="swal-photo" type="file" accept="image/*" capture="environment" style="display:block; width:100%; margin-top:8px;" />
                </label>
                <img id="swal-preview" alt="Preview foto" style="display:none; width:100%; border-radius: 12px; border: 1px solid #e5e7eb; margin-top: 10px;" />
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: options.confirmColor || '#0891B2',
        cancelButtonColor: '#6B7280',
        confirmButtonText: options.confirmText,
        showLoaderOnConfirm: true,
        allowOutsideClick: () => !Swal.isLoading(),
        didOpen: () => {
            const photoInput = Swal.getPopup().querySelector('#swal-photo');
            const preview = Swal.getPopup().querySelector('#swal-preview');

            photoInput.addEventListener('change', () => {
                const file = photoInput.files && photoInput.files[0];
                if (!file) {
                    preview.style.display = 'none';
                    preview.src = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = (event) => {
                    preview.src = event.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            });
        },
        preConfirm: () => {
            const popup = Swal.getPopup();
            const photoInput = popup.querySelector('#swal-photo');
            const file = photoInput.files && photoInput.files[0];

            if (!file) {
                Swal.showValidationMessage(options.photoRequiredMessage || 'Foto wajib diunggah.');
                return false;
            }

            const formData = new FormData();
            formData.append(options.fieldName, file);

            if (options.notesRequired) {
                const notes = popup.querySelector('#swal-notes').value.trim();
                if (!notes) {
                    Swal.showValidationMessage(options.notesRequiredMessage || 'Catatan harus diisi.');
                    return false;
                }

                formData.append(options.notesFieldName || 'issue_notes', notes);
            }

            formData.append('_token', getCsrfToken());

            return fetch(url, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            }).then(async (response) => {
                const data = await response.json();
                if (!response.ok || !data.success) {
                    throw new Error(data.message || 'Gagal memproses aksi.');
                }

                return data;
            }).catch((error) => {
                Swal.showValidationMessage(error.message);
                return false;
            });
        }
    }).then((result) => {
        if (result.isConfirmed && result.value) {
            Swal.fire('Berhasil', result.value.message, 'success').then(() => location.reload());
        }
    });
}
</script>
