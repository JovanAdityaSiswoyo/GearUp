<?php

use Livewire\Component;

use App\Models\Product;
use App\Models\Package;

new class extends Component
{
    public function with(): array
    {
        return [
            'bestPicks' => Product::with('category', 'brand')
                ->where('status', 'active')
                ->orderBy('created_at', 'desc')
                ->take(12)
                ->get(),
                        'packages' => Package::orderBy('created_at', 'desc')
                            ->take(10)
                            ->get()
        ];
    }
};
?>

<div>
<!-- Hero Section with Background -->
<div class="min-h-[70vh] bg-cover bg-center relative" style="background-image: url('https://images.unsplash.com/photo-1478131143081-80f7f84ca84d?q=80&w=2070'); font-family: 'Poppins', sans-serif;">
    <!-- Content -->
    <div class="relative z-10">
        <!-- Header Atas: Kontak & Info -->
        <div id="top-header" class="w-full bg-transparent px-6 lg:px-16 py-2 flex flex-col md:flex-row items-center justify-between text-white text-sm border-b-1 border-white/40 transition-all duration-300" style="background:transparent;">
            <div class="flex items-center space-x-6">
                <div class="flex items-center space-x-2">
                    <x-heroicon-o-phone class="h-5 w-5" />
                    <span>0878 1200 0155</span>
                </div>
                <div class="flex items-center space-x-2">
                    <x-heroicon-o-envelope class="h-5 w-5" />
                    <span>forestaadventure@gmail.com</span>
                </div>
            </div>
            <div class="flex items-center space-x-4 mt-2 md:mt-0">
                <span class="hidden md:inline">Lebih dari <span class="text-green-400 font-bold">1000+</span> peralatan camping</span>
                <a href="#" class="bg-green-500 hover:bg-green-600 text-white font-semibold px-5 py-1.5 rounded-full flex items-center space-x-2 transition">
                    <span>Lihat</span>
                    <x-heroicon-o-arrow-right class="h-4 w-4" />
                </a>
            </div>
        </div>

        <!-- Header Bawah: Logo, Navigasi, Login/Cart -->
        <nav id="main-navbar" class="w-full bg-transparent px-6 lg:px-16 py-3 flex items-center justify-between text-white border-b-1 border-white/40 transition-all duration-300" style="background:transparent; position:static; top:auto; left:auto; transform:none;">
        <script>
        // Navbar and header show/hide & sticky blur effect on scroll
        document.addEventListener('DOMContentLoaded', function() {
            const navbar = document.getElementById('main-navbar');
            const topHeader = document.getElementById('top-header');
            function handleScroll() {
                if(window.scrollY > 80) {
                    // Make navbar fixed and blurred
                    navbar.style.position = 'fixed';
                    navbar.style.top = '0';
                    navbar.style.left = '0';
                    navbar.style.right = '0';
                    navbar.style.zIndex = '40';
                    navbar.style.background = 'rgba(30,30,30,0.25)'; // semi-transparent dark
                    navbar.classList.remove('bg-transparent');
                    navbar.classList.add('backdrop-blur-md');
                    navbar.classList.add('shadow-lg');
                    navbar.style.backdropFilter = 'blur(12px)';
                    navbar.style.transform = 'translateY(0)';
                    if(topHeader) topHeader.style.transform = 'translateY(-100%)';
                } else {
                    // Make navbar static and transparent
                    navbar.style.position = 'static';
                    navbar.style.background = 'transparent';
                    navbar.classList.add('bg-transparent');
                    navbar.classList.remove('backdrop-blur-md');
                    navbar.classList.remove('shadow-lg');
                    navbar.style.backdropFilter = '';
                    navbar.style.transform = 'none';
                    if(topHeader) topHeader.style.transform = 'translateY(0)';
                }
            }
            // Initial state: navbar static, both visible
            navbar.style.position = 'static';
            navbar.style.background = 'transparent';
            navbar.classList.add('bg-transparent');
            navbar.classList.remove('backdrop-blur-md');
            navbar.classList.remove('shadow-lg');
            navbar.style.backdropFilter = '';
            navbar.style.transform = 'none';
            if(topHeader) topHeader.style.transform = 'translateY(0)';
            window.addEventListener('scroll', handleScroll);
        });
        </script>
            <div class="flex items-center space-x-8">
                <a href="#" class="flex items-center space-x-2">
                    <img src="/gallery/GearUpLogo.png" alt="GearUp Logo" class="h-10 w-auto">
                </a>
                <a href="#kategori" class="font-semibold hover:text-green-400 transition flex items-center space-x-1">
                    <span>Kategori</span>
                    <x-heroicon-o-chevron-down class="h-4 w-4" />
                </a>
                <a href="#cara-sewa" class="font-semibold hover:text-green-400 transition">Cara Sewa</a>
                <a href="#kontak" class="font-semibold hover:text-green-400 transition">Kontak</a>
            </div>
            <div class="flex items-center space-x-6">
                @auth
                    <a href="{{ route('profile.show') }}" class="flex items-center space-x-2 hover:opacity-80 transition group">
                        <x-heroicon-o-user class="h-6 w-6" />
                        <span class="text-white text-sm hidden sm:block group-hover:text-green-200">{{ auth()->user()->name }}</span>
                    </a>
                    <form action="{{ route('logout') }}" method="POST" class="inline-block">
                        @csrf
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-full font-semibold transition flex items-center space-x-2">
                            <span>Keluar</span>
                            <x-heroicon-o-arrow-right-on-rectangle class="h-4 w-4" />
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-full font-semibold transition flex items-center space-x-2">
                        <x-heroicon-o-user class="h-5 w-5" />
                        <span>Masuk</span>
                    </a>
                @endauth
                <!-- Cart Icon (linked & dynamic count) -->
                @php
                    $cartCount = session('cart') ? count(session('cart')) : 0;
                @endphp
                <a href="/cart" class="relative bg-white rounded-xl p-2 flex items-center justify-center shadow hover:opacity-90 transition" aria-label="Lihat Keranjang">
                    <x-heroicon-o-shopping-cart class="h-8 w-8 text-gray-800" />
                    <span class="absolute -top-2 -right-2 bg-red-600 text-white text-xs font-bold rounded-full px-1.5">
                        {{ $cartCount }}
                    </span>
                </a>
            </div>
        </nav>


        <!-- Hero Content -->
        <div class="px-6 lg:px-16 mt-16 lg:mt-24 max-w-3xl pb-28 md:pb-36 pt-[48px]" style="text-shadow: 2px 2px 8px rgba(0,0,0,0.9);">
            <!-- Tagline -->
            <p class="text-green-500 text-base mb-3 font-medium">
                Siap Menjelajah. Tanpa Ribet.
            </p>

            <!-- Main Heading -->
            <h1 class="text-white text-4xl lg:text-5xl font-bold leading-tight mb-8">
                Sewa Gear Outdoor<br />
                Kini Lebih Mudah!
            </h1>

            <!-- Features -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-48 lg:mb-56">
                <div class="flex items-start space-x-3">
                    <div class="bg-green-600 rounded-full p-1 flex-shrink-0">
                        <x-heroicon-o-check class="h-4 w-4 text-black" />
                    </div>
                    <span class="text-white text-sm lg:text-base">Stok Real-Time & Lengkap</span>
                </div>
                <div class="flex items-start space-x-3">
                    <div class="bg-green-600 rounded-full p-1 flex-shrink-0">
                        <x-heroicon-o-check class="h-4 w-4 text-black" />
                    </div>
                    <span class="text-white text-sm lg:text-base">Praktis & Cepat</span>
                </div>
                <div class="flex items-start space-x-3">
                    <div class="bg-green-600 rounded-full p-1 flex-shrink-0">
                        <x-heroicon-o-check class="h-4 w-4 text-black" />
                    </div>
                    <span class="text-white text-sm lg:text-base">Layanan Khusus JADETABEK</span>
                </div>
            </div>
        </div>

        <!-- Search Bar (Floating at Bottom) -->
        <div class="absolute -bottom-12 md:-bottom-16 left-6 right-6 lg:left-16 lg:right-16 z-20">
            <div class="bg-white rounded-2xl shadow-2xl p-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    <!-- Tgl Ambil -->
                    <div>
                        <label class="block text-gray-600 text-xs mb-2 font-medium">Tgl Ambil</label>
                        <div class="flex items-center space-x-2 text-sm border-b-2 border-gray-200 pb-3">
                            <x-heroicon-o-calendar class="h-4 w-4 text-gray-400" />
                            <span class="text-gray-800 font-semibold">Rab, 21 Jan 2026</span>
                            <x-heroicon-o-chevron-down class="h-3 w-3 text-gray-400 ml-auto" />
                        </div>
                    </div>

                    <!-- Durasi -->
                    <div>
                        <label class="block text-gray-600 text-xs mb-2 font-medium">Durasi</label>
                        <div class="flex items-center space-x-2 text-sm border-b-2 border-gray-200 pb-3">
                            <span class="text-gray-400">Pilih durasi</span>
                            <x-heroicon-o-chevron-down class="h-3 w-3 text-gray-400 ml-auto" />
                        </div>
                    </div>

                    <!-- Maks. Pengembalian -->
                    <div>
                        <label class="block text-gray-600 text-xs mb-2 font-medium">Maks. Pengembalian</label>
                        <div class="flex items-center space-x-2 text-sm border-b-2 border-gray-200 pb-3">
                            <x-heroicon-o-calendar class="h-4 w-4 text-gray-400" />
                            <span class="text-gray-400">Tanggal kembali</span>
                        </div>
                    </div>

                    <!-- Cari -->
                    <div>
                        <label class="block text-gray-600 text-xs mb-2 font-medium">Cari</label>
                        <div class="flex items-center gap-1 border-b-2 border-gray-200 pb-3 h-9">
                            <input 
                                type="text" 
                                placeholder="Cari peralatan ..." 
                                class="flex-1 text-sm focus:outline-none text-gray-400 border-0 h-full"
                            />
                            <button class="bg-teal-700 hover:bg-teal-800 text-white px-4 py-4 rounded-lg font-semibold transition flex items-center space-x-1 flex-shrink-0 h-full">
                                <x-heroicon-o-magnifying-glass class="h-4 w-4" />
                                <span class="text-sm">Cari</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scroll to Top Floating Button (hidden by default, appears on scroll) -->
