<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Details - Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-50">
    @include('sweetalert::alert')
    <div class="min-h-screen">
        @include('admin.partials.sidebar')

        <div class="ml-64">
            @include('admin.partials.header', ['title' => 'Payment Details'])

            <main class="p-8">
                <div class="mb-6">
                    <a href="{{ route('admin.payments.index') }}" class="text-purple-600 hover:text-purple-700 flex items-center space-x-2">
                        <x-heroicon-o-arrow-left class="h-5 w-5" />
                        <span>Back to Payments</span>
                    </a>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Payment Info Card -->
                    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-xl font-bold text-gray-800">Payment Information</h2>
                            @php
                                $statusColors = [
                                    'paid' => 'bg-green-100 text-green-800',
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'failed' => 'bg-red-100 text-red-800',
                                    'refunded' => 'bg-purple-100 text-purple-800',
                                ];
                            @endphp
                            <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full {{ $statusColors[$payment->status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($payment->status) }}
                            </span>
                        </div>

                        <div class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-600">Payment Reference</p>
                                    <p class="text-lg font-semibold text-gray-900">{{ $payment->provider_ref }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Provider</p>
                                    <p class="text-lg font-semibold text-gray-900">{{ ucfirst($payment->provider) }}</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-600">Amount</p>
                                    <p class="text-2xl font-bold text-purple-600">Rp {{ number_format($payment->amount, 0, ',', '.') }}</p>
                                    <p class="text-sm text-gray-500">{{ $payment->currency }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Payment Method</p>
                                    <p class="text-lg font-semibold text-gray-900">{{ ucfirst(str_replace('_', ' ', $payment->method)) }}</p>
                                </div>
                            </div>

                            <div class="border-t border-gray-200 pt-4">
                                <p class="text-sm text-gray-600 mb-2">Payable Type</p>
                                <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full {{ class_basename($payment->payable_type) === 'Book' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                    {{ class_basename($payment->payable_type) }}
                                </span>
                            </div>

                            @if($payment->meta)
                            <div class="border-t border-gray-200 pt-4">
                                <p class="text-sm text-gray-600 mb-2">Additional Information</p>
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <pre class="text-sm text-gray-700">{{ json_encode($payment->meta, JSON_PRETTY_PRINT) }}</pre>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Timeline Card -->
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <h2 class="text-xl font-bold text-gray-800 mb-6">Timeline</h2>
                        
                        <div class="space-y-4">
                            <div class="flex items-start space-x-3">
                                <div class="w-2 h-2 mt-2 bg-blue-500 rounded-full"></div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900">Payment Created</p>
                                    <p class="text-xs text-gray-500">{{ $payment->created_at->format('d M Y H:i:s') }}</p>
                                </div>
                            </div>

                            @if($payment->paid_at)
                            <div class="flex items-start space-x-3">
                                <div class="w-2 h-2 mt-2 bg-green-500 rounded-full"></div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900">Payment Completed</p>
                                    <p class="text-xs text-gray-500">{{ $payment->paid_at->format('d M Y H:i:s') }}</p>
                                </div>
                            </div>
                            @endif

                            @if($payment->failed_at)
                            <div class="flex items-start space-x-3">
                                <div class="w-2 h-2 mt-2 bg-red-500 rounded-full"></div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900">Payment Failed</p>
                                    <p class="text-xs text-gray-500">{{ $payment->failed_at->format('d M Y H:i:s') }}</p>
                                </div>
                            </div>
                            @endif

                            @if($payment->refunded_at)
                            <div class="flex items-start space-x-3">
                                <div class="w-2 h-2 mt-2 bg-purple-500 rounded-full"></div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900">Payment Refunded</p>
                                    <p class="text-xs text-gray-500">{{ $payment->refunded_at->format('d M Y H:i:s') }}</p>
                                </div>
                            </div>
                            @endif
                        </div>

                        @if($payment->status === 'pending')
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <form action="{{ route('admin.payments.verify', $payment->id) }}" method="POST" id="verify-form">
                                @csrf
                                <button type="button" onclick="confirmVerify()" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition">
                                    Verify Payment
                                </button>
                            </form>
                        </div>
                        @endif
                    </div>

                    <!-- Related Booking Card -->
                    @if($payment->payable)
                    <div class="lg:col-span-3 bg-white rounded-xl shadow-sm p-6">
                        <h2 class="text-xl font-bold text-gray-800 mb-6">Related Booking</h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            @if($payment->payable_type === 'App\Models\Book' && $payment->payable)
                            <div>
                                <p class="text-sm text-gray-600">Booking Code</p>
                                <p class="text-lg font-semibold text-gray-900">{{ $payment->payable->book_code }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Booker Name</p>
                                <p class="text-lg font-semibold text-gray-900">{{ $payment->payable->booker_name }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Status</p>
                                <p class="text-lg font-semibold text-gray-900">{{ ucfirst($payment->payable->status) }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Amount</p>
                                <p class="text-lg font-semibold text-gray-900">Rp {{ number_format($payment->payable->amount, 0, ',', '.') }}</p>
                            </div>
                            @elseif($payment->payable_type === 'App\Models\BookProduct' && $payment->payable)
                            <div>
                                <p class="text-sm text-gray-600">Product Booking ID</p>
                                <p class="text-lg font-semibold text-gray-900">{{ $payment->payable->id }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Status</p>
                                <p class="text-lg font-semibold text-gray-900">{{ ucfirst($payment->payable->status) }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Amount</p>
                                <p class="text-lg font-semibold text-gray-900">Rp {{ number_format($payment->payable->amount, 0, ',', '.') }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </main>
        </div>
    </div>

    <script>
        function confirmVerify() {
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
                    document.getElementById('verify-form').submit();
                }
            });
        }
    </script>
</body>
</html>
