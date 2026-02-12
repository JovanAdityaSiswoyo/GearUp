<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unit Details & QR Code - Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen">
        @include('admin.partials.sidebar')

        <div class="ml-64">
            @include('admin.partials.header', ['title' => 'Unit Details'])

            <main class="p-8">
                <div class="mb-6">
                    <a href="{{ route('admin.units.index') }}" class="text-purple-600 hover:text-purple-800 flex items-center space-x-2">
                        <x-heroicon-o-arrow-left class="h-5 w-5" />
                        <span>Back to Units</span>
                    </a>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Unit Information -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Basic Info -->
                        <div class="bg-white rounded-xl shadow-sm p-8">
                            <h2 class="text-2xl font-bold text-gray-800 mb-6">Unit Information</h2>

                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <p class="text-sm text-gray-500 font-medium mb-1">Serial Number</p>
                                    <p class="text-xl font-mono font-semibold text-gray-900">{{ $unit->serial_number }}</p>
                                </div>

                                <div>
                                    <p class="text-sm text-gray-500 font-medium mb-1">Status</p>
                                    <span class="px-4 py-2 inline-flex text-sm font-semibold rounded-full {{ 
                                        [
                                            'available' => 'bg-green-100 text-green-800',
                                            'booked' => 'bg-blue-100 text-blue-800',
                                            'deployed' => 'bg-yellow-100 text-yellow-800',
                                            'returning' => 'bg-orange-100 text-orange-800',
                                            'in_inspection' => 'bg-purple-100 text-purple-800',
                                            'maintenance' => 'bg-red-100 text-red-800',
                                            'lost_scrapped' => 'bg-gray-100 text-gray-800',
                                        ][$unit->status] ?? 'bg-gray-100 text-gray-800'
                                    }}">
                                        {{ ucfirst(str_replace('_', ' ', $unit->status)) }}
                                    </span>
                                </div>

                                <div class="col-span-2">
                                    <p class="text-sm text-gray-500 font-medium mb-1">Product</p>
                                    <div class="flex items-center gap-3 mt-2">
                                        @if($unit->product->image)
                                        <img src="{{ asset('storage/' . $unit->product->image) }}" alt="{{ $unit->product->name }}" class="w-12 h-12 rounded-lg object-cover">
                                        @else
                                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                                            <x-heroicon-o-cube class="h-6 w-6 text-purple-600" />
                                        </div>
                                        @endif
                                        <div>
                                            <p class="font-semibold text-gray-900">{{ $unit->product->name }}</p>
                                            <p class="text-sm text-gray-600">{{ $unit->product->desc }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-span-2">
                                    <p class="text-sm text-gray-500 font-medium mb-2">Description</p>
                                    <p class="text-gray-700">{{ $unit->product->description ?? 'No description' }}</p>
                                </div>

                                <div>
                                    <p class="text-sm text-gray-500 font-medium mb-1">Created At</p>
                                    <p class="text-gray-900">{{ $unit->created_at->format('d M Y H:i') }}</p>
                                </div>

                                <div>
                                    <p class="text-sm text-gray-500 font-medium mb-1">Last Maintenance</p>
                                    <p class="text-gray-900">{{ $unit->last_maintenance_at?->format('d M Y') ?? 'Never' }}</p>
                                </div>

                                @if($unit->notes)
                                <div class="col-span-2">
                                    <p class="text-sm text-gray-500 font-medium mb-2">Notes</p>
                                    <p class="text-gray-700 bg-gray-50 p-3 rounded">{{ $unit->notes }}</p>
                                </div>
                                @endif
                            </div>

                            <div class="mt-6 flex gap-3">
                                <a href="{{ route('admin.units.edit', $unit) }}" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded-lg transition inline-flex items-center gap-2">
                                    <x-heroicon-o-pencil class="h-4 w-4" />
                                    Edit Unit
                                </a>
                                <form action="{{ route('admin.units.destroy', $unit) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg transition inline-flex items-center gap-2" onclick="return confirm('Are you sure?')">
                                        <x-heroicon-o-trash class="h-4 w-4" />
                                        Delete Unit
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Product Pricing -->
                        <div class="bg-white rounded-xl shadow-sm p-8">
                            <h3 class="text-lg font-bold text-gray-800 mb-4">Product Pricing</h3>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-500 font-medium mb-1">Price</p>
                                    <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($unit->product->price ?? 0, 0, ',', '.') }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 font-medium mb-1">Price Per Day</p>
                                    <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($unit->product->price_per_day ?? 0, 0, ',', '.') }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Scan History -->
                        <div class="bg-white rounded-xl shadow-sm p-8">
                            <h3 class="text-lg font-bold text-gray-800 mb-4">📋 Scan History</h3>

                            @forelse($unitData['scan_history'] as $log)
                            <div class="flex items-start gap-4 pb-4 border-b border-gray-200 last:border-b-0">
                                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                                    <x-heroicon-o-qr-code class="h-5 w-5 text-blue-600" />
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center justify-between">
                                        <p class="font-semibold text-gray-900">{{ ucfirst(str_replace('_', ' ', $log['action'])) }}</p>
                                        <p class="text-sm text-gray-500">{{ $log['at'] }}</p>
                                    </div>
                                    <p class="text-sm text-gray-600 mt-1">
                                        <strong>{{ $log['actor'] }}</strong> ({{ $log['actor_type'] }})
                                    </p>
                                    @if($log['notes'])
                                    <p class="text-sm text-gray-500 mt-1">{{ $log['notes'] }}</p>
                                    @endif
                                </div>
                            </div>
                            @empty
                            <p class="text-gray-500 text-center py-4">No scan history yet</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- QR Code Section -->
                    <div class="lg:col-span-1">
                        <div class="bg-white rounded-xl shadow-sm overflow-hidden sticky top-8">
                            <div class="bg-gradient-to-br from-purple-600 to-purple-700 text-white p-6 text-center">
                                <h3 class="text-lg font-bold mb-2">QR Code</h3>
                                <p class="text-purple-100 text-sm">Scan untuk packing / pickup</p>
                            </div>

                            <div class="p-8 text-center">
                                <div id="qr-container" class="bg-gray-50 p-4 rounded-lg inline-block mb-6">
                                <img src="{{ $qrCode }}" alt="QR Code" width="300" height="300" />

                                <div class="space-y-2 mb-6">
                                    <p class="text-sm font-mono text-gray-600">{{ $unit->serial_number }}</p>
                                    <p class="text-xs text-gray-500">{{ route('scan-unit.show', ['unit' => $unit->id], absolute: false) }}</p>
                                </div>

                                <div class="flex flex-col gap-2">
                                    <button 
                                        onclick="downloadQr()"
                                        class="w-full bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition text-sm font-medium inline-flex items-center justify-center gap-2"
                                    >
                                        <x-heroicon-o-arrow-down-tray class="h-4 w-4" />
                                        Download QR
                                    </button>
                                    <button 
                                        onclick="copyUrl()"
                                        class="w-full bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-lg transition text-sm font-medium inline-flex items-center justify-center gap-2"
                                    >
                                        <x-heroicon-o-square-2-stack class="h-4 w-4" />
                                        Copy URL
                                    </button>
                                </div>
                            </div>

                            <!-- Last Scanned By -->
                            @if($unitData['last_scan'])
                            <div class="border-t border-gray-200 p-6">
                                <p class="text-sm text-gray-500 font-medium mb-3">Last Scanned</p>
                                <div class="bg-blue-50 rounded-lg p-4">
                                    <p class="font-semibold text-gray-900">{{ $unitData['last_scan']['actor'] }}</p>
                                    <p class="text-sm text-gray-600">{{ $unitData['last_scan']['type'] }}</p>
                                    <p class="text-xs text-gray-500 mt-2">{{ $unitData['last_scan']['at'] }}</p>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
    function downloadQr() {
        const element = document.getElementById('qr-container');
        html2canvas(element, {
            backgroundColor: '#ffffff',
            scale: 2
        }).then(canvas => {
            const link = document.createElement('a');
            link.href = canvas.toDataURL('image/png');
            link.download = 'unit-{{ $unit->serial_number }}-qr.png';
            link.click();
        });
    }

    function copyUrl() {
        const url = "{{ route('scan-unit.show', ['unit' => $unit->id], absolute: true) }}";
        navigator.clipboard.writeText(url).then(() => {
            alert('URL copied to clipboard!');
        }).catch(() => {
            alert('Failed to copy URL');
        });
    }
    </script>
</body>
</html>
