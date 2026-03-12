@extends('layouts.officer')

@section('title', 'Monitor Returns')

@section('content')
<div class="bg-white rounded-xl shadow-md p-6">
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-xl font-semibold text-gray-800">Returns Monitoring</h3>
        <div class="text-sm text-gray-600">
            <span class="font-medium">Total Items:</span> {{ $returns->total() }}
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    <!-- Legend -->
    <div class="mb-4 p-4 bg-gray-50 rounded-lg">
        <h4 class="text-sm font-semibold mb-2 text-gray-700">Status Legend:</h4>
        <div class="flex flex-wrap gap-3 text-xs">
            <div class="flex items-center gap-1">
                <span class="inline-block w-3 h-3 bg-green-400 rounded"></span>
                <span>Delivered (User menggunakan)</span>
            </div>
            <div class="flex items-center gap-1">
                <span class="inline-block w-3 h-3 bg-blue-400 rounded"></span>
                <span>Pickup Scheduled (Penjemputan dijadwalkan)</span>
            </div>
            <div class="flex items-center gap-1">
                <span class="inline-block w-3 h-3 bg-yellow-400 rounded"></span>
                <span>On Process Return (Sedang dikembalikan)</span>
            </div>
            <div class="flex items-center gap-1">
                <span class="inline-block w-3 h-3 bg-orange-400 rounded"></span>
                <span>Pending Review (Menunggu quality check)</span>
            </div>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">User</th>
                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-20 text-center">Type</th>
                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-28">Return Due</th>
                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-36">Order Status</th>
                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-36">Item Status</th>
                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Courier</th>
                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Action</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
            @forelse($returns as $return)
                @php
                    $isOverdue = $return->checkout_appointment_end && $return->checkout_appointment_end->isPast() && 
                                 in_array($return->order_status->value, ['Delivered', 'Pickup Scheduled']);
                    $itemName = $return->product ? $return->product->name : ($return->package ? $return->package->name : 'N/A');
                    $itemType = $return->product ? 'Product' : 'Package';
                @endphp
                <tr class="hover:bg-gray-50 @if($isOverdue) bg-red-50 @endif">
                    <td class="px-3 py-3">
                        <div class="text-sm font-medium text-gray-900 truncate max-w-[120px]" title="{{ $return->user->name }}">
                            {{ $return->user->name }}
                        </div>
                        <div class="text-xs text-gray-500 truncate max-w-[120px]" title="{{ $return->user->email }}">
                            {{ $return->user->email }}
                        </div>
                    </td>
                    <td class="px-3 py-3">
                        <div class="text-sm font-medium text-gray-900" title="{{ $itemName }}">{{ $itemName }}</div>
                        <div class="text-xs text-gray-500 font-mono">{{ $return->book_code }}</div>
                    </td>
                    <td class="px-3 py-3 text-center">
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full whitespace-nowrap
                            @if($itemType == 'Product') bg-blue-100 text-blue-800
                            @else bg-purple-100 text-purple-800 @endif">
                            {{ $itemType }}
                        </span>
                    </td>
                    <td class="px-3 py-3">
                        @if($return->checkout_appointment_end)
                            <div class="text-sm font-medium @if($isOverdue) text-red-600 @else text-gray-900 @endif">
                                {{ $return->checkout_appointment_end->format('d M Y') }}
                            </div>
                            <div class="text-xs text-gray-500">{{ $return->checkout_appointment_end->format('H:i') }}</div>
                            @if($isOverdue)
                                <div class="text-xs font-semibold text-red-600 mt-0.5">
                                    Overdue
                                </div>
                            @endif
                        @else
                            <span class="text-sm text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-3 py-3">
                        <span class="inline-block px-2 py-1 text-xs font-semibold rounded whitespace-nowrap
                            @if($return->order_status->value == 'Delivered') bg-green-100 text-green-800
                            @elseif($return->order_status->value == 'Pickup Scheduled') bg-blue-100 text-blue-800
                            @elseif($return->order_status->value == 'On Process Return') bg-yellow-100 text-yellow-800
                            @elseif($return->order_status->value == 'Pending Review') bg-orange-100 text-orange-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ $return->order_status->label() }}
                        </span>
                    </td>
                    <td class="px-3 py-3">
                        <span class="inline-block px-2 py-1 text-xs font-semibold rounded whitespace-nowrap
                            @if($return->item_status->value == 'Deployed') bg-green-100 text-green-800
                            @elseif($return->item_status->value == 'Returning') bg-yellow-100 text-yellow-800
                            @elseif($return->item_status->value == 'In-Inspection') bg-orange-100 text-orange-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ $return->item_status->label() }}
                        </span>
                    </td>
                    <td class="px-3 py-3">
                        @if($return->courier)
                            <div class="text-sm text-gray-900 truncate max-w-[120px]" title="{{ $return->courier->name }}">
                                {{ $return->courier->name }}
                            </div>
                            <div class="text-xs text-gray-500">{{ $return->courier->phone }}</div>
                        @else
                            <span class="text-xs text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-3 py-3">
                        @if($return->order_status->value == 'Pending Review')
                            <form action="{{ route('officer.returns.process', $return->id) }}" method="POST" 
                                  onsubmit="return confirm('Confirm that the item has been inspected and is in good condition?')">
                                @csrf
                                <button type="submit" 
                                    class="w-full px-2 py-1.5 bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-medium rounded shadow transition whitespace-nowrap">
                                    <i class="fas fa-check-circle"></i> Complete
                                </button>
                            </form>
                        @else
                            <div class="text-xs text-gray-500 text-center">
                                @if($return->order_status->value == 'Delivered')
                                    <span class="text-gray-400">Waiting</span>
                                @elseif($return->order_status->value == 'Pickup Scheduled')
                                    <span class="text-blue-600">Assigned</span>
                                @elseif($return->order_status->value == 'On Process Return')
                                    <span class="text-yellow-600">In transit</span>
                                @endif
                            </div>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                        <div class="flex flex-col items-center">
                            <i class="fas fa-inbox text-4xl mb-2 text-gray-300"></i>
                            <p>No returns to monitor at the moment.</p>
                        </div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    @if($returns->hasPages())
        <div class="mt-4">
            {{ $returns->links() }}
        </div>
    @endif
</div>
@endsection
