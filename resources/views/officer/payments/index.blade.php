<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payments - Officer</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-gradient-to-b from-blue-600 to-cyan-500 text-white">
            <div class="p-6">
                <h1 class="text-2xl font-bold">Officer Panel</h1>
                <p class="text-sm opacity-80">AplikasiPinjam</p>
            </div>
            
            <nav class="mt-6">
                <a href="{{ route('officer.dashboard') }}" class="flex items-center px-6 py-3 hover:bg-white/10 transition">
                    <x-heroicon-o-home class="h-5 w-5 mr-3" />
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('officer.books.index') }}" class="flex items-center px-6 py-3 hover:bg-white/10 transition">
                    <x-heroicon-o-book-open class="h-5 w-5 mr-3" />
                    <span>Book Loans</span>
                </a>
                <a href="{{ route('officer.products.index') }}" class="flex items-center px-6 py-3 hover:bg-white/10 transition">
                    <x-heroicon-o-cube class="h-5 w-5 mr-3" />
                    <span>Product Loans</span>
                </a>
                <a href="{{ route('officer.payments.index') }}" class="flex items-center px-6 py-3 bg-white/20 border-r-4 border-white">
                    <x-heroicon-o-credit-card class="h-5 w-5 mr-3" />
                    <span>Payments</span>
                </a>
                <a href="{{ route('officer.returns.index') }}" class="flex items-center px-6 py-3 hover:bg-white/10 transition">
                    <x-heroicon-o-arrow-path class="h-5 w-5 mr-3" />
                    <span>Returns</span>
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="flex-1">
            <!-- Header -->
            <header class="bg-white shadow-sm">
                <div class="px-8 py-4 flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">Payments</h2>
                        <p class="text-sm text-gray-600">View and manage payment records</p>
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
                <!-- Statistics Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">Total Payments</p>
                                <p class="text-2xl font-bold text-gray-800">{{ $stats['total'] }}</p>
                            </div>
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                <x-heroicon-o-banknotes class="h-6 w-6 text-blue-600" />
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">Paid</p>
                                <p class="text-2xl font-bold text-green-600">{{ $stats['paid'] }}</p>
                            </div>
                            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                <x-heroicon-o-check-circle class="h-6 w-6 text-green-600" />
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">Pending</p>
                                <p class="text-2xl font-bold text-yellow-600">{{ $stats['pending'] }}</p>
                            </div>
                            <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                                <x-heroicon-o-clock class="h-6 w-6 text-yellow-600" />
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">Total Amount</p>
                                <p class="text-lg font-bold text-gray-800">Rp {{ number_format($stats['total_amount'], 0, ',', '.') }}</p>
                            </div>
                            <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                                <x-heroicon-o-currency-dollar class="h-6 w-6 text-indigo-600" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                    <form method="GET" action="{{ route('officer.payments.index') }}" class="flex flex-wrap gap-4">
                        <div class="flex-1 min-w-[200px]">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by reference..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div class="min-w-[150px]">
                            <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">All Status</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                                <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                                <option value="refunded" {{ request('status') == 'refunded' ? 'selected' : '' }}>Refunded</option>
                            </select>
                        </div>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition">
                            Filter
                        </button>
                        <a href="{{ route('officer.payments.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-lg transition">
                            Reset
                        </a>
                    </form>
                </div>

                <!-- Payments Table -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reference</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Method</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($payments as $payment)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $payment->provider_ref }}</div>
                                        <div class="text-sm text-gray-500">{{ $payment->provider }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ class_basename($payment->payable_type) === 'Book' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                            {{ class_basename($payment->payable_type) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <div class="font-semibold">Rp {{ number_format($payment->amount, 0, ',', '.') }}</div>
                                        <div class="text-xs text-gray-500">{{ $payment->currency }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ ucfirst(str_replace('_', ' ', $payment->method)) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusColors = [
                                                'paid' => 'bg-green-100 text-green-800',
                                                'pending' => 'bg-yellow-100 text-yellow-800',
                                                'failed' => 'bg-red-100 text-red-800',
                                                'refunded' => 'bg-purple-100 text-purple-800',
                                            ];
                                        @endphp
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$payment->status] ?? 'bg-gray-100 text-gray-800' }}">
                                            {{ ucfirst($payment->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        @if($payment->paid_at)
                                            {{ $payment->paid_at->format('d M Y H:i') }}
                                        @else
                                            {{ $payment->created_at->format('d M Y H:i') }}
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                        No payments found.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $payments->links() }}
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