<button id="scrollToTopBtn" onclick="window.scrollTo({top: 0, behavior: 'smooth'})"
    class="fixed bottom-8 right-6 lg:right-12 w-12 h-12 bg-gray-800 hover:bg-gray-900 text-white rounded-full flex items-center justify-center shadow-lg z-50 opacity-0 pointer-events-none transition-opacity duration-500 p-0"
    style="display:block;">
    <span class="flex items-center justify-center w-full h-full">
        <x-heroicon-o-arrow-up class="h-6 w-6 m-0 p-0" />
    </span>
</button>

<script>
    // Show/hide scroll to top button with fade animation
    const scrollToTopBtn = document.getElementById('scrollToTopBtn');
    let scrollBtnVisible = false;
    window.addEventListener('scroll', function() {
        if(window.scrollY > 200) {
            if (!scrollBtnVisible) {
                scrollToTopBtn.classList.remove('opacity-0', 'pointer-events-none');
                scrollToTopBtn.classList.add('opacity-100');
                scrollBtnVisible = true;
            }
        } else {
            if (scrollBtnVisible) {
                scrollToTopBtn.classList.remove('opacity-100');
                scrollToTopBtn.classList.add('opacity-0', 'pointer-events-none');
                scrollBtnVisible = false;
            }
        }
    });
