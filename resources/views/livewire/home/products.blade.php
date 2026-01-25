@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">Daftar Produk</h1>
    </div>

    <div id="productGrid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
        @foreach($products as $product)
            <a href="{{ route('user.product.show', $product->id) }}" class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl transition p-5 flex flex-col items-center border border-gray-100 hover:border-teal-400 cursor-pointer relative overflow-hidden">
                <div class="aspect-[4/3] w-full mb-4 rounded-xl overflow-hidden bg-gray-100 flex items-center justify-center">
                    @if($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="object-cover w-full h-full group-hover:scale-105 transition-transform duration-300">
                    @elseif($product->images && $product->images->count())
                        <img src="{{ asset('storage/' . $product->images->first()->image) }}" alt="{{ $product->name }}" class="object-cover w-full h-full group-hover:scale-105 transition-transform duration-300">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-gray-200">
                            <x-heroicon-o-photo class="h-16 w-16 text-gray-400" />
                        </div>
                    @endif
                </div>
                <h2 class="text-lg font-bold mb-1 text-gray-900 text-center group-hover:text-teal-700">{{ $product->name }}</h2>
                <p class="text-sm text-gray-500 mb-2 text-center line-clamp-2">{{ $product->description }}</p>
                <span class="text-teal-700 font-bold text-lg mb-2">Rp {{ number_format($product->price_per_day ?? $product->price, 0, ',', '.') }}</span>
                <span class="absolute top-3 right-3 bg-teal-500 text-white text-xs px-3 py-1 rounded-full shadow">Detail</span>
            </a>
        @endforeach
    </div>

    <div class="flex justify-center mt-8">
        @if($products->hasMorePages())
            <button id="loadMoreBtn" onclick="loadMoreProducts()" class="flex items-center gap-2 bg-teal-600 hover:bg-teal-700 text-white font-semibold px-6 py-2 rounded-lg shadow transition relative">
                <span id="loadMoreText">Muat Produk Lagi</span>
                <svg id="loadMoreSpinner" class="hidden animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                </svg>
            </button>
        @endif
    </div>

    <script>
    let currentPage = {{ $products->currentPage() }};
    const lastPage = {{ $products->lastPage() }};
    function loadMoreProducts() {
        const btn = document.getElementById('loadMoreBtn');
        const spinner = document.getElementById('loadMoreSpinner');
        const text = document.getElementById('loadMoreText');
        btn.disabled = true;
        spinner.classList.remove('hidden');
        text.textContent = 'Loading...';
        fetch(`?page=${currentPage + 1}`)
            .then(res => res.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newProducts = doc.querySelectorAll('#productGrid > a');
                const grid = document.getElementById('productGrid');
                newProducts.forEach(card => grid.appendChild(card));
                currentPage++;
                if (currentPage >= lastPage) {
                    btn.style.display = 'none';
                } else {
                    btn.disabled = false;
                    spinner.classList.add('hidden');
                    text.textContent = 'Muat Produk Lagi';
                }
            });
    }
    </script>

    <script>
    function showAddProductSpinner() {
        document.getElementById('addProductText').textContent = 'Loading...';
        document.getElementById('addProductSpinner').classList.remove('hidden');
        // Simulasi loading, ganti dengan aksi sebenarnya jika ada
        setTimeout(function() {
            document.getElementById('addProductText').textContent = 'Tambah Produk';
            document.getElementById('addProductSpinner').classList.add('hidden');
            // Redirect ke halaman tambah produk jika perlu
            window.location.href = '/admin/products/create';
        }, 1200);
    }
    </script>
</div>
@endsection
