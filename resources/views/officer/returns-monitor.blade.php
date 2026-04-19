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
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($returns as $return)
                        @php
                            $isOverdue = $return->checkout_appointment_end && $return->checkout_appointment_end->isPast();
                            $itemName = $return->product ? $return->product->name : ($return->package ? $return->package->name_package : 'N/A');
                            $itemType = $return->product ? 'Product' : 'Package';
                            $rowClass = $isOverdue
                                ? 'border-red-400 bg-red-50'
                                : match ($return->order_status->value) {
                                    'dipinjam' => 'border-blue-400',
                                    'selesai' => 'border-emerald-400',
                                    default => 'border-gray-400',
                                };
                            $typeClass = $itemType === 'Product' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800';
                            $dueClass = $isOverdue ? 'text-red-600' : 'text-gray-900';
                            $orderStatusClass = match ($return->order_status->value) {
                                'pending' => 'bg-amber-100 text-amber-800',
                                'dipinjam' => 'bg-blue-100 text-blue-800',
                                'selesai' => 'bg-emerald-100 text-emerald-800',
                                default => 'bg-gray-100 text-gray-800',
                            };
                            $itemStatusClass = match ($return->item_status->value) {
                                'Deployed' => 'bg-green-100 text-green-800',
                                'Returning' => 'bg-yellow-100 text-yellow-800',
                                'In-Inspection' => 'bg-orange-100 text-orange-800',
                                'Available' => 'bg-emerald-100 text-emerald-800',
                                default => 'bg-gray-100 text-gray-800',
                            };
                            $bookingType = $return->product ? 'product' : 'package';
                        @endphp
                        <tr class="hover:bg-gray-50 border-l-4 {{ $rowClass }}">
                            <td class="px-4 py-4 text-center">
                                @if($return->product && $return->product->image)
                                    <img src="{{ asset('storage/' . $return->product->image) }}" alt="{{ $return->product->name }}" class="w-16 h-16 rounded object-cover mx-auto">
                                @elseif($return->package && $return->package->image)
                                    <img src="{{ asset('storage/' . $return->package->image) }}" alt="{{ $return->package->name_package }}" class="w-16 h-16 rounded object-cover mx-auto">
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
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $typeClass }}">
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
                                    <div class="text-sm font-medium {{ $dueClass }}">
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
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full whitespace-nowrap {{ $orderStatusClass }}">
                                    {{ $return->order_status->label() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full whitespace-nowrap {{ $itemStatusClass }}">
                                    {{ $return->item_status->label() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('officer.bookings.show', ['type' => $bookingType, 'bookingId' => $return->id]) }}" class="text-blue-600 hover:text-blue-900 font-medium mr-3">
                                    Detail
                                </a>
                                @if($return->item_status->value == 'Deployed')
                                    <form id="start-return-form-{{ $return->id }}" action="{{ route('officer.returns.start-return', $return->id) }}" method="POST" class="inline" onsubmit="return confirmReturnAction(event, 'start-return-form-{{ $return->id }}', 'Mulai Return?', 'Tandai item sedang dikembalikan?', 'Ya, Mulai', '#2563EB')">
                                        @csrf
                                        <button type="submit" class="text-blue-600 hover:text-blue-900 font-medium">
                                            Mulai Return
                                        </button>
                                    </form>
                                @elseif($return->item_status->value == 'Returning')
                                    <form id="start-inspection-form-{{ $return->id }}" action="{{ route('officer.returns.start-inspection', $return->id) }}" method="POST" class="inline" onsubmit="return confirmReturnAction(event, 'start-inspection-form-{{ $return->id }}', 'Masuk Inspeksi?', 'Pindahkan item ke tahap inspeksi?', 'Ya, Pindahkan', '#F97316')">
                                        @csrf
                                        <button type="submit" class="text-orange-600 hover:text-orange-900 font-medium">
                                            Masuk Inspeksi
                                        </button>
                                    </form>
                                @elseif($return->item_status->value == 'In-Inspection')
                                    <form id="complete-return-form-{{ $return->id }}" action="{{ route('officer.returns.process', $return->id) }}" method="POST" 
                                          onsubmit="return confirmReturnAction(event, 'complete-return-form-{{ $return->id }}', 'Complete Return?', 'Confirm that the item has been inspected and is in good condition?', 'Ya, Complete', '#10B981')"
                                          class="inline">
                                        @csrf
                                        <button type="submit" 
                                            class="text-emerald-600 hover:text-emerald-900 font-medium">
                                            Complete
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs text-gray-500">Selesai</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-8 text-center">
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
        <script>
            function confirmReturnAction(event, formId, title, text, confirmText, confirmButtonColor) {
                event.preventDefault();

                Swal.fire({
                    title,
                    text,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor,
                    cancelButtonColor: '#6B7280',
                    confirmButtonText: confirmText,
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById(formId).submit();
                    }
                });

                return false;
            }
        </script>
@endsection
