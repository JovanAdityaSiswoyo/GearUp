@extends('layouts.officer')

@section('title', 'Monitor Returns')

@section('content')
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center space-x-3 mb-2">
                <x-heroicon-o-arrow-uturn-right class="h-8 w-8 text-blue-600" />
                <h1 class="text-3xl font-bold text-gray-900">Monitoring Pengembalian</h1>
            </div>
            <p class="text-gray-600">Pantau semua pengembalian produk dan paket</p>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase w-20">Gambar</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode Booking</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Batas Kembali</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status Order</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status Item</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kurir</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($returns as $return)
                        @php
                            $isOverdue = $return->checkout_appointment_end && $return->checkout_appointment_end->isPast() && 
                                         in_array($return->order_status->value, ['Delivered', 'Pickup Scheduled']);
                            $itemName = $return->product ? $return->product->name : ($return->package ? $return->package->name : 'N/A');
                            $itemType = $return->product ? 'Product' : 'Package';
                        @endphp
                        <tr class="hover:bg-gray-50 border-l-4
                            @if($isOverdue) border-red-400 bg-red-50
                            @elseif($return->order_status->value == 'Delivered') border-green-400
                            @elseif($return->order_status->value == 'Pickup Scheduled') border-blue-400
                            @elseif($return->order_status->value == 'On Process Return') border-yellow-400
                            @elseif($return->order_status->value == 'Pending Review') border-orange-400
                            @else border-gray-400
                            @endif">
                            <td class="px-4 py-4 text-center">
                                @if($return->product && $return->product->image)
                                    <img src="{{ asset('storage/' . $return->product->image) }}" alt="{{ $return->product->name }}" class="w-16 h-16 rounded object-cover mx-auto">
                                @elseif($return->package && $return->package->image)
                                    <img src="{{ asset('storage/' . $return->package->image) }}" alt="{{ $return->package->name }}" class="w-16 h-16 rounded object-cover mx-auto">
                                @else
                                    <div class="w-16 h-16 rounded bg-gray-200 flex items-center justify-center mx-auto">
                                        <x-heroicon-o-photo class="h-6 w-6 text-gray-400" />
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold text-gray-900">{{ $return->book_code }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if($itemType == 'Product') bg-blue-100 text-blue-800
                                    @else bg-purple-100 text-purple-800 @endif">
                                    {{ $itemType }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $itemName }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $return->user->name }}</div>
                                <div class="text-xs text-gray-500">{{ $return->user->email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
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
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full whitespace-nowrap
                                    @if($return->order_status->value == 'Delivered') bg-green-100 text-green-800
                                    @elseif($return->order_status->value == 'Pickup Scheduled') bg-blue-100 text-blue-800
                                    @elseif($return->order_status->value == 'On Process Return') bg-yellow-100 text-yellow-800
                                    @elseif($return->order_status->value == 'Pending Review') bg-orange-100 text-orange-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ $return->order_status->label() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full whitespace-nowrap
                                    @if($return->item_status->value == 'Deployed') bg-green-100 text-green-800
                                    @elseif($return->item_status->value == 'Returning') bg-yellow-100 text-yellow-800
                                    @elseif($return->item_status->value == 'In-Inspection') bg-orange-100 text-orange-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ $return->item_status->label() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($return->courier)
                                    <div class="text-sm text-gray-900">{{ $return->courier->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $return->courier->phone }}</div>
                                @else
                                    <span class="text-xs text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                @if($return->order_status->value == 'Pending Review')
                                    <form action="{{ route('officer.returns.process', $return->id) }}" method="POST" 
                                          onsubmit="return confirm('Confirm that the item has been inspected and is in good condition?')"
                                          class="inline">
                                        @csrf
                                        <button type="submit" 
                                            class="text-emerald-600 hover:text-emerald-900 font-medium">
                                            Complete
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs text-gray-500">
                                        @if($return->order_status->value == 'Delivered')
                                            <span class="text-gray-400">Waiting</span>
                                        @elseif($return->order_status->value == 'Pickup Scheduled')
                                            <span class="text-blue-600">Assigned</span>
                                        @elseif($return->order_status->value == 'On Process Return')
                                            <span class="text-yellow-600">In transit</span>
                                        @endif
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-6 py-8 text-center">
                                <x-heroicon-o-inbox class="h-12 w-12 text-gray-300 mx-auto mb-3" />
                                <p class="text-gray-600">Tidak ada pengembalian yang harus dimonitor</p>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            @if($returns->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $returns->links() }}
                </div>
            @endif
        </div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection
