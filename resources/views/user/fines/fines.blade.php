@extends('layouts.app')

@section('content')
<div class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Denda User</h1>
            <p class="mt-1 text-sm text-gray-600">Kelola dan bayar denda peminjaman peralatan</p>
        </div>

        {{-- Flash message --}}
        @if (session('success'))
            <div
                x-data="{ show: true }"
                x-show="show"
                x-init="setTimeout(() => show = false, 4000)"
                x-transition:leave="transition ease-in duration-300"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 -translate-y-2"
                class="mb-6 flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 text-sm px-4 py-3 rounded-xl"
            >
                <svg class="w-4 h-4 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        {{-- Stats --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-2xl border border-gray-100 px-5 py-4">
                <p class="text-xs text-gray-400 mb-1">Total Denda</p>
                <p class="text-2xl font-semibold text-gray-800">{{ $totalFines }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 px-5 py-4">
                <p class="text-xs text-gray-400 mb-1">Pending</p>
                <p class="text-2xl font-semibold text-amber-500">{{ $totalPending }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 px-5 py-4">
                <p class="text-xs text-gray-400 mb-1">Lunas</p>
                <p class="text-2xl font-semibold text-green-500">{{ $totalPaid }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 px-5 py-4">
                <p class="text-xs text-gray-400 mb-1">Total Tagihan</p>
                <p class="text-2xl font-semibold text-blue-500">
                    Rp {{ number_format($totalAmount, 0, ',', '.') }}
                </p>
            </div>
        </div>

        {{-- Cards --}}
        @if ($fines->isEmpty())
            <div class="text-center py-20 text-gray-400 text-sm">
                Tidak ada data denda.
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach ($fines as $fine)
                    <div class="bg-white rounded-2xl border border-gray-100 p-5 flex flex-col gap-4 hover:shadow-sm transition-shadow">

                        {{-- Card header --}}
                        @php
                            $bookerName = $fine->payable->booker_name ?? data_get($fine->meta, 'booker_name', 'Unknown');
                            $bookCode = $fine->payable->book_code ?? data_get($fine->meta, 'book_code', '-');
                            $productName = optional($fine->payable->product)->name ?? optional($fine->payable->package)->name_package ?? '-';
                        @endphp
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-center gap-3">
                                {{-- Avatar initials --}}
                                <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center text-blue-700 text-xs font-semibold shrink-0">
                                    {{ strtoupper(substr($bookerName, 0, 2)) }}
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-800 leading-tight">{{ $bookerName }}</p>
                                    <p class="text-xs text-gray-400 mt-0.5">{{ $bookCode }}</p>
                                </div>
                            </div>

                            {{-- Badge status --}}
                            @if ($fine->status === 'pending')
                                <span class="shrink-0 text-xs font-medium bg-amber-50 text-amber-700 border border-amber-100 px-2.5 py-1 rounded-full">
                                    Pending
                                </span>
                            @else
                                <span class="shrink-0 text-xs font-medium bg-green-50 text-green-700 border border-green-100 px-2.5 py-1 rounded-full">
                                    Lunas
                                </span>
                            @endif
                        </div>

                        <hr class="border-gray-100">

                        {{-- Detail --}}
                        <div class="flex flex-col gap-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-400">Jenis Denda</span>
                                <span class="text-gray-700 font-medium">{{ ucfirst($fine->method) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-400">Jumlah Denda</span>
                                <span class="text-blue-600 font-semibold">
                                    Rp {{ number_format($fine->amount / 100, 0, ',', '.') }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-400">Tanggal Dibuat</span>
                                <span class="text-gray-700 font-medium">
                                    {{ \Carbon\Carbon::parse($fine->created_at)->format('d M Y') }}
                                </span>
                            </div>
                            @if($productName !== '-')
                                <div class="flex justify-between">
                                    <span class="text-gray-400">Produk</span>
                                    <span class="text-gray-700 font-medium text-right max-w-[60%] truncate">
                                        {{ $productName }}
                                    </span>
                                </div>
                            @endif
                        </div>

                        {{-- Action button --}}
                        <div class="mt-auto pt-1">
                            @if ($fine->status === 'pending')
                                <form
                                    action="{{ route('fines.pay', $fine->id) }}"
                                    method="POST"
                                    x-data
                                    @submit.prevent="
                                        if (confirm('Bayar denda sebesar Rp {{ number_format($fine->amount / 100, 0, ',', '.') }}?\nPembayaran akan diproses langsung.'))
                                            $el.submit()
                                    "
                                >
                                    @csrf
                                    @method('PATCH')
                                    <button
                                        type="submit"
                                        class="w-full py-2.5 rounded-xl bg-green-600 hover:bg-green-700 active:scale-[0.98] text-white text-sm font-medium transition-all"
                                    >
                                        Bayar Sekarang
                                    </button>
                                </form>
                            @else
                                <button
                                    disabled
                                    class="w-full py-2.5 rounded-xl bg-gray-50 text-gray-300 text-sm font-medium border border-gray-100 cursor-not-allowed"
                                >
                                    Sudah Lunas
                                </button>
                            @endif
                        </div>

                    </div>
                @endforeach
            </div>
        @endif

    </div>
</div>
@endsection