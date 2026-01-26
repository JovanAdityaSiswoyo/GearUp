@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto py-10">
    <div class="bg-white rounded-xl shadow p-8">
        <h1 class="text-2xl font-bold mb-6">Produk dari Brand: {{ $brand->name }}</h1>
        @if($products->count())
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($products as $product)
                    <div class="border rounded-lg p-4 flex flex-col items-center">
                        <img src="{{ asset('storage/' . $product->image) }}" class="w-32 h-32 object-cover rounded mb-2" alt="{{ $product->name }}">
                        <div class="font-semibold text-lg mb-1">{{ $product->name }}</div>
                        <div class="text-gray-500 text-sm mb-2">{{ $product->category->categories ?? '-' }}</div>
                        <div class="text-teal-700 font-bold mb-2">Rp {{ number_format($product->price_per_day, 0, ',', '.') }}/hari</div>
                        <a href="{{ route('user.product.show', $product->id) }}" class="bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded">Lihat Detail</a>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-gray-500">Tidak ada produk untuk brand ini.</div>
        @endif
    </div>
</div>
@endsection
