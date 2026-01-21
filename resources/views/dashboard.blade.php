<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - AplikasiPinjam</title>
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
        <!-- Header -->
        <header class="bg-white shadow-sm">
            <div class="flex items-center justify-between px-8 py-4">
                <div>
                    <h1 class="text-2xl font-bold bg-gradient-to-r from-purple-600 to-pink-500 bg-clip-text text-transparent">
                        AplikasiPinjam
                    </h1>
                    <p class="text-sm text-gray-500 sub-header">Book & Product Loan Management</p>
                </div>
                <div class="flex items-center space-x-4">
                    <button class="relative p-2 text-gray-600 hover:bg-gray-100 rounded-lg transition">
                        <x-heroicon-o-bell class="h-6 w-6" />
                        <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                    </button>
                    <div class="flex items-center space-x-3">
                        <div class="text-right">
                            <p class="text-sm font-medium text-gray-800">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-gray-500 sub-header">Member</p>
                        </div>
                        <div class="w-10 h-10 bg-gradient-to-br from-purple-600 to-pink-500 rounded-full flex items-center justify-center text-white font-semibold">
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

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Welcome Banner -->
            <div class="bg-gradient-to-r from-purple-600 to-pink-500 rounded-2xl shadow-lg p-8 mb-8 text-white">
                <h2 class="text-3xl font-bold mb-2">Welcome back, {{ auth()->user()->name }}! 👋</h2>
                <p class="text-lg opacity-90 sub-header">Discover and borrow books and products today.</p>
            </div>

            <!-- Quick Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 sub-header">Active Loans</p>
                            <h3 class="text-3xl font-bold text-gray-800 mt-1">{{ \App\Models\Book::where('id_user', auth()->id())->where('status', 'borrowed')->count() }}</h3>
                        </div>
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                            <x-heroicon-o-book-open class="h-6 w-6 text-purple-600" />
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 sub-header">Available Products</p>
                            <h3 class="text-3xl font-bold text-gray-800 mt-1">{{ \App\Models\Product::where('status', 'available')->count() }}</h3>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <x-heroicon-o-cube class="h-6 w-6 text-blue-600" />
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 sub-header">Pending Returns</p>
                            <h3 class="text-3xl font-bold text-gray-800 mt-1">{{ \App\Models\Book::where('id_user', auth()->id())->where('status', 'borrowed')->where('checkout_appointment_end', '<', now())->count() }}</h3>
                        </div>
                        <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                            <x-heroicon-o-clock class="h-6 w-6 text-orange-600" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Categories -->
            <div class="mb-8">
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Browse Categories</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @foreach(\App\Models\Category::take(8)->get() as $category)
                    <div class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition cursor-pointer">
                        <div class="w-12 h-12 bg-gradient-to-br from-purple-400 to-pink-400 rounded-lg flex items-center justify-center mb-3">
                            <x-heroicon-o-tag class="h-6 w-6 text-white" />
                        </div>
                        <h4 class="font-semibold text-gray-800">{{ $category->nama }}</h4>
                        <p class="text-sm text-gray-500 sub-header mt-1">Browse items</p>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Available Products -->
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-semibold text-gray-800">Available Now</h3>
                    <a href="#" class="text-purple-600 hover:text-purple-700 font-medium text-sm">View All →</a>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @foreach(\App\Models\Product::where('status', 'available')->take(8)->get() as $product)
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-md transition">
                        <div class="h-48 bg-gradient-to-br from-purple-200 to-pink-200 flex items-center justify-center">
                            <x-heroicon-o-cube class="h-16 w-16 text-purple-600" />
                        </div>
                        <div class="p-4">
                            <h4 class="font-semibold text-gray-800 mb-1">{{ $product->name }}</h4>
                            <p class="text-sm text-gray-500 sub-header mb-3">{{ Str::limit($product->desc, 50) }}</p>
                            <button class="w-full bg-gradient-to-r from-purple-600 to-pink-500 text-white py-2 rounded-lg hover:from-purple-700 hover:to-pink-600 transition text-sm font-medium">
                                Borrow Now
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </main>
    </div>
</body>
</html>
