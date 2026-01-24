@extends('layouts.app')

@section('content')

<div class="max-w-6xl mx-auto py-12 px-4 md:px-0">
    <div class="bg-white rounded-2xl shadow-lg p-8 flex flex-col md:flex-row gap-10 md:gap-16">
        <!-- Gallery Vertical -->
        <div id="gallery-thumbs" class="flex flex-row md:flex-col gap-2 md:gap-4 md:w-24 order-2 md:order-1 mb-4 md:mb-0">
            @php $thumbIndex = 0; @endphp
            @if($product->image)
                <img src="{{ asset('storage/' . $product->image) }}" class="rounded-lg w-16 h-16 md:w-20 md:h-20 object-cover border-2 border-teal-600 ring-2 ring-teal-400 cursor-pointer transition-all duration-150" alt="Main" onclick="setMainImage(this)" data-thumb data-index="0">
                @php $thumbIndex++; @endphp
            @endif
            @if($product->images && $product->images->count())
                @foreach($product->images as $img)
                    <img src="{{ asset('storage/' . $img->image) }}" class="rounded-lg w-16 h-16 md:w-20 md:h-20 object-cover border-2 border-gray-300 hover:border-teal-500 cursor-pointer transition-all duration-150" alt="Gallery" onclick="setMainImage(this)" data-thumb data-index="{{ $thumbIndex }}">
                    @php $thumbIndex++; @endphp
                @endforeach
            @endif
        </div>
        <!-- Main Image -->
        <div class="flex-1 flex items-center justify-center order-1 md:order-2">
            <div class="relative w-full max-w-lg aspect-[4/5] bg-white rounded-xl overflow-hidden flex items-center justify-center border-2 border-gray-200 shadow">
                <img id="mainImage" src="{{ $product->image ? asset('storage/' . $product->image) : (count($product->images ?? []) ? asset('storage/' . $product->images->first()->image) : '') }}" alt="{{ $product->name }}" class="object-cover w-full h-full cursor-pointer transition-all duration-200" style="background:transparent;" onclick="showModal(this)">
                <!-- Modal Preview -->
                <div id="imageModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-70 hidden">
                    <div class="relative">
                        <img id="modalImg" src="" class="max-h-[80vh] max-w-[90vw] rounded-lg shadow-2xl border-4 border-white" alt="Preview">
                        <button onclick="closeModal()" class="absolute top-2 right-2 bg-white rounded-full p-1 shadow hover:bg-gray-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Product Info -->
        <div class="flex-1 order-3 flex flex-col justify-center">
            <h1 class="text-3xl md:text-4xl font-bold mb-4 tracking-tight">{{ $product->name }}</h1>
            <div class="mb-3 text-base text-gray-500">Kategori: <span class="font-medium text-gray-700">{{ $product->category->categories ?? '-' }}</span></div>
            <div class="mb-3 text-base text-gray-500">Brand: <span class="font-medium text-gray-700">{{ $product->brand->name ?? '-' }}</span></div>
            <div class="mb-3 text-2xl font-bold text-teal-700">Rp {{ number_format($product->price_per_day, 0, ',', '.') }} <span class="text-base font-normal text-gray-500">/ hari</span></div>
            <div class="mb-3 text-base text-gray-500">Stok: <span class="font-medium text-gray-700">{{ $product->stock }} unit</span></div>
            <div class="mb-6 text-gray-700 leading-relaxed border-t pt-4">{{ $product->description }}</div>
            <form action="{{ route('user.cart.add', $product->id) }}" method="POST">
                @csrf
                <button type="submit" class="inline-block w-full md:w-auto text-center bg-teal-600 hover:bg-teal-700 text-white text-lg font-semibold px-10 py-3 rounded-lg shadow transition">Tambah ke Cart</button>
            </form>
        </div>
    </div>
</div>


@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        window.showModal = function(img) {
            const modal = document.getElementById('imageModal');
            const modalImg = document.getElementById('modalImg');
            modalImg.src = img.src;
            modal.classList.remove('hidden');
        }
        window.closeModal = function() {
            document.getElementById('imageModal').classList.add('hidden');
        }
        // Close modal on background click
        document.getElementById('imageModal').addEventListener('click', function(e) {
            if (e.target === this) closeModal();
        });

        // Uniqlo-style: click gallery thumbnail set main image + highlight active
        let activeThumbIndex = 0;
        window.setMainImage = function(img) {
            const main = document.getElementById('mainImage');
            const newIndex = parseInt(img.getAttribute('data-index'));
            if (activeThumbIndex === newIndex && main.src === img.src) return;
            main.src = img.src;
            activeThumbIndex = newIndex;
            // Remove highlight from all thumbs
            document.querySelectorAll('[data-thumb]').forEach(el => {
                el.classList.remove('border-teal-600', 'ring-2', 'ring-teal-400');
                el.classList.add('border-gray-300');
            });
            // Highlight active thumb
            img.classList.remove('border-gray-300');
            img.classList.add('border-teal-600', 'ring-2', 'ring-teal-400');
        }
    });
</script>
@endsection

@endsection
