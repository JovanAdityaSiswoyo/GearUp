<!-- Officer Layout with Sidebar Navigation -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Officer Dashboard')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
        .sub-header {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen">
        <!-- Sidebar -->
        <aside class="fixed top-0 left-0 h-screen w-64 bg-gray-500 text-white overflow-y-auto z-40">
            <div class="p-6">
                <h1 class="text-2xl font-bold">Officer Panel</h1>
                <p class="text-sm opacity-80 sub-header">AplikasiPinjam</p>
            </div>
            
            <nav class="mt-6">
                <a href="{{ route('officer.dashboard') }}" class="flex items-center px-6 py-3 hover:bg-white/10 transition {{ request()->routeIs('officer.dashboard') ? 'bg-white/20 border-r-4 border-white' : '' }}">
                    <x-heroicon-o-home class="h-5 w-5 mr-3" />
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('officer.bookings.index') }}" class="flex items-center px-6 py-3 hover:bg-white/10 transition {{ request()->routeIs('officer.bookings.*') ? 'bg-white/20 border-r-4 border-white' : '' }}">
                    <x-heroicon-o-clipboard-document-list class="h-5 w-5 mr-3" />
                    <span>Booking Management</span>
                </a>
                <a href="{{ route('officer.returns.monitor') }}" class="flex items-center px-6 py-3 hover:bg-white/10 transition {{ request()->routeIs('officer.returns.*') ? 'bg-white/20 border-r-4 border-white' : '' }}">
                    <x-heroicon-o-arrow-path class="h-5 w-5 mr-3" />
                    <span>Monitor Returns</span>
                </a>
                <a href="{{ route('officer.reports.print') }}" class="flex items-center px-6 py-3 hover:bg-white/10 transition {{ request()->routeIs('officer.reports.*') ? 'bg-white/20 border-r-4 border-white' : '' }}">
                    <x-heroicon-o-printer class="h-5 w-5 mr-3" />
                    <span>Print Report</span>
                </a>
                <a href="{{ route('officer.payments.index') }}" class="flex items-center px-6 py-3 hover:bg-white/10 transition {{ request()->routeIs('officer.payments.*') ? 'bg-white/20 border-r-4 border-white' : '' }}">
                    <x-heroicon-o-banknotes class="h-5 w-5 mr-3" />
                    <span>Payments</span>
                </a>
                <a href="{{ route('officer.penalties.index') }}" class="flex items-center px-6 py-3 hover:bg-white/10 transition {{ request()->routeIs('officer.penalties.*') ? 'bg-white/20 border-r-4 border-white' : '' }}">
                    <x-heroicon-o-exclamation-triangle class="h-5 w-5 mr-3" />
                    <span>Denda User</span>
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
                        <p class="text-sm text-gray-500 sub-header">Welcome back, {{ auth()->user()->name }}!</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center space-x-3">
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-800">{{ auth()->user()->name }}</p>
                                <p class="text-xs text-gray-500 sub-header">Officer</p>
                            </div>
                            <div class="w-10 h-10 bg-linear-to-br from-blue-600 to-cyan-500 rounded-full flex items-center justify-center text-white font-semibold">
                                {{ substr(auth()->user()->name, 0, 1) }}
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

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Display session messages as SweetAlert
        @if(session('success'))
            Swal.fire({
                title: 'Success!',
                text: '{{ session('success') }}',
                icon: 'success',
                confirmButtonColor: '#3b82f6',
                timer: 3000,
                timerProgressBar: true
            });
        @endif

        @if(session('error'))
            Swal.fire({
                title: 'Error!',
                text: '{{ session('error') }}',
                icon: 'error',
                confirmButtonColor: '#3b82f6'
            });
        @endif

        @if($errors->any())
            Swal.fire({
                title: 'Validation Error!',
                text: '{{ $errors->first() }}',
                icon: 'warning',
                confirmButtonColor: '#3b82f6'
            });
        @endif
    </script>

    @yield('scripts')
</body>
</html>