</script>

<!-- Pilihan Brand Section -->
<div class="py-16 px-6 lg:px-16 bg-gray-50 pt-48">
    <div class="max-w-7xl mx-auto">
        <!-- Section Header -->
        <div class="mb-12">
            <h2 class="text-4xl lg:text-5xl font-bold text-gray-900 mb-3">
                Pilihan Brand
            </h2>
            <p class="text-gray-600 text-lg" style="font-family: 'Inter', sans-serif;">
                Dipilih dari merek terbaik untuk pengalaman terbaik.
            </p>
        </div>

        <!-- Brands Carousel -->
        <div class="relative overflow-hidden">
            <div id="brandsCarousel" class="flex gap-6 transition-transform duration-500 ease-in-out px-4 py-6">
                <!-- REI -->
                <div class="flex-shrink-0 w-full sm:w-1/2 lg:w-1/4 xl:w-1/6 bg-white rounded-2xl shadow-sm hover:shadow-md transition p-8 flex items-center justify-center h-40">
                    <img src="/gallery/Rei.jpeg" alt="REI" class="w-24 h-auto object-contain">
                </div>

                <!-- CONSINA -->
                <div class="flex-shrink-0 w-full sm:w-1/2 lg:w-1/4 xl:w-1/6 bg-white rounded-2xl shadow-sm hover:shadow-md transition p-8 flex items-center justify-center h-40">
                    <img src="/gallery/Consina.jpg" alt="CONSINA" class="w-24 h-auto object-contain">
                </div>

                <!-- EIGER -->
                <div class="flex-shrink-0 w-full sm:w-1/2 lg:w-1/4 xl:w-1/6 bg-white rounded-2xl shadow-sm hover:shadow-md transition p-8 flex items-center justify-center h-40">
                    <img src="/gallery/Eiger.jpg" alt="EIGER" class="w-24 h-auto object-contain">
                </div>

                <!-- THE NORTH FACE -->
                <div class="flex-shrink-0 w-full sm:w-1/2 lg:w-1/4 xl:w-1/6 bg-white rounded-2xl shadow-sm hover:shadow-md transition p-8 flex items-center justify-center h-40">
                    <img src="/gallery/The north face.jpg" alt="THE NORTH FACE" class="w-24 h-auto object-contain">
                </div>

                <!-- OSPREY -->
                <div class="flex-shrink-0 w-full sm:w-1/2 lg:w-1/4 xl:w-1/6 bg-white rounded-2xl shadow-sm hover:shadow-md transition p-8 flex items-center justify-center h-40">
                    <img src="/gallery/Osprey.jpg" alt="OSPREY" class="w-24 h-auto object-contain">
                </div>

                <!-- QUECHUA -->
                <div class="flex-shrink-0 w-full sm:w-1/2 lg:w-1/4 xl:w-1/6 bg-white rounded-2xl shadow-sm hover:shadow-md transition p-8 flex items-center justify-center h-40">
                    <img src="/gallery/Quechua.jpg" alt="QUECHUA" class="w-24 h-auto object-contain">
                </div>

                <!-- ARCTERYX -->
                <div class="flex-shrink-0 w-full sm:w-1/2 lg:w-1/4 xl:w-1/6 bg-white rounded-2xl shadow-sm hover:shadow-md transition p-8 flex items-center justify-center h-40">
                    <img src="/gallery/Arcteryx.jpg" alt="ARCTERYX" class="w-24 h-auto object-contain">
                </div>

                <!-- MAMMUT -->
                <div class="flex-shrink-0 w-full sm:w-1/2 lg:w-1/4 xl:w-1/6 bg-white rounded-2xl shadow-sm hover:shadow-md transition p-8 flex items-center justify-center h-40">
                    <img src="/gallery/Mammut.jpg" alt="MAMMUT" class="w-24 h-auto object-contain">
                </div>

                <!-- MONTBELL -->
                <div class="flex-shrink-0 w-full sm:w-1/2 lg:w-1/4 xl:w-1/6 bg-white rounded-2xl shadow-sm hover:shadow-md transition p-8 flex items-center justify-center h-40">
                    <img src="/gallery/Montbell.jpg" alt="MONTBELL" class="w-24 h-auto object-contain">
                </div>

                <!-- PATAGONIA -->
                <div class="flex-shrink-0 w-full sm:w-1/2 lg:w-1/4 xl:w-1/6 bg-white rounded-2xl shadow-sm hover:shadow-md transition p-8 flex items-center justify-center h-40">
                    <img src="/gallery/Patagonia.jpg" alt="PATAGONIA" class="w-24 h-auto object-contain">
                </div>
            </div>
        </div>

        <script>
            const carousel = document.getElementById('brandsCarousel');
            let currentPosition = 0;
            let autoPlayInterval;

            const itemWidth = carousel.children[0].offsetWidth + 24; // item width + gap
            const totalItems = carousel.children.length;
            const visibleItems = window.innerWidth < 640 ? 1 : window.innerWidth < 1024 ? 2 : window.innerWidth < 1280 ? 4 : 6;

            function updateCarousel() {
                carousel.style.transform = `translateX(-${currentPosition * itemWidth}px)`;
            }

            function moveNext() {
                if (currentPosition < totalItems - visibleItems) {
                    currentPosition++;
                } else {
                    currentPosition = 0;
                }
                updateCarousel();
            }

            function startAutoPlay() {
                autoPlayInterval = setInterval(moveNext, 4000); // Auto move every 4 seconds
            }

            // Handle window resize
            window.addEventListener('resize', () => {
                currentPosition = 0;
                updateCarousel();
            });

            startAutoPlay();
        </script>



    </div>
