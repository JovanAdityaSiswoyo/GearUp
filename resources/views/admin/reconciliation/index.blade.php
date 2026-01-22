<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reconciliation - Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-50">
    @include('sweetalert::alert')
    <div class="min-h-screen">
        @include('admin.partials.sidebar')

        <div class="ml-64">
            @include('admin.partials.header', ['title' => 'Reconciliation'])

            <main class="p-8">
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">Payment Reconciliation</h2>
                    <p class="text-gray-600">Verify and reconcile all payments</p>
                </div>

                <!-- Summary Cards -->
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
                    <div class="bg-white p-6 rounded-xl shadow-sm">
                        <div class="text-gray-500 text-sm font-medium">Total Payments</div>
                        <div class="text-2xl font-bold text-gray-900 mt-2">{{ $summary['total_payments'] }}</div>
                    </div>
                    <div class="bg-white p-6 rounded-xl shadow-sm">
                        <div class="text-gray-500 text-sm font-medium">Total Amount</div>
                        <div class="text-2xl font-bold text-gray-900 mt-2">Rp{{ number_format($summary['total_amount'] / 100, 0, ',', '.') }}</div>
                    </div>
                    <div class="bg-green-50 p-6 rounded-xl shadow-sm border border-green-200">
                        <div class="text-green-600 text-sm font-medium">Paid</div>
                        <div class="text-2xl font-bold text-green-900 mt-2">Rp{{ number_format($summary['paid_amount'] / 100, 0, ',', '.') }}</div>
                    </div>
                    <div class="bg-yellow-50 p-6 rounded-xl shadow-sm border border-yellow-200">
                        <div class="text-yellow-600 text-sm font-medium">Pending</div>
                        <div class="text-2xl font-bold text-yellow-900 mt-2">Rp{{ number_format($summary['pending_amount'] / 100, 0, ',', '.') }}</div>
                    </div>
                    <div class="bg-red-50 p-6 rounded-xl shadow-sm border border-red-200">
                        <div class="text-red-600 text-sm font-medium">Failed</div>
                        <div class="text-2xl font-bold text-red-900 mt-2">Rp{{ number_format($summary['failed_amount'] / 100, 0, ',', '.') }}</div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="mb-6 space-x-3">
                    <a href="{{ route('admin.reconciliation.report') }}" class="inline-block bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                        📊 View Report
                    </a>
                    <a href="{{ route('admin.reconciliation.match-bookings') }}" class="inline-block bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 transition">
                        🔗 Match Bookings
                    </a>
                </div>

                <!-- Filter & Search -->
                <form method="GET" action="{{ route('admin.reconciliation.index') }}" class="mb-6 bg-white p-6 rounded-xl shadow-sm">
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search provider ref..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                                <option value="">All Status</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                                <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                                <option value="refunded" {{ request('status') == 'refunded' ? 'selected' : '' }}>Refunded</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Date From</label>
                            <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Date To</label>
                            <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                        </div>
                        <div class="flex items-end gap-2">
                            <button type="submit" class="bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 flex-1">
                                Filter
                            </button>
                            <a href="{{ route('admin.reconciliation.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300">
                                Reset
                            </a>
                        </div>
                    </div>
                </form>

                <!-- Payments Table -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Provider Ref</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Method</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Provider</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse($payments as $payment)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $payment->created_at->format('M d, Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900">
                                        {{ $payment->provider_ref ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                        Rp{{ number_format($payment->amount / 100, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $payment->method }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $payment->provider }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                            @if($payment->status === 'paid') bg-green-100 text-green-800
                                            @elseif($payment->status === 'pending') bg-yellow-100 text-yellow-800
                                            @elseif($payment->status === 'failed') bg-red-100 text-red-800
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            {{ ucfirst($payment->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button onclick="updateStatus('{{ $payment->id }}')" class="text-blue-600 hover:text-blue-900">Update</button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">No payments found</td>
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
        function updateStatus(paymentId) {
            Swal.fire({
                title: 'Update Payment Status',
                html: `
                    <select id="statusSelect" class="swal2-input" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                        <option value="">Select Status</option>
                        <option value="pending">Pending</option>
                        <option value="paid">Paid</option>
                        <option value="failed">Failed</option>
                        <option value="refunded">Refunded</option>
                    </select>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Update',
            }).then((result) => {
                if (result.isConfirmed) {
                    const status = document.getElementById('statusSelect').value;
                    if (status) {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = '/admin/reconciliation/' + paymentId + '/verify';
                        
                        const csrfToken = document.createElement('input');
                        csrfToken.type = 'hidden';
                        csrfToken.name = '_token';
                        csrfToken.value = '{{ csrf_token() }}';
                        form.appendChild(csrfToken);
                        
                        const statusInput = document.createElement('input');
                        statusInput.type = 'hidden';
                        statusInput.name = 'status';
                        statusInput.value = status;
                        form.appendChild(statusInput);
                        
                        document.body.appendChild(form);
                        form.submit();
                    }
                }
            });
        }
    </script>
</body>
</html>
