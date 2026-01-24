@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-10">
    <div class="bg-white rounded-xl shadow p-8">
        <h1 class="text-2xl font-bold mb-6">Booking Produk</h1>
        @if(session('error'))
            <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">{{ session('error') }}</div>
        @endif
        @if($products->count())
            <ul class="divide-y divide-gray-200 mb-6">
                @foreach($products as $product)
                    <li class="flex items-center py-4 gap-4">
                        <img src="{{ asset('storage/' . $product->image) }}" class="w-20 h-20 object-cover rounded-lg border" alt="{{ $product->name }}">
                        <div class="flex-1">
                            <div class="font-semibold text-lg">{{ $product->name }}</div>
                            <div class="text-gray-500 text-sm">{{ $product->category->categories ?? '-' }}</div>
                            <div class="text-teal-700 font-bold">Rp {{ number_format($product->price_per_day, 0, ',', '.') }}/hari</div>
                        </div>
                    </li>
                @endforeach
            </ul>
            <div class="text-right">
                <form action="{{ route('user.booking.create-multi') }}" method="GET">
                    @foreach($products as $product)
                        <input type="hidden" name="products[]" value="{{ $product->id }}">
                    @endforeach
                    <button type="submit" class="inline-block bg-teal-600 hover:bg-teal-700 text-white px-8 py-3 rounded-lg font-semibold text-lg shadow transition mt-6">Konfirmasi &amp; Lanjut Booking</button>
                </form>
            </div>
        @else
            <div class="text-gray-500">Tidak ada produk untuk dibooking.</div>
        @endif
    </div>
</div>
@endsection
