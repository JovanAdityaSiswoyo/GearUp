<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Packing Management | Officer</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .sub-header { font-family: 'Inter', sans-serif; }
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
                        <h2 class="text-2xl font-semibold text-gray-800">Packing Management</h2>
                        <p class="text-sm text-gray-500 sub-header">Manage package packing and unit assignment</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <span class="text-sm text-gray-600">Officer: {{ auth()->guard('officer')->user()->name ?? 'Unknown' }}</span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            <main class="p-8">
                <!-- Search & Filter -->
                <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                    <form method="GET" action="{{ route('officer.packing.index') }}" class="flex gap-4">
                        <input 
                            type="text" 
                            name="search" 
                            placeholder="Cari booking code, nama customer..."
                            value="{{ request('search') }}"
                            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition">
                            Cari
                        </button>
                        @if(request('search'))
                            <a href="{{ route('officer.packing.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-lg transition">
                                Reset
                            </a>
                        @endif
                    </form>
                </div>

                <!-- Bookings List -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    @if($bookings->isEmpty())
                        <div class="p-12 text-center">
                            <x-heroicon-o-inbox class="h-16 w-16 text-gray-300 mx-auto mb-4" />
                            <p class="text-gray-500 text-lg">Tidak ada booking yang perlu packing</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50 border-b border-gray-200">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Booking Code</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Package</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Items</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($bookings as $booking)
                                        <tr class="hover:bg-gray-50 transition">
                                            <td class="px-6 py-4">
                                                <span class="font-semibold text-gray-800">{{ $booking->booking_code }}</span>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div>
                                                    <p class="font-medium text-gray-800">{{ $booking->booker_name }}</p>
                                                    <p class="text-sm text-gray-500">{{ $booking->booker_email }}</p>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <span class="text-gray-800">{{ $booking->package->name_package ?? 'N/A' }}</span>
                                            </td>
                                            <td class="px-6 py-4">
                                                @if($booking->order_status === \App\Enums\OrderStatus::CONFIRMED)
                                                    <span class="px-3 py-1 text-xs font-semibold bg-yellow-100 text-yellow-800 rounded-full">
                                                        Confirmed
                                                    </span>
                                                @elseif($booking->order_status === \App\Enums\OrderStatus::READY_FOR_PICKUP)
                                                    <span class="px-3 py-1 text-xs font-semibold bg-green-100 text-green-800 rounded-full">
                                                        Ready
                                                    </span>
                                                @else
                                                    <span class="px-3 py-1 text-xs font-semibold bg-gray-100 text-gray-800 rounded-full">
                                                        {{ $booking->order_status->getLabel() }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4">
                                                @php
                                                    $itemCount = \App\Models\BookPackageProduct::where('id_book', $booking->id)->count();
                                                @endphp
                                                <span class="text-gray-800">{{ $itemCount }} items</span>
                                            </td>
                                            <td class="px-6 py-4">
                                                <a href="{{ route('officer.packing.show', $booking->id) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                                                    <x-heroicon-o-arrow-right class="h-4 w-4 mr-2" />
                                                    Packing
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                            {{ $bookings->links('pagination::tailwind') }}
                        </div>
                    @endif
                </div>
            </main>
        </div>
    </div>
</body>
</html>
