<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payments Management - Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-50">
    @include('sweetalert::alert')
    <div class="min-h-screen">
        @include('admin.partials.sidebar')

        <div class="ml-64">
            @include('admin.partials.header', ['title' => 'Payments Management'])

            <main class="p-8">
                <!-- Statistics Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                                <x-heroicon-o-banknotes class="h-8 w-8 text-white" />
                            </div>
                            <span class="text-sm font-medium bg-white/20 px-3 py-1 rounded-full">Total</span>
                        </div>
                        <div>
                            <p class="text-sm text-blue-100 mb-1">Total Payments</p>
                            <p class="text-4xl font-bold">{{ $stats['total'] }}</p>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                                <x-heroicon-o-check-circle class="h-8 w-8 text-white" />
                            </div>
                            <span class="text-sm font-medium bg-white/20 px-3 py-1 rounded-full">Paid</span>
                        </div>
                        <div>
                            <p class="text-sm text-green-100 mb-1">Paid</p>
                            <p class="text-4xl font-bold">{{ $stats['paid'] }}</p>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-yellow-500 to-orange-500 rounded-xl shadow-lg p-6 text-white">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                                <x-heroicon-o-clock class="h-8 w-8 text-white" />
                            </div>
                            <span class="text-sm font-medium bg-white/20 px-3 py-1 rounded-full">Pending</span>
                        </div>
                        <div>
                            <p class="text-sm text-yellow-100 mb-1">Pending</p>
                            <p class="text-4xl font-bold">{{ $stats['pending'] }}</p>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-xl shadow-lg p-6 text-white">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                                <x-heroicon-o-x-circle class="h-8 w-8 text-white" />
                            </div>
                            <span class="text-sm font-medium bg-white/20 px-3 py-1 rounded-full">Failed</span>
                        </div>
                        <div>
                            <p class="text-sm text-red-100 mb-1">Failed</p>
                            <p class="text-4xl font-bold">{{ $stats['failed'] }}</p>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                                <x-heroicon-o-arrow-uturn-left class="h-8 w-8 text-white" />
                            </div>
                            <span class="text-sm font-medium bg-white/20 px-3 py-1 rounded-full">Refunded</span>
                        </div>
                        <div>
                            <p class="text-sm text-purple-100 mb-1">Refunded</p>
                            <p class="text-4xl font-bold">{{ $stats['refunded'] }}</p>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl shadow-lg p-6 text-white">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                                <x-heroicon-o-currency-dollar class="h-8 w-8 text-white" />
                            </div>
                            <span class="text-sm font-medium bg-white/20 px-3 py-1 rounded-full">IDR</span>
                        </div>
                        <div>
                            <p class="text-sm text-indigo-100 mb-1">Total Amount</p>
                            <p class="text-2xl font-bold">Rp {{ number_format($stats['total_amount'], 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                    <form method="GET" action="{{ route('admin.payments.index') }}" class="flex flex-wrap gap-4">
                        <div class="flex-1 min-w-[200px]">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by reference..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        </div>
                        <div class="min-w-[150px]">
                            <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                <option value="">All Status</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                                <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                                <option value="refunded" {{ request('status') == 'refunded' ? 'selected' : '' }}>Refunded</option>
                            </select>
                        </div>
                        <div class="min-w-[150px]">
                            <select name="method" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                <option value="">All Methods</option>
                                <option value="cash" {{ request('method') == 'cash' ? 'selected' : '' }}>Cash</option>
                                <option value="bank_transfer" {{ request('method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                <option value="credit_card" {{ request('method') == 'credit_card' ? 'selected' : '' }}>Credit Card</option>
                                <option value="e_wallet" {{ request('method') == 'e_wallet' ? 'selected' : '' }}>E-Wallet</option>
                            </select>
                        </div>
                        <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded-lg transition">
                            Filter
                        </button>
                        <a href="{{ route('admin.payments.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-lg transition">
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
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
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
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                        <a href="{{ route('admin.payments.show', $payment->id) }}" class="text-blue-600 hover:text-blue-900">View</a>
                                        @if($payment->status === 'pending')
                                        <form action="{{ route('admin.payments.verify', $payment->id) }}" method="POST" class="inline" id="verify-form-{{ $payment->id }}">
                                            @csrf
                                            <button type="button" class="text-green-600 hover:text-green-900" onclick='confirmVerify("{{ $payment->id }}")'>Verify</button>
                                        </form>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">
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

    <script>
        function confirmVerify(id) {
            Swal.fire({
                title: 'Verify Payment?',
                text: "This will mark the payment as paid and update the booking status.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, verify it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('verify-form-' + id).submit();
                }
            });
        }
    </script>
</body>
</html>
