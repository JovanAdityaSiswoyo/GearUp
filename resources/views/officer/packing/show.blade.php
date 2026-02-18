@extends('layouts.officer')

@section('title', 'Packing Checklist')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
    .progress-bar {
        background: linear-gradient(to right, #3b82f6 0%, #06b6d4 100%);
    }
    .item-card.packed {
        background-color: #f0fdf4;
        border-color: #86efac;
    }
    .item-card.unpacked {
        background-color: #fff7ed;
        border-color: #fed7aa;
    }
</style>
                <!-- Booking Info -->
                <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <p class="text-sm text-gray-500 sub-header mb-1">Customer</p>
                            <p class="text-lg font-semibold text-gray-800">{{ $booking->booker_name }}</p>
                            <p class="text-sm text-gray-600">{{ $booking->booker_email }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 sub-header mb-1">Package</p>
                            <p class="text-lg font-semibold text-gray-800">{{ $booking->package->name_package ?? 'N/A' }}</p>
                            <p class="text-sm text-gray-600">Rp {{ number_format($booking->package->price ?? 0, 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 sub-header mb-1">Rental Period</p>
                            <p class="text-lg font-semibold text-gray-800">
                                {{ \Carbon\Carbon::parse($booking->checkin_appointment_start)->format('d M') }} - 
                                {{ \Carbon\Carbon::parse($booking->checkout_appointment_end)->format('d M Y') }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Progress Bar -->
                <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="text-sm text-gray-500 sub-header mb-1">Packing Progress</p>
                            <p class="text-3xl font-bold text-gray-800">{{ $packingProgress['packed'] }}/{{ $packingProgress['total'] }} items</p>
                        </div>
                        <div class="text-right">
                            <p class="text-4xl font-bold text-blue-600">{{ $packingProgress['percentage'] }}%</p>
                            <p class="text-sm text-gray-500 sub-header">Complete</p>
                        </div>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                        <div class="progress-bar h-full transition-all duration-300" style="width: {{ $packingProgress['percentage'] }}%"></div>
                    </div>
                </div>

                <!-- Items Summary -->
                <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="text-sm text-gray-500 sub-header mb-1">Items to Pack</p>
                            <p class="text-lg font-semibold text-gray-800">Daftar isi paket yang harus dipacking</p>
                        </div>
                        <span class="text-sm text-gray-600">
                            Total: {{ $packageProducts->count() }} items
                        </span>
                    </div>

                    @if($packageProducts->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            @foreach($packageProducts as $product)
                                <div class="flex items-start justify-between rounded-lg border border-gray-200 px-4 py-3">
                                    <div>
                                        <p class="font-semibold text-gray-800">{{ $product->name }}</p>
                                        <p class="text-sm text-gray-600">Produk paket</p>
                                    </div>
                                    <span class="text-xs px-2 py-1 rounded-full bg-blue-100 text-blue-700">
                                        Wajib
                                    </span>
                                </div>
                            @endforeach
                        </div>

                        @if(!$packingList || count($packingList) === 0)
                            <p class="text-sm text-gray-600 mt-4">
                                Assign units terlebih dahulu agar serial number muncul untuk proses scan.
                            </p>
                        @endif
                    @else
                        <p class="text-gray-600">Belum ada item di paket ini.</p>
                        <p class="text-sm text-gray-500 mt-2">Pastikan paket memiliki item sebelum proses packing.</p>
                    @endif
                </div>

                <!-- Packing Checklist -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-600 to-cyan-500 text-white px-6 py-4">
                        <h3 class="text-xl font-semibold">📋 Packing Checklist</h3>
                        <p class="text-blue-100 text-sm mt-1">Scan QR code untuk setiap item, atau masukkan serial number secara manual</p>
                    </div>

                    <div class="divide-y divide-gray-200">
                        @forelse($packingList as $item)
                            <div class="item-card border-l-4 {{ $item['is_packed'] ? 'packed' : 'unpacked' }} p-6">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3">
                                            <div class="text-2xl">
                                                {{ $item['is_packed'] ? '✅' : '⏳' }}
                                            </div>
                                            <div>
                                                <h4 class="text-lg font-semibold text-gray-800">{{ $item['product_name'] }}</h4>
                                                <p class="text-sm text-gray-600 mt-1">
                                                    <strong>Serial:</strong> {{ $item['unit_serial'] }}
                                                </p>
                                                @if($item['is_packed'])
                                                    <p class="text-xs text-green-600 mt-2">
                                                        ✓ Packed at {{ $item['packed_at']?->format('H:i') }} by Officer
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    @if(!$item['is_packed'])
                                        <form action="{{ route('officer.packing.scan') }}" method="POST" class="flex gap-2" onsubmit="handleScanSubmit(event, '{{ $item['id'] }}')">
                                            @csrf
                                            <input type="hidden" name="book_package_product_id" value="{{ $item['id'] }}">
                                            <input 
                                                type="text" 
                                                name="unit_serial"
                                                placeholder="Scan QR / Input serial"
                                                class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                                autofocus
                                                required
                                            >
                                            <button 
                                                type="submit"
                                                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition flex items-center gap-2"
                                            >
                                                <x-heroicon-o-qr-code class="h-4 w-4" />
                                                Scan
                                            </button>
                                            <button 
                                                type="button"
                                                onclick="openQRScanner(this)"
                                                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition flex items-center gap-2 text-sm"
                                                title="Scan QR code dengan kamera"
                                            >
                                                📱 Camera
                                            </button>
                                        </form>
                                    @else
                                        <div class="text-right">
                                            <span class="inline-block px-4 py-2 bg-green-100 text-green-800 rounded-lg font-semibold">
                                                ✓ Packed
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="p-8 text-center">
                                <p class="text-gray-500">Tidak ada items untuk packing</p>
                                <button 
                                    onclick="assignUnits()"
                                    class="mt-4 bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition"
                                >
                                    Assign Units Terlebih Dahulu
                                </button>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Action Buttons -->
                @if(!$packingList || count($packingList) > 0)
                    <div class="flex gap-4 mt-6">
                        <a href="{{ route('officer.packing.index') }}" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-3 rounded-lg transition text-center font-semibold">
                            ← Back
                        </a>
                        @if($packingProgress['is_complete'])
                            <button 
                                onclick="finalizePacking()"
                                class="flex-1 bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg transition font-semibold flex items-center justify-center gap-2"
                            >
                                <x-heroicon-o-check-circle class="h-5 w-5" />
                                Finalize Packing ✓
                            </button>
                        @else
                            <button 
                                disabled
                                class="flex-1 bg-gray-300 text-gray-600 px-6 py-3 rounded-lg cursor-not-allowed font-semibold"
                                title="Scan semua items terlebih dahulu"
                            >
                                Complete Packing ({{ $packingProgress['remaining'] }} remaining)
                            </button>
                        @endif
                    </div>
                @endif
            </main>
        </div>
    </div>

    <script>
    function assignUnits() {
        Swal.fire({
            title: 'Assign Units?',
            text: 'Assign units untuk booking ini?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3b82f6',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, assign it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('{{ route("officer.packing.assign", $booking->id) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: 'Success!',
                            text: data.message,
                            icon: 'success',
                            confirmButtonColor: '#3b82f6',
                            timer: 2000,
                            timerProgressBar: true
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('Error', 'Gagal assign units', 'error');
                });
            }
        });
    }

    function handleScanSubmit(event, itemId) {
        event.preventDefault();
        const form = event.target;
        const serialInput = form.querySelector('input[name="unit_serial"]');
        if (serialInput) {
            serialInput.value = serialInput.value.trim();
        }
        const formData = new FormData(form);

        fetch('{{ route("officer.packing.scan") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(async (response) => {
            const contentType = response.headers.get('content-type') || '';
            let data = {
                success: false,
                message: 'Terjadi kesalahan saat memproses scan.',
            };

            if (contentType.includes('application/json')) {
                data = await response.json();
            }

            if (!response.ok && !data.message) {
                data.message = 'Gagal scan unit.';
            }

            return data;
        })
        .then(data => {
            if (data.success) {
                Swal.fire({
                    title: 'Success!',
                    text: data.message,
                    icon: 'success',
                    confirmButtonColor: '#3b82f6',
                    timer: 2000,
                    timerProgressBar: true
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    title: 'Error',
                    text: data.message,
                    icon: 'error',
                    confirmButtonColor: '#3b82f6'
                });
                form.reset();
                form.querySelector('input[name="unit_serial"]').focus();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error', 'Gagal scan unit', 'error');
            form.reset();
        });
    }

    function finalizePacking() {
        Swal.fire({
            title: 'Finalize Packing?',
            text: 'Booking status akan berubah menjadi READY_FOR_PICKUP',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, finalize it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('{{ route("officer.packing.finalize", $booking->id) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: 'Success!',
                            text: data.message,
                            icon: 'success',
                            confirmButtonColor: '#10b981',
                            timer: 2000,
                            timerProgressBar: true
                        }).then(() => {
                            location.href = '{{ route("officer.packing.index") }}';
                        });
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('Error', 'Gagal finalize packing', 'error');
                });
            }
        });
    }

    // QR Scanner Implementation
    let currentScannerButton = null;

    function openQRScanner(button) {
        currentScannerButton = button;
        const form = button.closest('form');
        const serialInput = form.querySelector('input[name="unit_serial"]');
        
        // Create overlay modal
        const overlay = document.createElement('div');
        overlay.id = 'scanner-overlay';
        overlay.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        `;
        
        overlay.innerHTML = `
            <div style="background: white; border-radius: 12px; padding: 24px; max-width: 600px; width: 90%; max-height: 90vh; overflow: auto;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                    <h3 style="margin: 0; font-size: 20px; font-weight: 600;">📱 Scan QR Code</h3>
                    <button onclick="closeQRScanner()" style="background: none; border: none; font-size: 24px; cursor: pointer; color: #999;">✕</button>
                </div>
                <iframe src="/scan-unit-camera?mode=packing" style="width: 100%; height: 500px; border: 1px solid #ddd; border-radius: 8px;"></iframe>
                <p style="text-align: center; color: #666; font-size: 14px; margin-top: 12px;">
                    Arahkan kamera ke QR code untuk scan
                </p>
            </div>
        `;
        
        document.body.appendChild(overlay);
        
        // Listen for postMessage from iframe
        window.addEventListener('message', handleScanMessage);
    }

    function closeQRScanner() {
        const overlay = document.getElementById('scanner-overlay');
        if (overlay) {
            overlay.remove();
        }
        window.removeEventListener('message', handleScanMessage);
    }

    function handleScanMessage(event) {
        if (event.data.action === 'qr_scanned') {
            const serialNumber = event.data.serial;
            if (currentScannerButton) {
                const form = currentScannerButton.closest('form');
                const serialInput = form.querySelector('input[name="unit_serial"]');
                serialInput.value = serialNumber;
                serialInput.focus();
                closeQRScanner();
                // Auto-submit
                setTimeout(() => form.submit(), 200);
            }
        }
    }
    </script>
@endsection
