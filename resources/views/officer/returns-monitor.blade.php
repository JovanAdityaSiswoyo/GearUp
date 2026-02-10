@extends('layouts.officer')

@section('title', 'Monitor Returns')

@section('content')
<div class="bg-white rounded-xl shadow-md p-6">
    <h3 class="text-xl font-semibold mb-4 text-gray-800">Returns Monitoring</h3>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Equipment</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Returned At</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
            @foreach($returns as $return)
                <tr>
                    <td class="px-4 py-2">{{ $return->user->name }}</td>
                    <td class="px-4 py-2">{{ $return->equipment->name }}</td>
                    <td class="px-4 py-2">{{ $return->due_date->format('d M Y') }}</td>
                    <td class="px-4 py-2">
                        @if($return->is_overdue)
                            <span class="inline-block px-2 py-1 text-xs font-semibold bg-red-100 text-red-800 rounded">Overdue</span>
                        @elseif($return->returned_at)
                            <span class="inline-block px-2 py-1 text-xs font-semibold bg-green-100 text-green-800 rounded">Returned</span>
                        @else
                            <span class="inline-block px-2 py-1 text-xs font-semibold bg-yellow-100 text-yellow-800 rounded">Pending</span>
                        @endif
                    </td>
                    <td class="px-4 py-2">{{ $return->returned_at ? $return->returned_at->format('d M Y') : '-' }}</td>
                    <td class="px-4 py-2">
                        @if(!$return->returned_at)
                        <form action="{{ route('officer.returns.process', $return->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-3 py-1 bg-blue-500 hover:bg-blue-600 text-white text-xs rounded shadow">Mark as Returned</button>
                        </form>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    @if($returns->isEmpty())
        <div class="mt-4 p-4 bg-blue-50 text-blue-700 rounded">No returns to monitor.</div>
    @endif
    <div class="mt-4">
        {{ $returns->links() }}
    </div>
</div>
@endsection
