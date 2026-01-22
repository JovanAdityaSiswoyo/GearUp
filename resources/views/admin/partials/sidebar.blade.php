<aside class="w-64 bg-gradient-to-b from-purple-600 to-pink-500 text-white fixed left-0 top-0 bottom-0 overflow-y-auto">
    <div class="p-6">
        <h1 class="text-2xl font-bold">Admin Panel</h1>
        <p class="text-sm opacity-80">GearUp</p>
    </div>
    
    <nav class="mt-6">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center px-6 py-3 {{ request()->routeIs('admin.dashboard') ? 'bg-white/20 border-r-4 border-white' : 'hover:bg-white/10' }} transition">
            <x-heroicon-o-home class="h-5 w-5 mr-3" />
            <span>Dashboard</span>
        </a>
        <a href="{{ route('admin.users.index') }}" class="flex items-center px-6 py-3 {{ request()->routeIs('admin.users.*') ? 'bg-white/20 border-r-4 border-white' : 'hover:bg-white/10' }} transition">
            <x-heroicon-o-users class="h-5 w-5 mr-3" />
            <span>Users</span>
        </a>
        <a href="{{ route('admin.categories.index') }}" class="flex items-center px-6 py-3 {{ request()->routeIs('admin.categories.*') ? 'bg-white/20 border-r-4 border-white' : 'hover:bg-white/10' }} transition">
            <x-heroicon-o-tag class="h-5 w-5 mr-3" />
            <span>Categories</span>
        </a>
        <a href="{{ route('admin.brands.index') }}" class="flex items-center px-6 py-3 {{ request()->routeIs('admin.brands.*') ? 'bg-white/20 border-r-4 border-white' : 'hover:bg-white/10' }} transition">
            <x-heroicon-o-star class="h-5 w-5 mr-3" />
            <span>Brands</span>
        </a>
        <a href="{{ route('admin.products.index') }}" class="flex items-center px-6 py-3 {{ request()->routeIs('admin.products.*') ? 'bg-white/20 border-r-4 border-white' : 'hover:bg-white/10' }} transition">
            <x-heroicon-o-cube class="h-5 w-5 mr-3" />
            <span>Products</span>
        </a>
        <a href="{{ route('admin.packages.index') }}" class="flex items-center px-6 py-3 {{ request()->routeIs('admin.packages.*') ? 'bg-white/20 border-r-4 border-white' : 'hover:bg-white/10' }} transition">
            <x-heroicon-o-cube-transparent class="h-5 w-5 mr-3" />
            <span>Packages</span>
        </a>
        <a href="{{ route('admin.bookings.index') }}" class="flex items-center px-6 py-3 {{ request()->routeIs('admin.bookings.*') ? 'bg-white/20 border-r-4 border-white' : 'hover:bg-white/10' }} transition">
            <x-heroicon-o-clipboard-document-list class="h-5 w-5 mr-3" />
            <span>Peminjaman</span>
        </a>
        <a href="{{ route('admin.returns.index') }}" class="flex items-center px-6 py-3 {{ request()->routeIs('admin.returns.*') ? 'bg-white/20 border-r-4 border-white' : 'hover:bg-white/10' }} transition">
            <x-heroicon-o-arrow-uturn-left class="h-5 w-5 mr-3" />
            <span>Pengembalian</span>
        </a>
        <a href="{{ route('admin.payments.index') }}" class="flex items-center px-6 py-3 {{ request()->routeIs('admin.payments.*') ? 'bg-white/20 border-r-4 border-white' : 'hover:bg-white/10' }} transition">
            <x-heroicon-o-credit-card class="h-5 w-5 mr-3" />
            <span>Payments</span>
        </a>
        <a href="{{ route('admin.reconciliation.index') }}" class="flex items-center px-6 py-3 {{ request()->routeIs('admin.reconciliation.*') ? 'bg-white/20 border-r-4 border-white' : 'hover:bg-white/10' }} transition">
            <x-heroicon-o-calculator class="h-5 w-5 mr-3" />
            <span>Rekonsiliasi</span>
        </a>
        <a href="{{ route('admin.activity-log.index') }}" class="flex items-center px-6 py-3 {{ request()->routeIs('admin.activity-log.*') ? 'bg-white/20 border-r-4 border-white' : 'hover:bg-white/10' }} transition">
            <x-heroicon-o-clock class="h-5 w-5 mr-3" />
            <span>Log Aktivitas</span>
        </a>
    </nav>
</aside>