</div>

<!-- Paket Satset Outdoor Section -->
<div class="py-16 px-6 lg:px-16 bg-white">
    <div class="max-w-7xl mx-auto">
        <!-- Section Header with See All Button -->
        <div class="mb-12 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="text-4xl lg:text-5xl font-bold text-gray-900 mb-3 md:mb-0">
                    Paket Satset Outdoor
                </h2>
                <p class="text-gray-600 text-lg" style="font-family: 'Inter', sans-serif;">
                    Pilihan paket hemat & praktis untuk petualanganmu!
                </p>
            </div>
            <a href="#" class="inline-flex items-center space-x-2 bg-green-500 hover:bg-green-600 text-white font-semibold px-5 py-3 rounded-full shadow-sm transition whitespace-nowrap">
                <span>Lihat Semua Paket</span>
                <x-heroicon-o-arrow-right class="h-5 w-5" />
            </a>
        </div>

        <!-- Packages Carousel with Navigation -->
        <div class="relative overflow-hidden">
            <button id="pkgPrevBtn" type="button" class="absolute left-0 top-1/2 -translate-y-1/2 z-20 bg-white border border-gray-300 shadow rounded-full w-10 h-10 flex items-center justify-center hover:bg-gray-100 transition disabled:opacity-40" aria-label="Sebelumnya" style="display: none;">
                <x-heroicon-o-chevron-left class="h-6 w-6 text-gray-700" />
            </button>
            <div id="packagesCarousel" class="flex gap-6 transition-transform duration-500 ease-in-out px-4 py-6">
                @foreach ($packages as $package)
                    <a href="{{ route('user.package.show', $package->id) }}" class="flex-shrink-0 w-full sm:w-1/2 lg:w-1/4 xl:w-1/6 bg-white rounded-2xl shadow-sm hover:shadow-lg transition p-4 flex flex-col cursor-pointer block relative group" style="text-decoration:none;">
                        <div class="aspect-[4/3] rounded-xl overflow-hidden bg-gray-100 mb-4">
                            @if($package->image)
                                <img src="{{ asset('storage/' . $package->image) }}" alt="{{ $package->name_package }}" class="w-full h-full object-cover" loading="lazy">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gray-200">
                                    <x-heroicon-o-photo class="h-16 w-16 text-gray-400" />
                                </div>
                            @endif
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2 text-center">{{ $package->name_package }}</h3>
                        <p class="text-sm text-gray-500 mb-2 text-center line-clamp-2">{{ $package->description }}</p>
                        <div class="mt-auto text-teal-700 font-bold text-lg text-center mb-2">Rp {{ number_format($package->price, 0, ',', '.') }}</div>
                        <span class="inline-flex items-center space-x-2 bg-green-500 group-hover:bg-green-600 text-white font-semibold px-4 py-2 rounded-full shadow-sm transition mx-auto mt-2">
                            <span>Lihat Detail</span>
                            <x-heroicon-o-arrow-right class="h-4 w-4" />
                        </span>
                    </a>
                @endforeach
            </div>
            <button id="pkgNextBtn" type="button" class="absolute right-0 top-1/2 -translate-y-1/2 z-20 bg-white border border-gray-300 shadow rounded-full w-10 h-10 flex items-center justify-center hover:bg-gray-100 transition disabled:opacity-40" aria-label="Berikutnya" style="display: none;">
                <x-heroicon-o-chevron-right class="h-6 w-6 text-gray-700" />
            </button>
        </div>

        <script>
            const packagesCarousel = document.getElementById('packagesCarousel');
            const pkgPrevBtn = document.getElementById('pkgPrevBtn');
            const pkgNextBtn = document.getElementById('pkgNextBtn');
            let pkgCurrentPosition = 0;
            let pkgAutoPlayInterval;

            function getPkgVisibleItems() {
                if(window.innerWidth < 640) return 1;
                if(window.innerWidth < 1024) return 2;
                if(window.innerWidth < 1280) return 4;
                return 6;
            }

            function updatePackagesCarousel() {
                const itemWidth = packagesCarousel.children[0]?.offsetWidth + 24 || 0;
                const visibleItems = getPkgVisibleItems();
                packagesCarousel.style.transform = `translateX(-${pkgCurrentPosition * itemWidth}px)`;
                // Show/hide nav
                if (pkgPrevBtn && pkgNextBtn) {
                    pkgPrevBtn.style.display = (packagesCarousel.children.length > visibleItems) ? 'flex' : 'none';
                    pkgNextBtn.style.display = (packagesCarousel.children.length > visibleItems) ? 'flex' : 'none';
                    pkgPrevBtn.disabled = pkgCurrentPosition === 0;
                    pkgNextBtn.disabled = pkgCurrentPosition >= packagesCarousel.children.length - visibleItems;
                }
            }

            function movePkgNext() {
                const totalItems = packagesCarousel.children.length;
                const visibleItems = getPkgVisibleItems();
                if (pkgCurrentPosition < totalItems - visibleItems) {
                    pkgCurrentPosition++;
                } else {
                    pkgCurrentPosition = 0;
                }
                updatePackagesCarousel();
            }

            function movePkgPrev() {
                const visibleItems = getPkgVisibleItems();
                if (pkgCurrentPosition > 0) {
                    pkgCurrentPosition--;
                } else {
                    pkgCurrentPosition = packagesCarousel.children.length - visibleItems;
                    if(pkgCurrentPosition < 0) pkgCurrentPosition = 0;
                }
                updatePackagesCarousel();
            }

            function startPkgAutoPlay() {
                pkgAutoPlayInterval = setInterval(movePkgNext, 4000);
            }

            if(pkgPrevBtn) pkgPrevBtn.addEventListener('click', movePkgPrev);
            if(pkgNextBtn) pkgNextBtn.addEventListener('click', movePkgNext);

            window.addEventListener('resize', () => {
                pkgCurrentPosition = 0;
                updatePackagesCarousel();
            });

            startPkgAutoPlay();
            updatePackagesCarousel();
        </script>
    </div>
