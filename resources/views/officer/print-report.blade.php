@extends('layouts.officer')

@section('title', 'Print Report')

@section('content')
<div class="bg-white rounded-xl shadow-md p-6">
    <div class="print:hidden">
        <h3 class="text-xl font-semibold mb-4 text-gray-800">Laporan Peminjaman & Pengembalian</h3>
        <form method="GET" action="{{ route('officer.reports.print') }}" class="mb-6 flex flex-wrap gap-4 items-end">
            <div>
                <label class="block text-xs font-semibold mb-1 text-gray-700">Dari Tanggal</label>
                <input type="date" name="from" class="border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="{{ request('from') }}">
            </div>
            <div>
                <label class="block text-xs font-semibold mb-1 text-gray-700">Sampai Tanggal</label>
                <input type="date" name="to" class="border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="{{ request('to') }}">
            </div>
            <div>
                <label class="block text-xs font-semibold mb-1 text-gray-700">Tipe</label>
                <select name="type" class="border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="all" {{ request('type') == 'all' ? 'selected' : '' }}>Semua</option>
                    <option value="loan" {{ request('type') == 'loan' ? 'selected' : '' }}>Peminjaman</option>
                    <option value="return" {{ request('type') == 'return' ? 'selected' : '' }}>Pengembalian</option>
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded shadow transition">Filter</button>
            <button type="button" onclick="window.print()" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded shadow transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Print
            </button>
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

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">No</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Kode Booking</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Tanggal</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Penyewa</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Barang</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider print:hidden">Kurir</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Status Order</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider print:hidden">Status Barang</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
            @forelse($reports as $index => $report)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm">{{ $reports->firstItem() + $index }}</td>
                    <td class="px-4 py-3 text-sm font-mono font-semibold">{{ $report->book_code ?? $report->code ?? '-' }}</td>
                    <td class="px-4 py-3 text-sm">
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
                        {{ $displayDate ? $displayDate->format('d M Y') : '-' }}
                    </td>
                    <td class="px-4 py-3 text-sm">
                        <div class="font-medium text-gray-900">{{ $report->user->name ?? '-' }}</div>
                        <div class="text-xs text-gray-500">{{ $report->booker_email ?? $report->user->email ?? '' }}</div>
                    </td>
                    <td class="px-4 py-3 text-sm">
                        <div class="font-medium">{{ $report->product->name ?? '-' }}</div>
                        <div class="text-xs text-gray-500">Jumlah: {{ $report->amount ?? 1 }}</div>
                    </td>
                    <td class="px-4 py-3 text-sm print:hidden">
                        {{ $report->courier->name ?? '-' }}
                    </td>
                    <td class="px-4 py-3 text-sm">
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
                        <span class="inline-block px-2 py-1 text-xs font-semibold {{ $colorClass }} rounded">
                            {{ $orderStatus?->label() ?? '-' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-sm print:hidden">
                        @if($report->item_status)
                            <span class="inline-block px-2 py-1 text-xs font-medium bg-slate-100 text-slate-700 rounded">
                                {{ $report->item_status->label() }}
                            </span>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                        Tidak ada data untuk filter yang dipilih.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
    
    @if($reports->isNotEmpty())
        <div class="mt-6 p-4 bg-gray-50 rounded-lg">
            <div class="text-sm font-semibold text-gray-700">Total: {{ $reports->total() }} transaksi</div>
        </div>
    @endif

    <div class="mt-4 print:hidden">
        {{ $reports->appends(request()->query())->links() }}
    </div>
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
