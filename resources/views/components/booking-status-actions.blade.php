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
        conditionRequired: true,
        conditionFieldName: 'issue_condition',
        conditionOptions: [
            { value: 'rusak_ringan', label: 'Rusak Ringan (15%)' },
            { value: 'rusak_sedang', label: 'Rusak Sedang (30%)' },
            { value: 'rusak_berat', label: 'Rusak Berat (70%)' },
            { value: 'hilang', label: 'Hilang (100%)' }
        ],
        photoRequiredMessage: 'Foto masalah wajib diunggah.',
        notesRequiredMessage: 'Catatan masalah harus diisi.',
        conditionRequiredMessage: 'Kondisi barang harus dipilih.'
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
    let capturedBlob = null;
    let activeStream = null;

    function stopStream() {
        if (activeStream) {
            activeStream.getTracks().forEach(t => t.stop());
            activeStream = null;
        }
    }

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
                ${options.conditionRequired ? `
                    <label style="display: block; margin-bottom: 14px;">
                        <span style="font-weight: 600; color: #374151;">Kondisi Barang:</span>
                        <select id="swal-condition" style="width: 100%; margin-top: 8px; padding: 10px; border: 1px solid #d1d5db; border-radius: 8px; font-family: inherit; background-color: #fff;">
                            <option value="">-- Pilih kondisi --</option>
                            ${(options.conditionOptions || []).map((option) => `<option value="${option.value}">${option.label}</option>`).join('')}
                        </select>
                    </label>
                ` : ''}
                <label style="display: block; margin-bottom: 10px;">
                    <span style="font-weight: 600; color: #374151;">Foto Bukti:</span>
                    <div id="camera-container" style="margin-top: 8px;">
                        <video id="swal-video" autoplay playsinline muted
                            style="width: 100%; border-radius: 8px; display: none; background: #000;"></video>
                        <canvas id="swal-canvas" style="display: none;"></canvas>
                        <img id="swal-preview" alt="Preview foto"
                            style="display: none; width: 100%; border-radius: 12px; border: 1px solid #e5e7eb; margin-top: 8px;" />
                        <div style="display: flex; gap: 8px; margin-top: 10px; flex-wrap: wrap;">
                            <button type="button" id="btn-camera"
                                style="flex: 1; padding: 8px 12px; background: #0891B2; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 13px;">
                                Buka Kamera
                            </button>
                            <button type="button" id="btn-capture"
                                style="display: none; flex: 1; padding: 8px 12px; background: #059669; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 13px;">
                                Ambil Foto
                            </button>
                            <button type="button" id="btn-retake"
                                style="display: none; flex: 1; padding: 8px 12px; background: #6B7280; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 13px;">
                                Ulangi
                            </button>
                            <button type="button" id="btn-gallery"
                                style="flex: 1; padding: 8px 12px; background: #374151; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 13px;">
                                Pilih Galeri
                            </button>
                        </div>
                        <input id="swal-photo" type="file" accept="image/*" style="display: none;" />
                    </div>
                </label>
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
            const popup      = Swal.getPopup();
            const video      = popup.querySelector('#swal-video');
            const canvas     = popup.querySelector('#swal-canvas');
            const preview    = popup.querySelector('#swal-preview');
            const btnCamera  = popup.querySelector('#btn-camera');
            const btnCapture = popup.querySelector('#btn-capture');
            const btnRetake  = popup.querySelector('#btn-retake');
            const btnGallery = popup.querySelector('#btn-gallery');
            const fileInput  = popup.querySelector('#swal-photo');

            // Buka kamera via getUserMedia (kamera belakang)
            btnCamera.addEventListener('click', async () => {
                try {
                    activeStream = await navigator.mediaDevices.getUserMedia({
                        video: { facingMode: 'environment' },
                        audio: false
                    });
                    video.srcObject = activeStream;
                    video.style.display  = 'block';
                    preview.style.display = 'none';
                    btnCamera.style.display  = 'none';
                    btnCapture.style.display = 'flex';
                    btnRetake.style.display  = 'none';
                    capturedBlob = null;
                } catch (err) {
                    Swal.showValidationMessage('Kamera tidak dapat diakses: ' + err.message);
                }
            });

            // Ambil foto dari stream video
            btnCapture.addEventListener('click', () => {
                canvas.width  = video.videoWidth;
                canvas.height = video.videoHeight;
                canvas.getContext('2d').drawImage(video, 0, 0);
                canvas.toBlob(blob => {
                    capturedBlob = blob;
                    preview.src = canvas.toDataURL('image/jpeg');
                    preview.style.display    = 'block';
                    video.style.display      = 'none';
                    btnCapture.style.display = 'none';
                    btnRetake.style.display  = 'flex';
                    btnCamera.style.display  = 'none';
                    stopStream();
                }, 'image/jpeg', 0.92);
            });

            // Ulangi — matikan stream, kembali ke state awal
            btnRetake.addEventListener('click', () => {
                capturedBlob = null;
                preview.style.display   = 'none';
                btnRetake.style.display = 'none';
                btnCamera.style.display = 'flex';
                stopStream();
            });

            // Fallback: pilih dari galeri/storage
            btnGallery.addEventListener('click', () => {
                fileInput.click();
            });

            fileInput.addEventListener('change', () => {
                const file = fileInput.files && fileInput.files[0];
                if (!file) return;
                capturedBlob = file;
                const reader = new FileReader();
                reader.onload = e => {
                    preview.src = e.target.result;
                    preview.style.display    = 'block';
                    video.style.display      = 'none';
                    btnCapture.style.display = 'none';
                    btnRetake.style.display  = 'flex';
                    stopStream();
                };
                reader.readAsDataURL(file);
            });
        },
        willClose: () => {
            stopStream();
        },
        preConfirm: () => {
            const popup = Swal.getPopup();

            if (!capturedBlob) {
                Swal.showValidationMessage(options.photoRequiredMessage || 'Foto wajib diunggah.');
                return false;
            }

            const formData = new FormData();
            const filename = capturedBlob instanceof File ? capturedBlob.name : 'foto_bukti.jpg';
            formData.append(options.fieldName, capturedBlob, filename);

            if (options.notesRequired) {
                const notes = popup.querySelector('#swal-notes').value.trim();
                if (!notes) {
                    Swal.showValidationMessage(options.notesRequiredMessage || 'Catatan harus diisi.');
                    return false;
                }
                formData.append(options.notesFieldName || 'issue_notes', notes);
            }

            if (options.conditionRequired) {
                const condition = popup.querySelector('#swal-condition').value;
                if (!condition) {
                    Swal.showValidationMessage(options.conditionRequiredMessage || 'Kondisi harus dipilih.');
                    return false;
                }
                formData.append(options.conditionFieldName || 'issue_condition', condition);
            }

            formData.append('_token', getCsrfToken());

            return fetch(url, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
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