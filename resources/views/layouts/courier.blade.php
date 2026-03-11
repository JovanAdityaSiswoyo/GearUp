<!-- Courier Layout with Sidebar Navigation -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Courier Dashboard')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
        .sub-header {
            font-family: 'Inter', sans-serif;
        }
    </style>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gray-50">
    <div class="min-h-screen">
        <!-- Sidebar -->
        <aside class="fixed top-0 left-0 h-screen w-64 bg-gradient-to-b from-green-600 to-emerald-500 text-white overflow-y-auto z-40">
            <div class="p-6">
                <h1 class="text-2xl font-bold">Courier Panel</h1>
                <p class="text-sm opacity-80 sub-header">AplikasiPinjam</p>
            </div>
            
            <nav class="mt-6">
                <a href="{{ route('courier.dashboard') }}" class="flex items-center px-6 py-3 hover:bg-white/10 transition {{ request()->routeIs('courier.dashboard') ? 'bg-white/20 border-r-4 border-white' : '' }}">
                    <x-heroicon-o-home class="h-5 w-5 mr-3" />
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('courier.deliveries.index') }}" class="flex items-center px-6 py-3 hover:bg-white/10 transition {{ request()->routeIs('courier.deliveries.index') || request()->routeIs('courier.deliveries.show') ? 'bg-white/20 border-r-4 border-white' : '' }}">
                    <x-heroicon-o-truck class="h-5 w-5 mr-3" />
                    <span>Pengiriman</span>
                    @if(isset($readyForPickupCount) && $readyForPickupCount > 0)
                        <span class="ml-auto bg-red-500 text-white text-xs font-bold rounded-full px-2 py-0.5">{{ $readyForPickupCount }}</span>
                    @endif
                </a>
                <a href="{{ route('courier.returns.index') }}" class="flex items-center px-6 py-3 hover:bg-white/10 transition {{ request()->routeIs('courier.returns.*') ? 'bg-white/20 border-r-4 border-white' : '' }}">
                    <x-heroicon-o-arrow-uturn-left class="h-5 w-5 mr-3" />
                    <span>Pengembalian</span>
                </a>
                <a href="{{ route('courier.route.map') }}" class="flex items-center px-6 py-3 hover:bg-white/10 transition {{ request()->routeIs('courier.route.map') ? 'bg-white/20 border-r-4 border-white' : '' }}">
                    <x-heroicon-o-map class="h-5 w-5 mr-3" />
                    <span>Peta Rute</span>
                </a>
                <a href="{{ route('courier.deliveries.history') }}" class="flex items-center px-6 py-3 hover:bg-white/10 transition {{ request()->routeIs('courier.deliveries.history') ? 'bg-white/20 border-r-4 border-white' : '' }}">
                    <x-heroicon-o-clock class="h-5 w-5 mr-3" />
                    <span>History</span>
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="ml-64">
            <!-- Header -->
            <header class="bg-white shadow-sm sticky top-0 z-30">
                <div class="flex items-center justify-between px-8 py-4">
                    <div>
                        <h2 class="text-2xl font-semibold text-gray-800">@yield('title', 'Dashboard')</h2>
                        <p class="text-sm text-gray-500 sub-header">Welcome back, {{ auth()->user()->courier->nama ?? auth()->user()->name }}!</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <button class="relative p-2 text-gray-600 hover:bg-gray-100 rounded-lg transition">
                            <x-heroicon-o-bell class="h-6 w-6" />
                            <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                        </button>
                        <div class="flex items-center space-x-3">
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-800">{{ auth()->user()->courier->nama ?? auth()->user()->name }}</p>
                                <p class="text-xs text-gray-500 sub-header">Courier</p>
                            </div>
                            <div class="w-10 h-10 bg-gradient-to-br from-green-600 to-emerald-500 rounded-full flex items-center justify-center text-white font-semibold">
                                {{ substr(auth()->user()->courier->nama ?? auth()->user()->name, 0, 1) }}
                            </div>
                            <form action="{{ route('logout') }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-gray-600 hover:text-red-600 transition">
                                    <x-heroicon-o-arrow-right-on-rectangle class="h-6 w-6" />
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="p-8">
                @yield('content')
            </main>
        </div>
    </div>

    @yield('scripts')
</body>
</html>
