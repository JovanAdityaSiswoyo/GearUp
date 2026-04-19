<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Return Details - Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    @include('sweetalert::alert')
    <div class="min-h-screen">
        @include('admin.partials.sidebar')

        <div class="ml-64">
            @include('admin.partials.header', ['title' => 'Return Details'])

            <main class="p-8">
                <div class="max-w-4xl mx-auto">
                    @php
                        $returnStatusClass = $return->status === 'active'
                            ? 'bg-green-100 text-green-800'
                            : 'bg-gray-100 text-gray-800';
                        $renterDetail = ($return->item_type ?? 'product') === 'package'
                            ? $return->detailBooks?->first()
                            : $return->detailBookProducts?->first();
                    @endphp
                    <div class="bg-white rounded-xl shadow-sm p-8">
                        <div class="flex justify-between items-start mb-6">
                            <div>
                                <h2 class="text-2xl font-bold text-gray-800">Booking Details</h2>
                                <p class="text-gray-600">{{ $return->book_code }}</p>
                            </div>
                            <span class="px-4 py-2 inline-flex text-sm font-semibold rounded-full {{ $returnStatusClass }}">
                                {{ ucfirst($return->status) }}
                            </span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 mb-2">User Information</h3>
                                <div class="space-y-2">
                                    <p class="text-gray-900"><span class="font-medium">Name:</span> {{ $return->user->name ?? 'N/A' }}</p>
                                    <p class="text-gray-900"><span class="font-medium">Email:</span> {{ $return->user->email ?? 'N/A' }}</p>
                                </div>
                            </div>

                            <div>
                                <h3 class="text-sm font-medium text-gray-500 mb-2">Booker Information</h3>
                                <div class="space-y-2">
                                    <p class="text-gray-900"><span class="font-medium">Name:</span> {{ $return->booker_name }}</p>
                                    <p class="text-gray-900"><span class="font-medium">Email:</span> {{ $return->booker_email }}</p>
                                    <p class="text-gray-900"><span class="font-medium">Phone:</span> {{ $return->booker_telp }}</p>
                                </div>
                            </div>

                            <div>
                                <h3 class="text-sm font-medium text-gray-500 mb-2">Item</h3>
                                <div class="space-y-2">
                                    <p class="text-gray-900">
                                        <span class="font-medium">Type:</span>
                                        {{ ($return->item_type ?? 'product') === 'package' ? 'Package' : 'Product' }}
                                    </p>
                                    <p class="text-gray-900">
                                        <span class="font-medium">Name:</span>
                                        {{ ($return->item_type ?? 'product') === 'package'
                                            ? ($return->package->name_package ?? 'N/A')
                                            : ($return->product->name ?? 'N/A') }}
                                    </p>
                                    <p class="text-gray-900"><span class="font-medium">Amount:</span> {{ $return->amount }} pcs</p>
                                </div>
                            </div>

                            <div>
                                <h3 class="text-sm font-medium text-gray-500 mb-2">Rental Period</h3>
                                <div class="space-y-2">
                                    <p class="text-gray-900"><span class="font-medium">Check-in:</span> {{ $return->checkin_appointment_start->format('M d, Y H:i') }}</p>
                                    <p class="text-gray-900"><span class="font-medium">Check-out:</span> {{ $return->checkout_appointment_end->format('M d, Y H:i') }}</p>
                                    @php
                                        $totalDays = $return->checkin_appointment_start->diffInDays($return->checkout_appointment_end);
                                        $daysLeft = now()->diffInDays($return->checkout_appointment_end, false);
                                    @endphp
                                    <p class="text-gray-900"><span class="font-medium">Total Days:</span> {{ $totalDays }} days</p>
                                    @if($daysLeft < 0)
                                        <p class="text-red-600 font-semibold">⚠️ Overdue by {{ abs($daysLeft) }} days!</p>
                                    @elseif($daysLeft <= 3 && $return->status === 'active')
                                        <p class="text-orange-600 font-semibold">⏰ {{ $daysLeft }} days remaining</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        @if($renterDetail)
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Data Penyewa Lengkap</h3>
                            <div class="bg-gray-50 p-4 rounded-lg grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                <p><span class="font-medium">Nama Lengkap:</span> {{ $renterDetail->full_name ?? '-' }}</p>
                                <p><span class="font-medium">No. HP Penyewa:</span> {{ $renterDetail->phone_number ?? '-' }}</p>
                                <p><span class="font-medium">No. Orang Tua/Wali:</span> {{ $renterDetail->emergency_phone_number ?? '-' }}</p>
                                <p><span class="font-medium">Instagram:</span> {{ $renterDetail->instagram_handle ?? '-' }}</p>
                                <p class="md:col-span-2"><span class="font-medium">Media Sosial Lainnya:</span> {{ $renterDetail->other_socials ?? '-' }}</p>
                                <p class="md:col-span-2"><span class="font-medium">Alamat Lengkap:</span> {{ $renterDetail->renter_address ?? '-' }}</p>
                                <p><span class="font-medium">Mulai Sewa (Detail):</span> {{ $renterDetail->rental_start_at?->format('d M Y H:i') ?? '-' }}</p>
                                <p><span class="font-medium">Akhir Sewa (Detail):</span> {{ $renterDetail->rental_end_at?->format('d M Y H:i') ?? '-' }}</p>
                                <div class="md:col-span-2">
                                    <p class="font-medium mb-2">Foto KTP/Identitas:</p>
                                    @if(!empty($renterDetail->identity_document_path))
                                        <a href="{{ asset('storage/' . $renterDetail->identity_document_path) }}" target="_blank" rel="noopener noreferrer" class="inline-block">
                                            <img src="{{ asset('storage/' . $renterDetail->identity_document_path) }}" alt="Foto KTP Penyewa" class="w-44 h-28 rounded-lg object-cover border border-gray-200 hover:opacity-90 transition">
                                        </a>
                                    @else
                                        <p>-</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endif

                        <div class="flex items-center space-x-4">
                            <a href="{{ route('admin.returns.index') }}" class="text-gray-600 hover:text-gray-800 px-6 py-2">
                                Back to List
                            </a>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>
