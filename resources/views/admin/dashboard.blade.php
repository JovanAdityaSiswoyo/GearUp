<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - AplikasiPinjam</title>
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
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-gradient-to-b from-purple-600 to-pink-500 text-white">
            <div class="p-6">
                <h1 class="text-2xl font-bold">Admin Panel</h1>
                <p class="text-sm opacity-80 sub-header">AplikasiPinjam</p>
            </div>
            
            <nav class="mt-6">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center px-6 py-3 bg-white/20 border-r-4 border-white">
                    <x-heroicon-o-home class="h-5 w-5 mr-3" />
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('admin.users.index') }}" class="flex items-center px-6 py-3 hover:bg-white/10 transition">
                    <x-heroicon-o-users class="h-5 w-5 mr-3" />
                    <span>Users</span>
                </a>
                <a href="{{ route('admin.books.index') }}" class="flex items-center px-6 py-3 hover:bg-white/10 transition">
                    <x-heroicon-o-book-open class="h-5 w-5 mr-3" />
                    <span>Books</span>
                </a>
                <a href="{{ route('admin.products.index') }}" class="flex items-center px-6 py-3 hover:bg-white/10 transition">
                    <x-heroicon-o-cube class="h-5 w-5 mr-3" />
                    <span>Products</span>
                </a>
                <a href="{{ route('admin.categories.index') }}" class="flex items-center px-6 py-3 hover:bg-white/10 transition">
                    <x-heroicon-o-tag class="h-5 w-5 mr-3" />
                    <span>Categories</span>
                </a>
                <a href="{{ route('admin.packages.index') }}" class="flex items-center px-6 py-3 hover:bg-white/10 transition">
                    <x-heroicon-o-cube-transparent class="h-5 w-5 mr-3" />
                    <span>Packages</span>
                </a>
                <a href="{{ route('admin.payments.index') }}" class="flex items-center px-6 py-3 hover:bg-white/10 transition">
                    <x-heroicon-o-credit-card class="h-5 w-5 mr-3" />
                    <span>Payments</span>
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="flex-1">
            <!-- Header -->
            <header class="bg-white shadow-sm">
                <div class="flex items-center justify-between px-8 py-4">
                    <div>
                        <h2 class="text-2xl font-semibold text-gray-800">Dashboard</h2>
                        <p class="text-sm text-gray-500 sub-header">Welcome back, {{ auth()->guard('admin')->user()->name ?? auth()->user()->name }}!</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <button class="relative p-2 text-gray-600 hover:bg-gray-100 rounded-lg transition">
                            <x-heroicon-o-bell class="h-6 w-6" />
                            <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                        </button>
                        <div class="flex items-center space-x-3">
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-800">{{ auth()->guard('admin')->user()->name ?? auth()->user()->name }}</p>
                                <p class="text-xs text-gray-500 sub-header">
                                    {{ auth()->guard('admin')->check() ? 'Administrator' : (auth()->user()->roles->first()->name ?? 'User') }}
                                </p>
                            </div>
                            <div class="w-10 h-10 bg-gradient-to-br from-purple-600 to-pink-500 rounded-full flex items-center justify-center text-white font-semibold">
                                {{ substr(auth()->guard('admin')->user()->name ?? auth()->user()->name, 0, 1) }}
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

            <!-- Dashboard Content -->
            <main class="p-8">
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500 sub-header">Total Users</p>
                                <h3 class="text-3xl font-bold text-gray-800 mt-1">{{ \App\Models\User::count() }}</h3>
                            </div>
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                <x-heroicon-o-users class="h-6 w-6 text-blue-600" />
                            </div>
                        </div>
                        <div class="mt-4">
                            <span class="text-sm text-green-600 font-medium">↑ 12%</span>
                            <span class="text-sm text-gray-500 sub-header ml-1">from last month</span>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500 sub-header">Total Books</p>
                                <h3 class="text-3xl font-bold text-gray-800 mt-1">{{ \App\Models\Book::count() }}</h3>
                            </div>
                            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                                <x-heroicon-o-book-open class="h-6 w-6 text-purple-600" />
                            </div>
                        </div>
                        <div class="mt-4">
                            <span class="text-sm text-green-600 font-medium">↑ 8%</span>
                            <span class="text-sm text-gray-500 sub-header ml-1">from last month</span>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500 sub-header">Total Products</p>
                                <h3 class="text-3xl font-bold text-gray-800 mt-1">{{ \App\Models\Product::count() }}</h3>
                            </div>
                            <div class="w-12 h-12 bg-pink-100 rounded-lg flex items-center justify-center">
                                <x-heroicon-o-cube class="h-6 w-6 text-pink-600" />
                            </div>
                        </div>
                        <div class="mt-4">
                            <span class="text-sm text-green-600 font-medium">↑ 15%</span>
                            <span class="text-sm text-gray-500 sub-header ml-1">from last month</span>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500 sub-header">Total Revenue</p>
                                <h3 class="text-3xl font-bold text-gray-800 mt-1">Rp {{ number_format(\App\Models\Payment::where('status', 'completed')->sum('amount'), 0, ',', '.') }}</h3>
                            </div>
                            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                <x-heroicon-o-currency-dollar class="h-6 w-6 text-green-600" />
                            </div>
                        </div>
                        <div class="mt-4">
                            <span class="text-sm text-green-600 font-medium">↑ 23%</span>
                            <span class="text-sm text-gray-500 sub-header ml-1">from last month</span>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity & Quick Actions -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Recent Users -->
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Recent Users</h3>
                        <div class="space-y-4">
                            @foreach(\App\Models\User::latest()->take(5)->get() as $user)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-gradient-to-br from-purple-400 to-pink-400 rounded-full flex items-center justify-center text-white font-semibold">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-800">{{ $user->name }}</p>
                                        <p class="text-xs text-gray-500 sub-header">{{ $user->email }}</p>
                                    </div>
                                </div>
                                <span class="text-xs text-gray-500 sub-header">{{ $user->created_at->diffForHumans() }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <a href="{{ route('admin.users.create') }}" class="flex flex-col items-center justify-center p-6 border-2 border-dashed border-gray-300 rounded-lg hover:border-purple-500 hover:bg-purple-50 transition">
                                <x-heroicon-o-user-plus class="h-8 w-8 text-purple-600 mb-2" />
                                <span class="text-sm font-medium text-gray-700">Add User</span>
                            </a>
                            <a href="{{ route('admin.books.create') }}" class="flex flex-col items-center justify-center p-6 border-2 border-dashed border-gray-300 rounded-lg hover:border-purple-500 hover:bg-purple-50 transition">
                                <x-heroicon-o-plus-circle class="h-8 w-8 text-purple-600 mb-2" />
                                <span class="text-sm font-medium text-gray-700">Add Book</span>
                            </a>
                            <a href="{{ route('admin.products.create') }}" class="flex flex-col items-center justify-center p-6 border-2 border-dashed border-gray-300 rounded-lg hover:border-purple-500 hover:bg-purple-50 transition">
                                <x-heroicon-o-cube class="h-8 w-8 text-purple-600 mb-2" />
                                <span class="text-sm font-medium text-gray-700">Add Product</span>
                            </a>
                            <a href="{{ route('admin.payments.index') }}" class="flex flex-col items-center justify-center p-6 border-2 border-dashed border-gray-300 rounded-lg hover:border-purple-500 hover:bg-purple-50 transition">
                                <x-heroicon-o-credit-card class="h-8 w-8 text-purple-600 mb-2" />
                                <span class="text-sm font-medium text-gray-700">View Payments</span>
                            </a>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
