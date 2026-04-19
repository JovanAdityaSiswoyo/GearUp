<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Detail - Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <div class="min-h-screen">
        @include('admin.partials.sidebar')

        <div class="ml-64">
            @include('admin.partials.header', ['title' => 'Booking Detail'])

            <main class="p-8">
                @php
                    $statusBadgeClass = match ($booking->status) {
                        'pending' => 'bg-yellow-100 text-yellow-800',
                        'confirmed' => 'bg-blue-100 text-blue-800',
                        'active' => 'bg-green-100 text-green-800',
                        'completed' => 'bg-gray-100 text-gray-800',
                        default => 'bg-red-100 text-red-800',
                    };
                @endphp
                @php
                    $renterDetail = $booking->item_type === 'product'
                        ? $booking->detailBookProduct
                        : $booking->detailBook;
                @endphp
                <!-- Header -->
                <div class="mb-8 flex justify-between items-center">
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('admin.bookings.index') }}" class="text-purple-600 hover:text-purple-700 font-medium flex items-center">
                            <x-heroicon-o-arrow-left class="h-5 w-5 mr-2" />
                            Back to Bookings
                        </a>
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">Booking Detail</h1>
                            <p class="text-gray-600 mt-1">Code: <span class="font-mono font-semibold">{{ $booking->book_code }}</span></p>
                        </div>
                    </div>
                    <span class="px-4 py-2 inline-block text-sm font-semibold rounded
                        {{ $statusBadgeClass }}">
                        {{ ucfirst($booking->status) }}
                    </span>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Main Content (2 cols) -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Customer Info -->
                        <div class="bg-white rounded-lg shadow p-6">
                            <h2 class="text-lg font-bold text-gray-900 mb-4">Customer Information</h2>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-600">Name</p>
                                    <p class="font-semibold text-gray-900">{{ $booking->booker_name }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Phone</p>
                                    <p class="font-semibold text-gray-900">{{ $booking->booker_telp }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Email</p>
                                    <p class="font-semibold text-gray-900">{{ $booking->booker_email }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">User Account</p>
                                    <p class="font-semibold text-gray-900">{{ $booking->user->name ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Product/Package Info -->
                        <div class="bg-white rounded-lg shadow p-6">
                            <h2 class="text-lg font-bold text-gray-900 mb-4">{{ $booking->item_type === 'product' ? 'Product Details' : 'Package Details' }}</h2>
                            <div class="flex space-x-4">
                                @if($booking->item_type === 'product')
                                    @if($booking->product && $booking->product->image)
                                        <img src="{{ asset('storage/' . $booking->product->image) }}" alt="{{ $booking->product->name }}" class="w-24 h-24 rounded-lg object-cover">
                                    @else
                                        <div class="w-24 h-24 rounded-lg bg-gray-200 flex items-center justify-center">
                                            <x-heroicon-o-photo class="h-8 w-8 text-gray-400" />
                                        </div>
                                    @endif
                                    <div class="flex-1">
                                        <p class="text-sm text-gray-600">Product</p>
                                        <h3 class="font-bold text-gray-900 text-lg">{{ $booking->product->name ?? 'Product Deleted' }}</h3>
                                        <p class="text-sm text-gray-700 mt-2">{{ $booking->product->description ?? '-' }}</p>
                                        <div class="mt-3 flex space-x-4">
                                            <div>
                                                <p class="text-xs text-gray-600">Category</p>
                                                <p class="font-semibold">{{ $booking->product->category->name ?? '-' }}</p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-600">Brand</p>
                                                <p class="font-semibold">{{ $booking->product->brand->name ?? '-' }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    @if($booking->package && $booking->package->image)
                                        <img src="{{ asset('storage/' . $booking->package->image) }}" alt="{{ $booking->package->name_package }}" class="w-24 h-24 rounded-lg object-cover">
                                    @else
                                        <div class="w-24 h-24 rounded-lg bg-gray-200 flex items-center justify-center">
                                            <x-heroicon-o-photo class="h-8 w-8 text-gray-400" />
                                        </div>
                                    @endif
                                    <div class="flex-1">
                                        <p class="text-sm text-gray-600">Package</p>
                                        <h3 class="font-bold text-gray-900 text-lg">{{ $booking->package->name_package ?? 'Package Deleted' }}</h3>
                                        <p class="text-sm text-gray-700 mt-2">{{ $booking->package->description ?? '-' }}</p>
                                        
                                        @if($booking->detailBooks && $booking->detailBooks->count() > 0)
                                            <div class="mt-3">
                                                <p class="text-xs text-gray-600 mb-2">Package Items:</p>
                                                <div class="space-y-1">
                                                    @foreach($booking->detailBooks as $detail)
                                                        <div class="text-xs bg-gray-50 px-2 py-1 rounded">
                                                            <span class="font-semibold">{{ $detail->product->name ?? 'N/A' }}</span>
                                                            <span class="text-gray-500">x{{ $detail->amount }}</span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
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
                                <div>
                                    <p class="text-gray-600">Mulai Sewa (Detail)</p>
                                    <p class="font-semibold text-gray-900">{{ $renterDetail->rental_start_at?->format('d M Y H:i') ?? '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-600">Akhir Sewa (Detail)</p>
                                    <p class="font-semibold text-gray-900">{{ $renterDetail->rental_end_at?->format('d M Y H:i') ?? '-' }}</p>
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
                            <h2 class="text-lg font-bold text-gray-900 mb-4">Rental Period</h2>
                            <div class="grid grid-cols-3 gap-4">
                                <div>
                                    <p class="text-sm text-gray-600">Check-in</p>
                                    <p class="font-semibold text-gray-900 text-lg">{{ $booking->checkin_appointment_start->format('M d, Y') }}</p>
                                    <p class="text-xs text-gray-500">{{ $booking->checkin_appointment_start->format('H:i') }}</p>
                                </div>
                                <div class="flex items-center justify-center pt-6">
                                    <x-heroicon-o-arrow-right class="h-6 w-6 text-gray-400" />
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Check-out</p>
                                    <p class="font-semibold text-gray-900 text-lg">{{ $booking->checkout_appointment_end->format('M d, Y') }}</p>
                                    <p class="text-xs text-gray-500">{{ $booking->checkout_appointment_end->format('H:i') }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Financial Summary -->
                        <div class="bg-white rounded-lg shadow p-6">
                            <h2 class="text-lg font-bold text-gray-900 mb-4">Financial Summary</h2>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Amount</span>
                                    <span class="font-semibold">{{ $booking->amount }} pcs</span>
                                </div>
                                <div class="border-t pt-3 flex justify-between">
                                    <span class="font-bold text-gray-900">Total Cost</span>
                                    <span class="font-bold text-lg text-purple-600">Rp {{ number_format($booking->amount * 100000, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Timeline -->
                        <div class="bg-white rounded-lg shadow p-6">
                            <h2 class="text-lg font-bold text-gray-900 mb-4">Timeline</h2>
                            <div class="space-y-3 text-sm">
                                <div class="flex">
                                    <div class="w-32 text-gray-600">Created:</div>
                                    <div class="font-semibold">{{ $booking->created_at->format('M d, Y H:i') }}</div>
                                </div>
                                <div class="flex">
                                    <div class="w-32 text-gray-600">Last Updated:</div>
                                    <div class="font-semibold">{{ $booking->updated_at->format('M d, Y H:i') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar (1 col) -->
                    <div class="space-y-6">
                        <!-- Quick Info -->
                        <div class="bg-white rounded-lg shadow p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">Details</h3>
                            <div class="space-y-3 text-sm">
                                <div>
                                    <p class="text-gray-600">Book Code</p>
                                    <p class="font-semibold font-mono text-gray-900">{{ $booking->book_code }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-600">Status</p>
                                    <span class="px-2 py-1 inline-block text-xs font-semibold rounded {{ $statusBadgeClass }}">
                                        {{ ucfirst($booking->status) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Status Info -->
                        <div class="bg-blue-50 rounded-lg border border-blue-200 p-4 text-sm text-blue-800">
                            <p class="font-semibold mb-2">📋 Status Reference:</p>
                            <ul class="space-y-1 text-xs">
                                <li><strong>Pending:</strong> Booking created, awaiting payment</li>
                                <li><strong>Confirmed:</strong> Payment received, approved</li>
                                <li><strong>Active:</strong> Rental period is ongoing</li>
                                <li><strong>Completed:</strong> Rental finished, returned</li>
                                <li><strong>Cancelled:</strong> Booking cancelled</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        // View-only detail page; no admin actions available.
    </script>
</body>
</html>