</div>

<!-- Pilihan Terbaik Minggu Ini -->
<div class="py-16 px-6 lg:px-16 bg-white">
    <div class="max-w-7xl mx-auto">
        <div class="flex items-start justify-between flex-wrap gap-6 mb-10">
            <div>
                <h2 class="text-4xl lg:text-5xl font-bold text-gray-900 mb-3">Pilihan Terbaik Minggu Ini!</h2>
                <p class="text-gray-600 text-lg" style="font-family: 'Inter', sans-serif;">Gear pilihan pendaki, siap temani petualanganmu.</p>
            </div>
            <a href="#" class="inline-flex items-center space-x-2 bg-green-500 hover:bg-green-600 text-white font-semibold px-5 py-3 rounded-full shadow-sm transition">
                <span>Lihat Lainnya</span>
                <x-heroicon-o-arrow-right class="h-5 w-5" />
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach ($bestPicks as $product)
                <a href="{{ auth()->check() ? route('user.product.show', $product->id) : route('login') }}" class="bg-white rounded-2xl shadow-sm hover:shadow-lg transition p-4 flex flex-col cursor-pointer block">
                    <div class="aspect-[4/3] rounded-xl overflow-hidden bg-gray-100 mb-4">
                        @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover" loading="lazy">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-gray-200">
                                <x-heroicon-o-photo class="h-16 w-16 text-gray-400" />
                            </div>
                        @endif
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $product->name }}</h3>
                    @if($product->category)
                        <p class="text-sm text-gray-500 mb-2">{{ $product->category->categories }}</p>
                    @endif
                    <div class="mt-auto text-teal-700 font-bold text-lg">Rp {{ number_format($product->price_per_day, 0, ',', '.') }}/hari</div>
                </a>
            @endforeach
        </div>
    </div>
