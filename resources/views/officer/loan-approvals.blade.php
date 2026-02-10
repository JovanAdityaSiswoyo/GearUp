@extends('layouts.officer')

@section('title', 'Loan Approvals')

@section('content')
<div class="bg-white rounded-xl shadow-md p-6">
    <h3 class="text-xl font-semibold mb-4 text-gray-800">Pending Equipment Loan Requests</h3>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Equipment</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Request Date</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
            @foreach($pendingLoans as $loan)
                <tr>
                    <td class="px-4 py-2">{{ $loan->user->name }}</td>
                    <td class="px-4 py-2">{{ $loan->equipment->name }}</td>
                    <td class="px-4 py-2">{{ $loan->created_at->format('d M Y') }}</td>
                    <td class="px-4 py-2">
                        <span class="inline-block px-2 py-1 text-xs font-semibold bg-yellow-100 text-yellow-800 rounded">Pending</span>
                    </td>
                    <td class="px-4 py-2 flex gap-2">
                        <form action="{{ route('officer.loans.approve', $loan->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-3 py-1 bg-green-500 hover:bg-green-600 text-white text-xs rounded shadow">Approve</button>
                        </form>
                        <form action="{{ route('officer.loans.reject', $loan->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-3 py-1 bg-red-500 hover:bg-red-600 text-white text-xs rounded shadow">Reject</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    @if($pendingLoans->isEmpty())
        <div class="mt-4 p-4 bg-blue-50 text-blue-700 rounded">No pending requests.</div>
    @endif
    <div class="mt-4">
        {{ $pendingLoans->links() }}
    </div>
</div>
@endsection
