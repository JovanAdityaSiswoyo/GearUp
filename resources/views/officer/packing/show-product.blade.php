@extends('layouts.officer')

@section('title', 'Packing Product')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="bg-white rounded-xl shadow-sm p-6 mb-6">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div>
            <p class="text-sm text-gray-500 sub-header mb-1">Customer</p>
            <p class="text-lg font-semibold text-gray-800">{{ $booking->booker_name }}</p>
            <p class="text-sm text-gray-600">{{ $booking->booker_email }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-500 sub-header mb-1">Product</p>
            <p class="text-lg font-semibold text-gray-800">{{ $booking->product->name ?? 'N/A' }}</p>
            <p class="text-sm text-gray-600">Booking Code: {{ $booking->book_code }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-500 sub-header mb-1">Rental Period</p>
            <p class="text-lg font-semibold text-gray-800">
                {{ \Carbon\Carbon::parse($booking->checkin_appointment_start)->format('d M') }} -
                {{ \Carbon\Carbon::parse($booking->checkout_appointment_end)->format('d M Y') }}
            </p>
        </div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm p-6 mb-6">
    <div class="flex items-center justify-between mb-4">
        <div>
            <p class="text-sm text-gray-500 sub-header mb-1">Packing Progress</p>
            <p class="text-3xl font-bold text-gray-800">{{ $packingProgress['packed'] }}/{{ $packingProgress['total'] }} item</p>
        </div>
        <div class="text-right">
            <p class="text-4xl font-bold text-blue-600">{{ $packingProgress['percentage'] }}%</p>
            <p class="text-sm text-gray-500 sub-header">Complete</p>
        </div>
    </div>
    <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
        <div class="h-full transition-all duration-300 bg-gradient-to-r from-blue-600 to-cyan-500" style="width: {{ $packingProgress['percentage'] }}%"></div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm p-6 mb-6">
    <h3 class="text-xl font-semibold mb-2">📦 Product Packing</h3>
    <p class="text-gray-700 mb-2">Booking produk tidak memerlukan assignment unit seperti package.</p>
    <p class="text-sm text-gray-500">Klik <strong>Finalize Packing</strong> untuk mengubah status menjadi <strong>Ready for Pickup</strong>.</p>
</div>

<div class="flex gap-4 mt-6">
    <a href="{{ route('officer.packing.index') }}" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-3 rounded-lg transition text-center font-semibold">
        ← Back
    </a>
    @if($packingProgress['is_complete'])
        <button disabled class="flex-1 bg-green-100 text-green-800 px-6 py-3 rounded-lg cursor-not-allowed font-semibold">
            Packing Sudah Selesai
        </button>
    @else
        <button onclick="finalizePacking()" class="flex-1 bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg transition font-semibold">
            Finalize Packing ✓
        </button>
    @endif
</div>

<script>
function finalizePacking() {
    Swal.fire({
        title: 'Finalize Packing?',
        text: 'Booking status akan berubah menjadi READY_FOR_PICKUP',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, finalize it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('{{ route("officer.packing.finalize", $booking->id) }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Success!',
                        text: data.message,
                        icon: 'success',
                        confirmButtonColor: '#10b981',
                        timer: 2000,
                        timerProgressBar: true
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            })
            .catch(() => {
                Swal.fire('Error', 'Gagal finalize packing', 'error');
            });
        }
    });
}
</script>
@endsection
