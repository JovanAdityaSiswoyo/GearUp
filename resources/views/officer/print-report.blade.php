@extends('layouts.officer')

@section('title', 'Print Report')

@section('content')
<div class="bg-white rounded-xl shadow-md p-6">
    <h3 class="text-xl font-semibold mb-4 text-gray-800">Loan & Return Report</h3>
    <form method="GET" action="{{ route('officer.reports.print') }}" class="mb-6 flex flex-wrap gap-4 items-end">
        <div>
            <label class="block text-xs font-semibold mb-1">From</label>
            <input type="date" name="from" class="border border-gray-300 rounded px-2 py-1" value="{{ request('from') }}">
        </div>
        <div>
            <label class="block text-xs font-semibold mb-1">To</label>
            <input type="date" name="to" class="border border-gray-300 rounded px-2 py-1" value="{{ request('to') }}">
        </div>
        <div>
            <label class="block text-xs font-semibold mb-1">Type</label>
            <select name="type" class="border border-gray-300 rounded px-2 py-1">
                <option value="all" {{ request('type') == 'all' ? 'selected' : '' }}>All</option>
                <option value="loan" {{ request('type') == 'loan' ? 'selected' : '' }}>Loan</option>
                <option value="return" {{ request('type') == 'return' ? 'selected' : '' }}>Return</option>
            </select>
        </div>
        <button type="submit" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded shadow">Filter</button>
        <button type="button" onclick="window.print()" class="px-4 py-2 bg-gray-400 hover:bg-gray-500 text-white rounded shadow ml-2">Print</button>
    </form>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Equipment</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
            @foreach($reports as $report)
                <tr>
                    <td class="px-4 py-2">{{ $report->status == 'returned' ? ($report->returned_at ? \Carbon\Carbon::parse($report->returned_at)->format('d M Y') : '-') : ($report->created_at ? \Carbon\Carbon::parse($report->created_at)->format('d M Y') : '-') }}</td>
                    <td class="px-4 py-2">{{ $report->user->name }}</td>
                    <td class="px-4 py-2">{{ $report->equipment->name }}</td>
                    <td class="px-4 py-2">
                        <span class="inline-block px-2 py-1 text-xs font-semibold {{ $report->status == 'returned' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }} rounded">
                            {{ $report->status == 'returned' ? 'Return' : 'Loan' }}
                        </span>
                    </td>
                    <td class="px-4 py-2">
                        @if($report->status == 'pending')
                            <span class="inline-block px-2 py-1 text-xs font-semibold bg-yellow-100 text-yellow-800 rounded">Pending</span>
                        @elseif($report->status == 'approved')
                            <span class="inline-block px-2 py-1 text-xs font-semibold bg-green-100 text-green-800 rounded">Approved</span>
                        @elseif($report->status == 'returned')
                            <span class="inline-block px-2 py-1 text-xs font-semibold bg-blue-100 text-blue-800 rounded">Returned</span>
                        @else
                            <span class="inline-block px-2 py-1 text-xs font-semibold bg-gray-100 text-gray-800 rounded">{{ ucfirst($report->status) }}</span>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    @if($reports->isEmpty())
        <div class="mt-4 p-4 bg-blue-50 text-blue-700 rounded">No data found for the selected filter.</div>
    @endif
    <div class="mt-4">
        {{ $reports->links() }}
    </div>
</div>
@endsection
