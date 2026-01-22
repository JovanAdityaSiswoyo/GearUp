<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reconciliation Report - Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <div class="min-h-screen">
        @include('admin.partials.sidebar')

        <div class="ml-64">
            @include('admin.partials.header', ['title' => 'Reconciliation Report'])

            <main class="p-8">
                <div class="mb-6 flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">Reconciliation Report</h2>
                        <p class="text-gray-600">Detailed period-based reconciliation analysis</p>
                    </div>
                    <a href="{{ route('admin.reconciliation.index') }}" class="bg-gray-600 text-white px-6 py-2 rounded-lg hover:bg-gray-700">
                        ← Back
                    </a>
                </div>

                <!-- Period Filter -->
                <form method="GET" action="{{ route('admin.reconciliation.report') }}" class="mb-6 bg-white p-6 rounded-xl shadow-sm">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Date From</label>
                            <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Date To</label>
                            <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        </div>
                        <div class="flex items-end gap-2">
                            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 flex-1">
                                Generate Report
                            </button>
                        </div>
                        <div class="flex items-end gap-2">
                            <button type="button" onclick="window.print()" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 flex-1">
                                🖨️ Print
                            </button>
                        </div>
                    </div>
                </form>

                <div class="space-y-6">
                    <!-- Summary by Status -->
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Summary by Status</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Count</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Amount</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Percentage</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @php
                                        $totalAmount = collect($report['by_status'])->values()->sum();
                                    @endphp
                                    @foreach($report['by_status'] as $status => $amount)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                                @if($status === 'paid') bg-green-100 text-green-800
                                                @elseif($status === 'pending') bg-yellow-100 text-yellow-800
                                                @elseif($status === 'failed') bg-red-100 text-red-800
                                                @else bg-gray-100 text-gray-800
                                                @endif">
                                                {{ ucfirst($status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            @php
                                                $count = collect($paymentsByStatus)->get($status, collect())->count();
                                            @endphp
                                            {{ $count }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                            Rp{{ number_format($amount / 100, 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $totalAmount > 0 ? number_format(($amount / $totalAmount) * 100, 1) : '0' }}%
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-gray-50 font-semibold">
                                    <tr>
                                        <td colspan="2" class="px-6 py-4">Total</td>
                                        <td class="px-6 py-4 text-lg">Rp{{ number_format($totalAmount / 100, 0, ',', '.') }}</td>
                                        <td class="px-6 py-4">100%</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <!-- Summary by Payment Method -->
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Summary by Payment Method</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Method</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Count</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Amount</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Percentage</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @php
                                        $methodTotal = collect($report['by_method'])->values()->sum();
                                    @endphp
                                    @foreach($report['by_method'] as $method => $amount)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ ucfirst($method) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            @php
                                                $count = collect($paymentsByMethod)->get($method, collect())->count();
                                            @endphp
                                            {{ $count }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                            Rp{{ number_format($amount / 100, 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $methodTotal > 0 ? number_format(($amount / $methodTotal) * 100, 1) : '0' }}%
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-gray-50 font-semibold">
                                    <tr>
                                        <td colspan="2" class="px-6 py-4">Total</td>
                                        <td class="px-6 py-4 text-lg">Rp{{ number_format($methodTotal / 100, 0, ',', '.') }}</td>
                                        <td class="px-6 py-4">100%</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <!-- Summary by Provider -->
                    @if(!empty($report['by_provider']))
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Summary by Provider</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Provider</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Count</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Amount</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Percentage</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @php
                                        $providerTotal = collect($report['by_provider'])->values()->sum();
                                    @endphp
                                    @foreach($report['by_provider'] as $provider => $amount)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $provider ?? 'Unknown' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            @php
                                                $count = collect($paymentsByProvider)->get($provider, collect())->count();
                                            @endphp
                                            {{ $count }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                            Rp{{ number_format($amount / 100, 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $providerTotal > 0 ? number_format(($amount / $providerTotal) * 100, 1) : '0' }}%
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-gray-50 font-semibold">
                                    <tr>
                                        <td colspan="2" class="px-6 py-4">Total</td>
                                        <td class="px-6 py-4 text-lg">Rp{{ number_format($providerTotal / 100, 0, ',', '.') }}</td>
                                        <td class="px-6 py-4">100%</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    @endif
                </div>
            </main>
        </div>
    </div>

    <style>
        @media print {
            body {
                background: white;
            }
            .ml-64 {
                margin-left: 0;
            }
            .hidden-on-print {
                display: none;
            }
        }
    </style>
</body>
</html>
