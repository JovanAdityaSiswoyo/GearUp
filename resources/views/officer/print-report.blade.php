@extends('layouts.officer')

@section('title', 'Print Report')

@section('content')
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center space-x-3 mb-2">
                <x-heroicon-o-document-text class="h-8 w-8 text-blue-600" />
                <h1 class="text-3xl font-bold text-gray-900">Laporan Peminjaman & Pengembalian</h1>
            </div>
            <p class="text-gray-600">Cetak laporan transaksi peminjaman dan pengembalian barang</p>
        </div>

        <div class="print:hidden mb-6 bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-sm font-semibold text-gray-700 mb-3 uppercase">Filter Laporan</h3>
            <form method="GET" action="{{ route('officer.reports.print') }}" class="flex flex-wrap gap-4 items-end">
                <div class="flex-1 min-w-40">
                    <label class="block text-xs font-semibold mb-1 text-gray-700">Dari Tanggal</label>
                    <input type="date" name="from" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent" value="{{ request('from') }}">
                </div>
                <div class="flex-1 min-w-40">
                    <label class="block text-xs font-semibold mb-1 text-gray-700">Sampai Tanggal</label>
                    <input type="date" name="to" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent" value="{{ request('to') }}">
                </div>
                <div class="flex-1 min-w-40">
                    <label class="block text-xs font-semibold mb-1 text-gray-700">Tipe</label>
                    <select name="type" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="all" {{ request('type') == 'all' ? 'selected' : '' }}>Semua</option>
                        <option value="loan" {{ request('type') == 'loan' ? 'selected' : '' }}>Peminjaman</option>
                        <option value="return" {{ request('type') == 'return' ? 'selected' : '' }}>Pengembalian</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition flex items-center gap-2">
                        <x-heroicon-o-funnel class="h-5 w-5" />
                        Filter
                    </button>
                    <button type="button" onclick="window.print()" class="px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg font-medium transition flex items-center gap-2">
                        <x-heroicon-o-printer class="h-5 w-5" />
                        Print
                    </button>
                </div>
            </form>
        </div>

        <!-- Print Header -->
        <div class="hidden print:block text-center mb-6">
            <h2 class="text-2xl font-bold">Laporan Peminjaman & Pengembalian Barang</h2>
            @if(request('from') || request('to'))
                <p class="text-sm text-gray-600 mt-2">
                    Periode: 
                    {{ request('from') ? \Carbon\Carbon::parse(request('from'))->format('d M Y') : 'Awal' }}
                    s/d
                    {{ request('to') ? \Carbon\Carbon::parse(request('to'))->format('d M Y') : 'Sekarang' }}
                </p>
            @endif
            @if(request('type') != 'all')
                <p class="text-sm text-gray-600">Tipe: {{ request('type') == 'loan' ? 'Peminjaman' : 'Pengembalian' }}</p>
            @endif
            <p class="text-xs text-gray-500 mt-1">Dicetak pada: {{ \Carbon\Carbon::now()->format('d M Y H:i') }}</p>
        </div>

        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase w-20">Gambar</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode Booking</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Penyewa</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Barang</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status Order</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase print:hidden">Status Barang</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($reports as $index => $report)
                        @php
                            $itemType = $report->product ? 'Product' : 'Package';
                            $itemImage = $report->product ? $report->product->image : ($report->package ? $report->package->image : null);
                            $itemName = $report->product ? $report->product->name : ($report->package ? $report->package->name_package : 'N/A');
                            $itemTypeClass = $itemType === 'Product' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800';
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-4 text-center">
                                @if($itemImage)
                                    <img src="{{ asset('storage/' . $itemImage) }}" alt="{{ $itemName }}" class="w-16 h-16 rounded object-cover mx-auto">
                                @else
                                    <div class="w-16 h-16 rounded bg-gray-200 flex items-center justify-center mx-auto">
                                        <x-heroicon-o-photo class="h-6 w-6 text-gray-400" />
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold text-gray-900">{{ $report->book_code ?? $report->code ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $itemTypeClass }}">
                                    {{ $itemType }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $displayDate = null;
                                    // Tentukan tanggal yang relevan berdasarkan order_status
                                    if (in_array($report->order_status?->value, ['Delivered', 'Out for Delivery'])) {
                                        $displayDate = $report->delivery_at;
                                    } elseif (in_array($report->order_status?->value, ['Completed', 'Pending Review', 'On Process Return'])) {
                                        $displayDate = $report->returned_at ?? $report->delivery_at;
                                    } else {
                                        $displayDate = $report->created_at;
                                    }
                                @endphp
                                <div class="text-sm text-gray-900">{{ $displayDate ? $displayDate->format('d M Y') : '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $report->user->name ?? '-' }}</div>
                                <div class="text-xs text-gray-500">{{ $report->booker_email ?? $report->user->email ?? '' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $itemName }}</div>
                                <div class="text-xs text-gray-500">Jumlah: {{ $report->amount ?? 1 }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $orderStatus = $report->order_status;
                                    $statusColors = [
                                        'Draft' => 'bg-gray-100 text-gray-800',
                                        'Awaiting Validation' => 'bg-yellow-100 text-yellow-800',
                                        'Confirmed' => 'bg-blue-100 text-blue-800',
                                        'Ready for Pickup' => 'bg-indigo-100 text-indigo-800',
                                        'Out for Delivery' => 'bg-purple-100 text-purple-800',
                                        'Delivered' => 'bg-green-100 text-green-800',
                                        'Pickup Scheduled' => 'bg-orange-100 text-orange-800',
                                        'On Process Return' => 'bg-teal-100 text-teal-800',
                                        'Pending Review' => 'bg-amber-100 text-amber-800',
                                        'Completed' => 'bg-emerald-100 text-emerald-800',
                                        'Issue Detected' => 'bg-red-100 text-red-800',
                                        'Overdue' => 'bg-red-100 text-red-800',
                                        'Cancelled' => 'bg-gray-100 text-gray-800',
                                    ];
                                    $colorClass = $statusColors[$orderStatus?->value] ?? 'bg-gray-100 text-gray-800';
                                @endphp
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $colorClass }}">
                                    {{ $orderStatus?->label() ?? '-' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap print:hidden">
                                @if($report->item_status)
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-slate-100 text-slate-700">
                                        {{ $report->item_status->label() }}
                                    </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-8 text-center">
                                <x-heroicon-o-inbox class="h-12 w-12 text-gray-300 mx-auto mb-3" />
                                <p class="text-gray-600">Tidak ada data untuk filter yang dipilih</p>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($reports->isNotEmpty())
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    <div class="text-sm font-semibold text-gray-700">Total: {{ $reports->total() }} transaksi</div>
                </div>
            @endif

            @if($reports->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 print:hidden">
                    {{ $reports->appends(request()->query())->links() }}
                </div>
            @endif
        </div>

<style>
    @media print {
        body * {
            visibility: hidden;
        }
        .bg-white, .bg-white * {
            visibility: visible;
        }
        .bg-white {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        .print\:hidden {
            display: none !important;
        }
        .print\:block {
            display: block !important;
        }
    }
</style>
@endsection
