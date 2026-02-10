<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Packing Checklist | Officer</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .sub-header { font-family: 'Inter', sans-serif; }
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
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-gradient-to-b from-blue-600 to-cyan-500 text-white">
            <div class="p-6">
                <h1 class="text-2xl font-bold">Officer Panel</h1>
                <p class="text-sm opacity-80 sub-header">AplikasiPinjam</p>
            </div>
            <nav class="mt-6">
                <a href="{{ route('officer.dashboard') }}" class="flex items-center px-6 py-3 hover:bg-white/10 transition">
                    <x-heroicon-o-home class="h-5 w-5 mr-3" />
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('officer.loan-approvals.index') }}" class="flex items-center px-6 py-3 hover:bg-white/10 transition">
                    <x-heroicon-o-clipboard-document-list class="h-5 w-5 mr-3" />
                    <span>Loan Approvals</span>
                </a>
                <a href="{{ route('officer.packing.index') }}" class="flex items-center px-6 py-3 bg-white/20 border-r-4 border-white">
                    <x-heroicon-o-square-3-stack-3d class="h-5 w-5 mr-3" />
                    <span>Packing</span>
                </a>
                <a href="{{ route('officer.returns.monitor') }}" class="flex items-center px-6 py-3 hover:bg-white/10 transition">
                    <x-heroicon-o-arrow-path class="h-5 w-5 mr-3" />
                    <span>Monitor Returns</span>
                </a>
                <a href="{{ route('officer.reports.print') }}" class="flex items-center px-6 py-3 hover:bg-white/10 transition">
                    <x-heroicon-o-printer class="h-5 w-5 mr-3" />
                    <span>Print Report</span>
                </a>
                <a href="{{ route('officer.payments.index') }}" class="flex items-center px-6 py-3 hover:bg-white/10 transition">
                    <x-heroicon-o-credit-card class="h-5 w-5 mr-3" />
                    <span>Payments</span>
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="flex-1">
            <!-- Header -->
            <header class="bg-white shadow-sm">
                <div class="flex items-center justify-between px-8 py-4">
                    <div>
                        <h2 class="text-2xl font-semibold text-gray-800">Packing Checklist</h2>
                        <p class="text-sm text-gray-500 sub-header">Order #{{ $booking->booking_code }}</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('officer.packing.index') }}" class="text-gray-600 hover:text-gray-800 transition">
                            <x-heroicon-o-arrow-left class="h-6 w-6" />
                        </a>
                    </div>
                </div>
            </header>

            <main class="p-8">
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
        if (confirm('Assign units untuk booking ini?')) {
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
                    alert('✅ ' + data.message);
                    location.reload();
                } else {
                    alert('❌ ' + data.message);
                    if (data.failures) {
                        console.log('Failures:', data.failures);
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('❌ Error assigning units');
            });
        }
    }

    function handleScanSubmit(event, itemId) {
        event.preventDefault();
        const form = event.target;
        const formData = new FormData(form);

        fetch('{{ route("officer.packing.scan") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                alert('✅ ' + data.message);
                location.reload();
            } else {
                alert('❌ ' + data.message);
                form.reset();
                form.querySelector('input[name="unit_serial"]').focus();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('❌ Error scanning unit');
            form.reset();
        });
    }

    function finalizePacking() {
        if (confirm('Finalize packing? Booking akan status menjadi READY_FOR_PICKUP')) {
            fetch('{{ route("officer.packing.finalize", $booking->id) }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('✅ ' + data.message);
                    location.href = '{{ route("officer.packing.index") }}';
                } else {
                    alert('❌ ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('❌ Error finalizing packing');
            });
        }
    }
    </script>
</body>
</html>