</div>

<!-- Footer -->
<footer class="bg-black text-white py-16 px-6 lg:px-16 relative">
    <div class="max-w-7xl mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-16">
            <!-- Logo and Company Info -->
            <div>
                <img src="/gallery/Gearup.png" alt="GearUp Logo" class="h-40 w-auto mb-6">
                
                <div class="space-y-4 text-gray-400 text-sm">
                    <div class="flex items-start space-x-2">
                        <x-heroicon-o-map-pin class="h-5 w-5 flex-shrink-0 mt-0.5" />
                        <p>Jl. H. Jian No.38, RT.9/RW.3, Cipete Utara,<br>Kec. Kby. Baru, Kota Jakarta Selatan,<br>Daerah Khusus Ibukota Jakarta 12150</p>
                    </div>
                    
                    <div class="flex items-center space-x-2">
                        <x-heroicon-o-clock class="h-5 w-5 flex-shrink-0" />
                        <p>Setiap hari, 09:00 - 20:00 WIB</p>
                    </div>
                    
                    <div class="flex items-center space-x-2">
                        <x-heroicon-o-envelope class="h-5 w-5 flex-shrink-0" />
                        <p>gearup@gmail.com</p>
                    </div>
                </div>
            </div>

            <!-- Informasi -->
            <div>
                <h3 class="text-xl font-semibold mb-6">Informasi</h3>
                <ul class="space-y-3 text-gray-400">
                    <li><a href="#" class="hover:text-white transition">Cara sewa</a></li>
                    <li><a href="#" class="hover:text-white transition">Cara jadi member</a></li>
                    <li><a href="#" class="hover:text-white transition">Cara pengembalian</a></li>
                    <li><a href="#" class="hover:text-white transition">Syarat dan Ketentuan</a></li>
                </ul>
            </div>

            <!-- Tentang Foresta -->
            <div>
                <h3 class="text-xl font-semibold mb-6">Tentang GearUp</h3>
                <ul class="space-y-3 text-gray-400">
                    <li><a href="#" class="hover:text-white transition">Tentang Kami</a></li>
                </ul>
            </div>

            <!-- Layanan Bantuan -->
            <div>
                <h3 class="text-xl font-semibold mb-6">Layanan Bantuan</h3>
                <div class="text-gray-400 mb-4">
                    <p>Kontak Kami</p>
                </div>
                <div class="flex items-center space-x-2 mb-2">
                    <x-heroicon-o-phone class="h-5 w-5 text-gray-400" />
                    <span class="text-sm text-gray-400">WhatsApp Kami</span>
                </div>
                <a href="https://wa.me/6287812000155" class="text-green-500 text-2xl font-bold hover:text-green-400 transition">0878 1200 0155</a>
            </div>
        </div>



        <!-- Bottom Bar -->
        <div class="border-t border-gray-800 pt-8">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                <p class="text-gray-400 text-sm">© 2026 GearUp. All rights reserved</p>
                
                <div class="flex items-center space-x-4">
                    <span class="text-gray-400 text-sm">Follow us</span>
                    <a href="#" class="text-gray-400 hover:text-white transition">
                        <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white transition">
                        <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white transition">
                        <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</footer>

</div>