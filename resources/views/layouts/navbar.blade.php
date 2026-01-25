<!-- Header Atas: Kontak & Info -->
<div id="top-header" class="w-full bg-transparent px-6 lg:px-16 py-2 flex flex-col md:flex-row items-center justify-between text-black text-sm border-b-1 border-gray-300 transition-all duration-300 bg-white" style="background:transparent;">
    <div class="flex items-center space-x-6">
        <div class="flex items-center space-x-2">
            <x-heroicon-o-phone class="h-5 w-5" />
            <span>0878 0987 0155</span>
        </div>
        <div class="flex items-center space-x-2">
            <x-heroicon-o-envelope class="h-5 w-5" />
            <span>gearup@gmail.com</span>
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
<nav id="main-navbar" class="w-full bg-transparent px-6 lg:px-16 py-3 flex items-center justify-between text-black border-b-1 border-gray-300 transition-all duration-300" style="background:transparent; position:static; top:auto; left:auto; transform:none;">
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
        <a href="/" class="flex items-center space-x-2">
            <img src="/gallery/GearUpLogo.png" alt="GearUp Logo" class="h-10 w-auto">
        </a>
        <a href="#kategori" class="font-semibold hover:text-green-400 transition flex items-center space-x-1 text-black">
            <span class="text-black">Kategori</span>
            <x-heroicon-o-chevron-down class="h-4 w-4 text-black" />
        </a>
        <a href="#cara-sewa" class="font-semibold hover:text-green-400 transition text-black">Cara Sewa</a>
        <a href="#kontak" class="font-semibold hover:text-green-400 transition text-black">Kontak</a>
    </div>
    <div class="flex items-center space-x-6">
        @auth
            <a href="{{ route('profile.show') }}" class="flex items-center space-x-2 hover:opacity-80 transition group">
                <x-heroicon-o-user class="h-6 w-6 text-black" />
                <span class="text-black text-sm hidden sm:block group-hover:text-green-600">{{ auth()->user()->name }}</span>
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
                <x-heroicon-o-user class="h-5 w-5 text-black" />
                <span class="text-black">Masuk</span>
            </a>
        @endauth
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
