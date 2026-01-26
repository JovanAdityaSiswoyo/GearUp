@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-10">
    <div class="bg-white rounded-xl shadow p-8">
        <h1 class="text-2xl font-bold mb-6">Cart Produk</h1>

        {{-- Pilihan Brand --}}
        @php
            $brands = \App\Models\Brand::all();
        @endphp
        <div class="mb-8">
            <h2 class="text-lg font-semibold mb-2">Pilih Brand:</h2>
            <div class="flex flex-wrap gap-3">
                @foreach($brands as $brand)
                    <a href="{{ route('user.brand.products', $brand->id) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-teal-100 rounded-lg border border-gray-200 shadow-sm transition">
                        @if($brand->logo)
                            <img src="{{ asset('storage/' . $brand->logo) }}" alt="{{ $brand->name }}" class="w-8 h-8 object-contain rounded">
                        @endif
                        <span class="font-medium">{{ $brand->name }}</span>
                    </a>
                @endforeach
            </div>
        </div>
        {{-- <pre>{{ var_export(session('cart'), true) }}</pre> --}}
        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
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
                        <form action="{{ route('user.cart.remove', $product->id) }}" method="POST" class="form-remove-cart">
                            @csrf
                            <button type="submit" class="text-red-600 hover:text-red-800 font-semibold px-3 py-1 rounded btn-remove-cart">Hapus</button>
                        </form>
                    </li>
                @endforeach
            </ul>
            <!-- Summary -->
            @php
                $totalProduk = $products->count();
                $totalHarga = $products->sum('price_per_day');
            @endphp
            <div class="border-t pt-4 mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="text-gray-700 text-base">Total Produk: <span class="font-bold">{{ $totalProduk }}</span></div>
                <div class="text-gray-700 text-base">Total Harga / hari: <span class="font-bold text-teal-700">Rp {{ number_format($totalHarga, 0, ',', '.') }}</span></div>
            </div>
            <form action="{{ route('user.cart.checkout') }}" method="POST">
                @csrf
                <button type="submit" class="inline-block bg-teal-600 hover:bg-teal-700 text-white px-8 py-3 rounded-lg font-semibold text-lg shadow transition">Checkout</button>
            </form>
        @else
            <div class="text-gray-500">Cart masih kosong.</div>
        @endif
    </div>
</div>
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.querySelectorAll('.btn-remove-cart').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const form = btn.closest('form');
            Swal.fire({
                title: 'Hapus produk dari cart?',
                text: 'Produk akan dihapus dari cart Anda.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@endpush
@endsection
