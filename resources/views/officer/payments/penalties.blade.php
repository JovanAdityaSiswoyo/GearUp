@extends('layouts.officer')

@section('title', 'Denda User')

@section('content')
    @php
        $conditionLabels = [
            'rusak_ringan' => 'Rusak Ringan',
            'rusak_sedang' => 'Rusak Sedang',
            'rusak_berat' => 'Rusak Berat',
            'hilang' => 'Hilang',
        ];
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm p-5">
            <p class="text-sm text-gray-600">Total Denda</p>
            <p class="text-2xl font-bold text-gray-900">{{ $summary['total_penalties'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5">
            <p class="text-sm text-gray-600">Belum Bayar</p>
            <p class="text-2xl font-bold text-amber-600">{{ $summary['unpaid_count'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5">
            <p class="text-sm text-gray-600">Sudah Bayar</p>
            <p class="text-2xl font-bold text-emerald-600">{{ $summary['paid_count'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5">
            <p class="text-sm text-gray-600">Nominal Belum Bayar</p>
            <p class="text-lg font-bold text-amber-700">Rp {{ number_format($summary['unpaid_amount'] / 100, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5">
            <p class="text-sm text-gray-600">Nominal Sudah Bayar</p>
            <p class="text-lg font-bold text-emerald-700">Rp {{ number_format($summary['paid_amount'] / 100, 0, ',', '.') }}</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <form method="GET" action="{{ route('officer.penalties.index') }}" class="grid grid-cols-1 xl:grid-cols-[1fr_auto] gap-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari kode booking / reference"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Tanggal Mulai</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Tanggal Akhir</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
            </div>
            <div class="flex flex-wrap items-end gap-3">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg transition">Cari</button>
                <a href="{{ route('officer.penalties.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-5 py-2 rounded-lg transition">Reset</a>
                <a href="{{ route('officer.penalties.export-pdf', request()->query()) }}" class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-lg transition">Export PDF</a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-8">
        <div class="px-6 py-4 border-b bg-amber-50">
            <h3 class="text-lg font-bold text-amber-900">Denda Belum Bayar</h3>
            <p class="text-sm text-amber-700">User di bawah ini belum melunasi denda.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Booking</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kondisi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Denda</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($unpaidPenalties as $payment)
                        @php
                            $payable = $payment->payable;
                            $user = $payable?->user;
                            $condition = data_get($payment->meta, 'issue_condition', $payable?->issue_condition);
                            $conditionLabel = $conditionLabels[$condition] ?? '-';
                            $percentage = data_get($payment->meta, 'fine_percentage', $payable?->fine_percentage);
                            $bookCode = data_get($payment->meta, 'book_code', $payable?->book_code);
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <p class="text-sm font-semibold text-gray-900">{{ $user?->name ?? '-' }}</p>
                                <p class="text-xs text-gray-500">{{ $user?->email ?? '-' }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm font-semibold text-gray-900">{{ $bookCode ?? '-' }}</p>
                                <p class="text-xs text-gray-500">{{ class_basename($payment->payable_type) }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm text-gray-900">{{ $conditionLabel }}</p>
                                @if($percentage)
                                    <p class="text-xs text-gray-500">{{ $percentage }}%</p>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm font-bold text-amber-700">Rp {{ number_format($payment->amount / 100, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $payment->created_at?->format('d M Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-6 text-center text-gray-500">Tidak ada denda belum bayar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t">{{ $unpaidPenalties->links() }}</div>
    </div>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b bg-emerald-50">
            <h3 class="text-lg font-bold text-emerald-900">Denda Sudah Bayar</h3>
            <p class="text-sm text-emerald-700">Riwayat user yang sudah melunasi denda.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Booking</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kondisi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Denda</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Bayar</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($paidPenalties as $payment)
                        @php
                            $payable = $payment->payable;
                            $user = $payable?->user;
                            $condition = data_get($payment->meta, 'issue_condition', $payable?->issue_condition);
                            $conditionLabel = $conditionLabels[$condition] ?? '-';
                            $percentage = data_get($payment->meta, 'fine_percentage', $payable?->fine_percentage);
                            $bookCode = data_get($payment->meta, 'book_code', $payable?->book_code);
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <p class="text-sm font-semibold text-gray-900">{{ $user?->name ?? '-' }}</p>
                                <p class="text-xs text-gray-500">{{ $user?->email ?? '-' }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm font-semibold text-gray-900">{{ $bookCode ?? '-' }}</p>
                                <p class="text-xs text-gray-500">{{ class_basename($payment->payable_type) }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm text-gray-900">{{ $conditionLabel }}</p>
                                @if($percentage)
                                    <p class="text-xs text-gray-500">{{ $percentage }}%</p>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm font-bold text-emerald-700">Rp {{ number_format($payment->amount / 100, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ ($payment->paid_at ?? $payment->updated_at)?->format('d M Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-6 text-center text-gray-500">Belum ada denda yang dibayar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t">{{ $paidPenalties->links() }}</div>
    </div>
@endsection
