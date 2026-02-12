<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unit {{ $unit->serial_number }} - Scan Detail</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <div class="min-h-screen py-8">
        <div class="max-w-4xl mx-auto px-4">
            <!-- Header -->
            <div class="mb-8 flex justify-between items-center">
                <div>
                    <h1 class="text-4xl font-bold text-gray-900">{{ $unit->serial_number }}</h1>
                    <p class="text-gray-600 mt-2">Unit Information & Status</p>
                </div>
                <a href="/" class="text-purple-600 hover:text-purple-800">← Back</a>
            </div>

            <!-- Product Info -->
            <div class="bg-white rounded-xl shadow-sm p-8 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        @if($unit->product->image)
                        <img src="{{ asset('storage/' . $unit->product->image) }}" alt="{{ $unit->product->name }}" class="w-full rounded-lg object-cover h-200">
                        @else
                        <div class="w-full h-48 bg-gray-200 rounded-lg flex items-center justify-center">
                            <x-heroicon-o-cube class="h-16 w-16 text-gray-400" />
                        </div>
                        @endif
                    </div>

                    <div class="md:col-span-2">
                        <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ $unit->product->name }}</h2>
                        <p class="text-gray-600 mb-4">{{ $unit->product->desc }}</p>

                        @if($unit->product->description)
                        <div class="mb-4">
                            <p class="text-sm font-semibold text-gray-700 mb-2">Description:</p>
                            <p class="text-gray-700">{{ $unit->product->description }}</p>
                        </div>
                        @endif

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-500">Status</p>
                                <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full {{ 
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

                            <div>
                                <p class="text-sm text-gray-500">Price</p>
                                <p class="text-lg font-semibold text-purple-600">Rp {{ number_format($unit->product->price ?? 0, 0, ',', '.') }}</p>
                            </div>

                            <div>
                                <p class="text-sm text-gray-500">Price Per Day</p>
                                <p class="text-lg font-semibold text-purple-600">Rp {{ number_format($unit->product->price_per_day ?? 0, 0, ',', '.') }}</p>
                            </div>

                            <div>
                                <p class="text-sm text-gray-500">Created</p>
                                <p class="font-semibold text-gray-900">{{ $unit->created_at->format('d M Y') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Current Status & Last Scan -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="font-semibold text-gray-900 mb-4">Current Status</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Serial Number:</span>
                            <span class="font-mono font-semibold">{{ $unit->serial_number }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Unit Status:</span>
                            <span class="font-semibold">{{ ucfirst(str_replace('_', ' ', $unit->status)) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Last Maintenance:</span>
                            <span class="font-semibold">{{ $unit->last_maintenance_at?->format('d M Y') ?? 'Never' }}</span>
                        </div>
                        @if($unit->notes)
                        <div class="border-t pt-3 mt-3">
                            <span class="text-gray-600 block mb-2">Notes:</span>
                            <p class="text-sm text-gray-700 bg-gray-50 p-2 rounded">{{ $unit->notes }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                @if($unitData['last_scan'])
                <div class="bg-blue-50 rounded-xl shadow-sm p-6">
                    <h3 class="font-semibold text-gray-900 mb-4">Last Scanned</h3>
                    <div class="space-y-3">
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Actor:</p>
                            <p class="text-xl font-semibold text-gray-900">{{ $unitData['last_scan']['actor'] }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Type:</p>
                            <p class="text-gray-900">{{ $unitData['last_scan']['type'] }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Time:</p>
                            <p class="text-gray-900">{{ $unitData['last_scan']['at'] }}</p>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Action Buttons -->
            <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                <h3 class="font-semibold text-gray-900 mb-4">Actions</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @auth('officer')
                    <form action="{{ route('scan-unit.start-packing', $unit) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg transition inline-flex items-center justify-center gap-2">
                            <x-heroicon-o-arrow-path class="h-5 w-5" />
                            Start Packing
                        </button>
                    </form>
                    @elseauth('courier')
                    <form action="{{ route('scan-unit.pickup', $unit) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 rounded-lg transition inline-flex items-center justify-center gap-2">
                            <x-heroicon-o-arrow-up-tray class="h-5 w-5" />
                            Pick Up Unit
                        </button>
                    </form>
                    @else
                    <button type="button" disabled class="w-full bg-gray-300 text-gray-600 font-semibold py-3 rounded-lg cursor-not-allowed">
                        Login Required
                    </button>
                    @endauth

                    <a href="{{ route('scan-unit.history', $unit) }}" class="w-full bg-purple-600 hover:bg-purple-700 text-white font-semibold py-3 rounded-lg transition inline-flex items-center justify-center gap-2">
                        <x-heroicon-o-bookmark class="h-5 w-5" />
                        View History
                    </a>

                    <a href="/" class="w-full bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-3 rounded-lg transition inline-flex items-center justify-center gap-2">
                        <x-heroicon-o-home class="h-5 w-5" />
                        Home
                    </a>
                </div>
            </div>

            <!-- Quick Info -->
            <div class="bg-purple-50 rounded-xl shadow-sm p-6 border border-purple-200">
                <h3 class="font-semibold text-gray-900 mb-3">Unit Information Summary</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                    <div>
                        <p class="text-2xl font-bold text-purple-600">{{ count($unitData['scan_history']) }}</p>
                        <p class="text-sm text-gray-600">Total Scans</p>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-purple-600">{{ $unit->status === 'available' ? '✓' : '✗' }}</p>
                        <p class="text-sm text-gray-600">Available</p>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-purple-600">{{ $unit->created_at->diffForHumans() }}</p>
                        <p class="text-sm text-gray-600">Created</p>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-purple-600">{{ $unit->product->stock ?? 0 }}</p>
                        <p class="text-sm text-gray-600">Product Stock</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
