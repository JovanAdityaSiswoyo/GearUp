<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Match Bookings - Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-50">
    @include('sweetalert::alert')
    <div class="min-h-screen">
        @include('admin.partials.sidebar')

        <div class="ml-64">
            @include('admin.partials.header', ['title' => 'Match Bookings to Payments'])

            <main class="p-8">
                <div class="mb-6 flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">Match Bookings to Payments</h2>
                        <p class="text-gray-600">Find unmatched bookings and create payment records</p>
                    </div>
                    <a href="{{ route('admin.reconciliation.index') }}" class="bg-gray-600 text-white px-6 py-2 rounded-lg hover:bg-gray-700">
                        ← Back
                    </a>
                </div>

                <!-- Stats -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div class="bg-white p-6 rounded-xl shadow-sm">
                        <div class="text-gray-500 text-sm font-medium">Unmatched Bookings</div>
                        <div class="text-2xl font-bold text-gray-900 mt-2">{{ $bookings->total() }}</div>
                    </div>
                    <div class="bg-blue-50 p-6 rounded-xl shadow-sm border border-blue-200">
                        <div class="text-blue-600 text-sm font-medium">Total Unmatched Amount</div>
                        <div class="text-2xl font-bold text-blue-900 mt-2">
                            Rp{{ number_format($bookings->sum(fn($b) => $b->amount ?? 0) / 100, 0, ',', '.') }}
                        </div>
                    </div>
                    <div class="bg-green-50 p-6 rounded-xl shadow-sm border border-green-200">
                        <div class="text-green-600 text-sm font-medium">Per Page</div>
                        <div class="text-2xl font-bold text-green-900 mt-2">{{ $bookings->count() }}</div>
                    </div>
                </div>

                <!-- Unmatched Bookings Table -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rental Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse($bookings as $booking)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="font-mono text-sm font-semibold text-purple-600">{{ $booking->book_code }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $booking->user->name ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $booking->product->name ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                        Rp{{ number_format(($booking->amount ?? 0) / 100, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                            @if($booking->status === 'active') bg-blue-100 text-blue-800
                                            @elseif($booking->status === 'completed') bg-green-100 text-green-800
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            {{ ucfirst($booking->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $booking->checkin_appointment_start->format('M d, Y') ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button onclick="openPaymentModal('{{ $booking->id }}', '{{ $booking->book_code }}', {{ $booking->amount ?? 0 }})" class="text-green-600 hover:text-green-900 font-semibold">
                                            Add Payment
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                        ✓ All bookings have payment records!
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $bookings->links() }}
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Payment Modal -->
    <div id="paymentModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
        <div class="bg-white rounded-xl shadow-lg p-8 max-w-md w-full mx-4">
            <h3 class="text-xl font-bold text-gray-900 mb-4">Create Payment Record</h3>
            
            <form id="paymentForm" method="POST" action="">
                @csrf
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Booking Code</label>
                    <input type="text" id="bookingCode" readonly class="w-full px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-900 font-mono">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Amount</label>
                    <div class="relative">
                        <span class="absolute left-4 top-2 text-gray-900 font-semibold">Rp</span>
                        <input type="number" id="amount" name="amount" min="0" step="100" required class="w-full pl-12 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Payment Method</label>
                    <select name="method" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                        <option value="">Select Method</option>
                        <option value="cash">Cash</option>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="credit_card">Credit Card</option>
                        <option value="debit_card">Debit Card</option>
                        <option value="e_wallet">E-Wallet</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Payment Status</label>
                    <select name="status" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                        <option value="pending">Pending</option>
                        <option value="paid">Paid</option>
                        <option value="failed">Failed</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Provider (Optional)</label>
                    <input type="text" name="provider" placeholder="e.g., BCA, GCash, OVO" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Provider Reference (Optional)</label>
                    <input type="text" name="provider_ref" placeholder="e.g., TXN12345" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>

                <div class="flex gap-3">
                    <button type="submit" class="flex-1 bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 font-semibold">
                        Create Payment
                    </button>
                    <button type="button" onclick="closePaymentModal()" class="flex-1 bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 font-semibold">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let currentBookingId = null;

        function openPaymentModal(bookingId, bookingCode, amount) {
            currentBookingId = bookingId;
            document.getElementById('bookingCode').value = bookingCode;
            document.getElementById('amount').value = Math.floor(amount / 100);
            document.getElementById('paymentForm').action = '/admin/reconciliation/' + bookingId + '/create-payment';
            document.getElementById('paymentModal').classList.remove('hidden');
        }

        function closePaymentModal() {
            document.getElementById('paymentModal').classList.add('hidden');
        }

        // Close modal when clicking outside
        document.getElementById('paymentModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closePaymentModal();
            }
        });

        // Format number input
        document.getElementById('amount').addEventListener('input', function(e) {
            let value = e.target.value.replace(/[^\d]/g, '');
            if (value) {
                e.target.value = Math.floor(value);
            }
        });
    </script>
</body>
</html>
