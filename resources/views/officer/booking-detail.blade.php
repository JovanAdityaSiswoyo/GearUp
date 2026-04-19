@extends('layouts.officer')

@section('title', 'Detail Booking')

@section('content')
        @php
            $isProductBooking = $booking instanceof \App\Models\BookProduct;
            $packageImage = data_get($booking, 'package.image');
            $packageName = data_get($booking, 'package.name_package', 'Paket Dihapus');
            $packageDescription = data_get($booking, 'package.description', '-');
            $packagePrice = data_get($booking, 'package.price', 0);
            $rentalDays = null;
            if ($booking->checkin_appointment_start && $booking->checkout_appointment_end) {
                $startDate = $booking->checkin_appointment_start->copy()->startOfDay();
                $endDate = $booking->checkout_appointment_end->copy()->startOfDay();
                $rentalDays = max(1, $startDate->diffInDays($endDate) + 1);
            }

            $dailyRentalPrice = $isProductBooking
                ? ($booking->product->price_per_day ?? $booking->product->price ?? 0)
                : $packagePrice;

            $totalRentalPrice = $dailyRentalPrice * ($rentalDays ?? 0);
            $depositAmount = $booking->deposit_amount ?? 0;
            $grandTotal = $totalRentalPrice + $depositAmount;

            $orderBadgeClass = match ($booking->order_status->value) {
                'pending' => 'bg-yellow-100 text-yellow-800',
                'dipinjam' => 'bg-blue-100 text-blue-800',
                'selesai' => 'bg-emerald-100 text-emerald-800',
                default => 'bg-gray-100 text-gray-800',
            };

            $itemBadgeClass = match ($booking->item_status->value) {
                'Available' => 'bg-green-100 text-green-800',
                'Booked' => 'bg-blue-100 text-blue-800',
                'Packing' => 'bg-blue-100 text-blue-800',
                'Picked-Up' => 'bg-purple-100 text-purple-800',
                'Deployed' => 'bg-emerald-100 text-emerald-800',
                'Returning' => 'bg-orange-100 text-orange-800',
                'In-Inspection' => 'bg-yellow-100 text-yellow-800',
                'Maintenance' => 'bg-red-100 text-red-800',
                'Lost/Scrapped' => 'bg-red-200 text-red-900',
                default => 'bg-gray-100 text-gray-800',
            };

            $renterDetail = $isProductBooking
                ? $booking->detailBookProduct
                : $booking->detailBook;
        @endphp

        <!-- Header -->
        <div class="mb-8 flex items-center justify-between">
            <div>
                <a href="{{ route('officer.bookings.index') }}" class="text-blue-600 hover:text-blue-700 mb-4 inline-flex items-center">
                    <x-heroicon-o-arrow-left class="h-5 w-5 mr-2" /> Kembali
                </a>
                <div class="flex items-center space-x-3">
                    <x-heroicon-o-document class="h-8 w-8 text-blue-600" />
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Detail Booking</h1>
                        <p class="text-sm text-gray-600">Kode: <span class="font-mono font-semibold">{{ $booking->book_code }}</span></p>
                    </div>
                </div>
            </div>
            <span class="px-4 py-2 inline-block text-sm font-semibold rounded {{ $orderBadgeClass }}">
                {{ $booking->order_status->label() }}
            </span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Customer Info -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">Informasi Penyewa</h2>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">Nama</p>
                            <p class="font-semibold text-gray-900">{{ $booking->booker_name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Telepon</p>
                            <p class="font-semibold text-gray-900">{{ $booking->booker_telp }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Email</p>
                            <p class="font-semibold text-gray-900">{{ $booking->booker_email ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Member Sejak</p>
                            <p class="font-semibold text-gray-900">{{ $booking->user->created_at->format('d M Y') ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Item Details -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">
                        @if($booking instanceof \App\Models\BookProduct)
                            Produk yang Disewa
                        @else
                            Paket yang Disewa
                        @endif
                    </h2>
                    <div class="flex space-x-4">
                        @if($booking instanceof \App\Models\BookProduct)
                            @if($booking->product && $booking->product->image)
                                <img src="{{ asset('storage/' . $booking->product->image) }}" alt="{{ $booking->product->name }}" class="w-24 h-24 rounded-lg object-cover">
                            @else
                                <div class="w-24 h-24 rounded-lg bg-gray-200 flex items-center justify-center">
                                    <x-heroicon-o-photo class="h-8 w-8 text-gray-400" />
                                </div>
                            @endif
                            <div class="flex-1">
                                <p class="text-sm text-gray-600">Produk</p>
                                <h3 class="font-bold text-gray-900 text-lg">{{ $booking->product->name ?? 'Produk Dihapus' }}</h3>
                                <p class="text-sm text-gray-700 mt-2">{{ $booking->product->description ?? '-' }}</p>
                                <div class="mt-3 flex space-x-4">
                                    <div>
                                        <p class="text-xs text-gray-600">Kategori</p>
                                        <p class="font-semibold">{{ $booking->product->category->categories ?? $booking->product->category->name ?? '-' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-600">Brand</p>
                                        <p class="font-semibold">{{ $booking->product->brand->name ?? '-' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-600">Harga Sewa/Hari</p>
                                        <p class="font-semibold text-green-600">Rp {{ number_format($booking->product->price_per_day ?? $booking->product->price ?? 0, 0, ',', '.') }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-600">Jumlah Hari</p>
                                        <p class="font-semibold">{{ $rentalDays ?? '-' }}</p>
                                    </div>
                                </div>
                            </div>
                        @else
                            @if($packageImage)
                                <img src="{{ asset('storage/' . $packageImage) }}" alt="{{ $packageName }}" class="w-24 h-24 rounded-lg object-cover">
                            @else
                                <div class="w-24 h-24 rounded-lg bg-gray-200 flex items-center justify-center">
                                    <x-heroicon-o-photo class="h-8 w-8 text-gray-400" />
                                </div>
                            @endif
                            <div class="flex-1">
                                <p class="text-sm text-gray-600">Paket</p>
                                <h3 class="font-bold text-gray-900 text-lg">{{ $packageName }}</h3>
                                <p class="text-sm text-gray-700 mt-2">{{ $packageDescription }}</p>
                                <div class="mt-3 flex space-x-4">
                                    <div>
                                        <p class="text-xs text-gray-600">Harga Paket</p>
                                        <p class="font-semibold text-blue-600">Rp {{ number_format($packagePrice, 0, ',', '.') }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-600">Jumlah Hari</p>
                                        <p class="font-semibold">{{ $rentalDays ?? '-' }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                @if($renterDetail)
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">Data Penyewa Lengkap</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-gray-600">Nama Lengkap</p>
                            <p class="font-semibold text-gray-900">{{ $renterDetail->full_name ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">No. HP Penyewa</p>
                            <p class="font-semibold text-gray-900">{{ $renterDetail->phone_number ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">No. Orang Tua/Wali</p>
                            <p class="font-semibold text-gray-900">{{ $renterDetail->emergency_phone_number ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Instagram</p>
                            <p class="font-semibold text-gray-900">{{ $renterDetail->instagram_handle ?? '-' }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <p class="text-gray-600">Media Sosial Lainnya</p>
                            <p class="font-semibold text-gray-900">{{ $renterDetail->other_socials ?? '-' }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <p class="text-gray-600">Alamat Lengkap</p>
                            <p class="font-semibold text-gray-900">{{ $renterDetail->renter_address ?? '-' }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <p class="text-gray-600 mb-2">Foto KTP/Identitas</p>
                            @if(!empty($renterDetail->identity_document_path))
                                <a href="{{ asset('storage/' . $renterDetail->identity_document_path) }}" target="_blank" rel="noopener noreferrer" class="inline-block">
                                    <img src="{{ asset('storage/' . $renterDetail->identity_document_path) }}" alt="Foto KTP Penyewa" class="w-44 h-28 rounded-lg object-cover border border-gray-200 hover:opacity-90 transition">
                                </a>
                            @else
                                <p class="font-semibold text-gray-500">-</p>
                            @endif
                        </div>
                    </div>
                </div>
                @endif

                <!-- Rental Period -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">Periode Sewa</h2>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">Mulai Sewa</p>
                            <p class="font-semibold text-gray-900">{{ $booking->checkin_appointment_start->format('d M Y H:i') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Akhir Sewa</p>
                            <p class="font-semibold text-gray-900">{{ $booking->checkout_appointment_end->format('d M Y H:i') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Financial Summary -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">Ringkasan Finansial</h2>
                    <div class="space-y-2 mb-4">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Harga Sewa</span>
                            <span class="font-semibold">Rp {{ number_format($totalRentalPrice, 0, ',', '.') }}</span>
                        </div>
                        <div class="border-t pt-2 flex justify-between">
                            <span class="font-bold text-gray-900">Total</span>
                            <span class="font-bold text-lg text-gray-900">Rp {{ number_format($grandTotal, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Order Timeline -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">Timeline</h2>
                    <div class="space-y-3 text-sm">
                        <div class="flex">
                            <div class="w-32 text-gray-600">Dibuat:</div>
                            <div class="font-semibold">{{ $booking->created_at->format('d M Y H:i') }}</div>
                        </div>
                        <div class="flex">
                            <div class="w-32 text-gray-600">Update Terakhir:</div>
                            <div class="font-semibold">{{ $booking->updated_at->format('d M Y H:i') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Quick Status Info -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Status Order</h3>
                    <div class="space-y-3">
                        <div>
                            <p class="text-xs text-gray-600 mb-1">Order</p>
                            <span class="px-3 py-2 inline-block text-sm font-semibold rounded bg-blue-100 text-blue-800">
                                {{ $booking->order_status->label() }}
                            </span>
                        </div>
                        <div>
                            <p class="text-xs text-gray-600 mb-1">Item</p>
                            <span class="px-3 py-2 inline-block text-sm font-semibold rounded {{ $itemBadgeClass }}">
                                {{ $booking->item_status->label() }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Tindakan</h3>
                    <div class="space-y-2">
                        <button onclick="openEditModal()" 
                            class="w-full bg-purple-600 hover:bg-purple-700 text-white font-medium py-2 px-3 rounded transition">
                            ✏️ Edit Data
                        </button>
                        @php
                            $bookingType = $isProductBooking ? 'product' : 'package';
                        @endphp

                        @if($booking->order_status == \App\Enums\OrderStatus::PENDING)
                            @if(!$isApproved)
                                <button onclick="confirmAction('{{ $isProductBooking ? url('/officer/book-products/' . $booking->id . '/validate') : url('/officer/book-packages/' . $booking->id . '/validate') }}', 'Approve Booking')" 
                                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-3 rounded transition">
                                    ✓ Approve Booking
                                </button>
                                <div class="bg-amber-50 border border-amber-200 rounded p-3 text-sm text-amber-800">
                                    Booking harus di-approve officer sebelum bisa masuk status dipinjam.
                                </div>
                            @else
                                <button onclick="confirmAction('{{ $isProductBooking ? url('/officer/book-products/' . $booking->id . '/handover') : url('/officer/book-packages/' . $booking->id . '/handover') }}', 'Serahkan ke User')" 
                                    class="w-full bg-cyan-600 hover:bg-cyan-700 text-white font-medium py-2 px-3 rounded transition">
                                    🚚 Serahkan ke User (Dipinjam)
                                </button>
                            @endif
                        @elseif($booking->order_status == \App\Enums\OrderStatus::DIPINJAM)
                            <button onclick="confirmAction('{{ $isProductBooking ? url('/officer/book-products/' . $booking->id . '/complete') : url('/officer/book-packages/' . $booking->id . '/complete') }}', 'Selesaikan Booking')" 
                                class="w-full bg-emerald-500 hover:bg-emerald-600 text-white font-medium py-2 px-3 rounded transition">
                                ✓ Selesai
                            </button>
                        @elseif($booking->order_status == \App\Enums\OrderStatus::SELESAI)
                            <div class="bg-emerald-50 border border-emerald-200 rounded p-3 text-sm text-emerald-800">
                                ✓ Booking sudah selesai
                            </div>
                        @endif

                        @if($booking->order_status !== \App\Enums\OrderStatus::SELESAI)
                            <button onclick="openIssueModal()" 
                                class="w-full bg-red-500 hover:bg-red-600 text-white font-medium py-2 px-3 rounded transition">
                                ⚠️ Deteksi Issue
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Help Info -->
                <div class="bg-blue-50 rounded-lg border border-blue-200 p-4 text-sm text-blue-800">
                    <p class="font-semibold mb-2">💡 Alur Flow:</p>
                    <ol class="list-decimal list-inside space-y-1 text-xs">
                        <li>Approve booking oleh officer (Booking Management)</li>
                        <li>Serah ke user (status menjadi Dipinjam)</li>
                        <li>Setelah selesai masa pinjam, set status Selesai</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="hidden fixed inset-0 bg-black bg-opacity-50 items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4 max-h-96 overflow-y-auto">
        <h3 class="text-xl font-bold text-gray-900 mb-4">Edit Status Order</h3>
        <form id="editForm">
            <div class="space-y-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select id="editStatus" name="order_status" required class="w-full border rounded p-2 text-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent" onchange="showStatusInfo()">
                        <option value="">-- Pilih Status --</option>
                        <option value="pending">Pending</option>
                        <option value="dipinjam">Dipinjam</option>
                        <option value="selesai">Selesai</option>
                    </select>
                </div>
                <!-- Status Info Box -->
                <div id="statusInfo" class="bg-blue-50 border border-blue-200 rounded p-3 text-sm text-blue-800 hidden">
                    <p class="font-semibold mb-1" id="statusTitle"></p>
                    <p id="statusDescription" class="text-xs leading-relaxed"></p>
                </div>
            </div>
            <div class="flex space-x-2">
                <button type="button" onclick="closeEditModal()" 
                    class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-900 font-medium py-2 rounded transition">
                    Batal
                </button>
                <button type="button" onclick="submitEdit()" 
                    class="flex-1 bg-purple-500 hover:bg-purple-600 text-white font-medium py-2 rounded transition">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Issue Modal -->
<div id="issueModal" class="hidden fixed inset-0 bg-black bg-opacity-50 items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-xl font-bold text-gray-900 mb-4">Deteksi Issue</h3>
        <form id="issueForm">
            <textarea id="issueDescription" name="description" placeholder="Deskripsi issue..." 
                class="w-full border rounded p-3 mb-4 text-sm" rows="4" required></textarea>
            <div class="flex space-x-2">
                <button type="button" onclick="closeIssueModal()" 
                    class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-900 font-medium py-2 rounded transition">
                    Batal
                </button>
                <button type="button" onclick="submitIssue()" 
                    class="flex-1 bg-red-500 hover:bg-red-600 text-white font-medium py-2 rounded transition">
                    Konfirmasi
                </button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmAction(url, action) {
        Swal.fire({
            title: action,
            text: 'Apakah Anda yakin?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('Berhasil!', data.message, 'success')
                            .then(() => location.reload());
                    } else {
                        Swal.fire('Error!', data.message, 'error');
                    }
                });
            }
        });
    }

    function openEditModal() {
        document.getElementById('editStatus').value = '{{ $booking->order_status->value }}';
        document.getElementById('editModal').classList.remove('hidden');
        showStatusInfo();
    }

    function showStatusInfo() {
        const select = document.getElementById('editStatus');
        const value = select.value;
        const infoBox = document.getElementById('statusInfo');
        const titleEl = document.getElementById('statusTitle');
        const descEl = document.getElementById('statusDescription');

        const statusDescriptions = {
            'pending': { title: '⏳ Pending', desc: 'Booking aktif dan menunggu proses operasional officer.' },
            'dipinjam': { title: '🚚 Dipinjam', desc: 'Barang sudah diserahkan ke user dan sedang masa pinjam.' },
            'selesai': { title: '✅ Selesai', desc: 'Transaksi selesai, barang kembali dan proses ditutup.' }
        };

        if (value && statusDescriptions[value]) {
            const desc = statusDescriptions[value];
            titleEl.textContent = desc.title;
            descEl.textContent = desc.desc;
            infoBox.classList.remove('hidden');
        } else {
            infoBox.classList.add('hidden');
        }
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
        document.getElementById('editForm').reset();
    }

    function submitEdit() {
        const statusValue = document.getElementById('editStatus').value;
        console.log('Opening Modal - Status value:', statusValue);
        
        if (!statusValue) {
            Swal.fire('Error!', 'Status harus diisi', 'error');
            return;
        }

        const data = {
            order_status: statusValue
        };
        
        console.log('Sending data:', data);
        console.log('URL:', '{{ route("officer.booking-detail.update", ["type" => $bookingType, "bookingId" => $booking->id]) }}');

            fetch('{{ route("officer.booking-detail.update", ["type" => $bookingType, "bookingId" => $booking->id]) }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => {
            console.log('Response status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            if (data.success) {
                Swal.fire('Berhasil!', data.message, 'success')
                    .then(() => location.reload());
            } else {
                Swal.fire('Error!', data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
            Swal.fire('Error!', 'Terjadi kesalahan: ' + error.message, 'error');
        });
    }

    function openIssueModal() {
        document.getElementById('issueModal').classList.remove('hidden');
    }

    function closeIssueModal() {
        document.getElementById('issueModal').classList.add('hidden');
        document.getElementById('issueForm').reset();
    }

    function submitIssue() {
        const description = document.getElementById('issueDescription').value;
        if (!description.trim()) {
            Swal.fire('Error!', 'Deskripsi issue harus diisi', 'error');
            return;
        }

        @php
            $isProductBooking = $booking instanceof \App\Models\BookProduct;
            $issueRoute = $isProductBooking
                ? url('/officer/book-products/' . $booking->id . '/detect-issue')
                : url('/officer/book-packages/' . $booking->id . '/detect-issue');
        @endphp

        fetch('{{ $issueRoute }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ issue_notes: description })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                Swal.fire('Berhasil!', data.message, 'success')
                    .then(() => location.reload());
            } else {
                Swal.fire('Error!', data.message, 'error');
            }
        });
    }
</script>
@endsection
