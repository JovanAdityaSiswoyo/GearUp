@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto py-12 px-4 md:px-0">
    <div class="bg-white rounded-2xl shadow-lg p-8 flex flex-col md:flex-row gap-10 md:gap-16">
        <!-- Gallery Vertical (optional for package) -->
        @if($package->image)
        <div class="flex flex-row md:flex-col gap-2 md:gap-4 md:w-24 order-2 md:order-1 mb-4 md:mb-0">
            <img src="{{ asset('storage/' . $package->image) }}" class="rounded-lg w-16 h-16 md:w-20 md:h-20 object-cover border-2 border-teal-600 ring-2 ring-teal-400 cursor-pointer transition-all duration-150" alt="Main">
        </div>
        @endif
        <!-- Main Image -->
        <div class="flex-1 flex items-center justify-center order-1 md:order-2">
            <div class="relative w-full max-w-lg aspect-[4/5] bg-white rounded-xl overflow-hidden flex items-center justify-center border-2 border-gray-200 shadow">
                <img src="{{ $package->image ? asset('storage/' . $package->image) : '' }}" alt="{{ $package->name_package }}" class="object-cover w-full h-full" style="background:transparent;">
            </div>
        </div>
        <!-- Package Info -->
        <div class="flex-1 order-3 flex flex-col justify-center">
            <h1 class="text-3xl md:text-4xl font-bold mb-4 tracking-tight">{{ $package->name_package }}</h1>
            <div class="mb-3 text-base text-gray-500">Harga: <span class="font-medium text-teal-700">Rp {{ number_format($package->price, 0, ',', '.') }}</span></div>
            <div class="mb-6 text-gray-700 leading-relaxed border-t pt-4">{{ $package->description }}</div>
            <div class="mt-8">
                <a href="{{ route('user.booking.package.create', $package->id) }}" class="inline-block bg-green-600 hover:bg-green-700 text-white font-semibold px-8 py-3 rounded-lg shadow transition focus:outline-none focus:ring-2 focus:ring-green-400 focus:ring-offset-2">Booking Paket</a>
            </div>
        </div>
    </div>

    <!-- Daftar Produk dalam Paket -->
    <div class="mt-10">
        <h2 class="text-xl font-semibold mb-4">Daftar Produk dalam Paket:</h2>
        <ul class="space-y-4">
            @foreach($package->products as $product)
            <li class="flex items-center gap-4 p-4 bg-gray-50 rounded-lg shadow-sm">
                @if($product->image)
                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-16 h-16 object-cover rounded border">
                @endif
                <div class="flex-1">
                    <div class="font-bold text-lg">{{ $product->name }}</div>
                    <div class="text-sm text-gray-500">Kategori: {{ $product->category->categories ?? '-' }}</div>
                    <div class="text-sm text-gray-500">Brand: {{ $product->brand->name ?? '-' }}</div>
                </div>
            </li>
            @endforeach
        </ul>
    </div>
</div>
@endsection
