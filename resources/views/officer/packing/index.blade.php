@extends('layouts.officer')

@section('title', 'Packing Management')

@section('content')
                <!-- Search & Filter -->
                <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                    <form method="GET" action="{{ route('officer.packing.index') }}" class="flex gap-4">
                        <input 
                            type="text" 
                            name="search" 
                            placeholder="Cari booking code, nama customer..."
                            value="{{ request('search') }}"
                            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition">
                            Cari
                        </button>
                        @if(request('search'))
                            <a href="{{ route('officer.packing.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-lg transition">
                                Reset
                            </a>
                        @endif
                    </form>
                </div>

                <!-- Bookings List -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    @if($bookings->isEmpty())
                        <div class="p-12 text-center">
                            <x-heroicon-o-inbox class="h-16 w-16 text-gray-300 mx-auto mb-4" />
                            <p class="text-gray-500 text-lg">Tidak ada booking yang perlu packing</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50 border-b border-gray-200">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Booking Code</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($bookings as $booking)
                                        <tr class="hover:bg-gray-50 transition">
                                            <td class="px-6 py-4">
                                                <span class="font-semibold text-gray-800">{{ $booking->book_code }}</span>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div>
                                                    <p class="font-medium text-gray-800">{{ $booking->booker_name }}</p>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <span class="text-gray-800">{{ $booking->item_name }}</span>
                                            </td>
                                            <td class="px-6 py-4">
                                                <span class="px-2 py-1 text-xs font-semibold rounded 
                                                    {{ $booking->item_type === 'Product' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                                    {{ $booking->item_type }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4">
                                                @if($booking->order_status === \App\Enums\OrderStatus::CONFIRMED)
                                                    <span class="px-3 py-1 text-xs font-semibold bg-yellow-100 text-yellow-800 rounded-full">
                                                        Confirmed
                                                    </span>
                                                @elseif($booking->order_status === \App\Enums\OrderStatus::READY_FOR_PICKUP)
                                                    <span class="px-3 py-1 text-xs font-semibold bg-green-100 text-green-800 rounded-full">
                                                        Ready
                                                    </span>
                                                @else
                                                    <span class="px-3 py-1 text-xs font-semibold bg-gray-100 text-gray-800 rounded-full">
                                                        {{ $booking->order_status->label() }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4">
                                                <a href="{{ route('officer.packing.show', $booking->id) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                                                    <x-heroicon-o-arrow-right class="h-4 w-4 mr-2" />
                                                    Packing
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                            {{ $bookings->links('pagination::tailwind') }}
                        </div>
                    @endif
@endsection
