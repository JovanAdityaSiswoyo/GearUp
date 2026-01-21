<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Officer Dashboard - AplikasiPinjam</title>
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
        <aside class="w-64 bg-gradient-to-b from-blue-600 to-cyan-500 text-white">
            <div class="p-6">
                <h1 class="text-2xl font-bold">Officer Panel</h1>
                <p class="text-sm opacity-80 sub-header">AplikasiPinjam</p>
            </div>
            
            <nav class="mt-6">
                <a href="{{ route('officer.dashboard') }}" class="flex items-center px-6 py-3 bg-white/20 border-r-4 border-white">
                    <x-heroicon-o-home class="h-5 w-5 mr-3" />
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('officer.books.index') }}" class="flex items-center px-6 py-3 hover:bg-white/10 transition">
                    <x-heroicon-o-book-open class="h-5 w-5 mr-3" />
                    <span>Book Loans</span>
                </a>
                <a href="{{ route('officer.products.index') }}" class="flex items-center px-6 py-3 hover:bg-white/10 transition">
                    <x-heroicon-o-cube class="h-5 w-5 mr-3" />
                    <span>Product Loans</span>
                </a>
                <a href="{{ route('officer.payments.index') }}" class="flex items-center px-6 py-3 hover:bg-white/10 transition">
                    <x-heroicon-o-credit-card class="h-5 w-5 mr-3" />
                    <span>Payments</span>
                </a>
                <a href="{{ route('officer.returns.index') }}" class="flex items-center px-6 py-3 hover:bg-white/10 transition">
                    <x-heroicon-o-arrow-path class="h-5 w-5 mr-3" />
                    <span>Returns</span>
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
                        <p class="text-sm text-gray-500 sub-header">Welcome back, {{ auth()->guard('officer')->user()->name ?? auth()->user()->name }}!</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <button class="relative p-2 text-gray-600 hover:bg-gray-100 rounded-lg transition">
                            <x-heroicon-o-bell class="h-6 w-6" />
                            <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                        </button>
                        <div class="flex items-center space-x-3">
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-800">{{ auth()->guard('officer')->user()->name ?? auth()->user()->name }}</p>
                                <p class="text-xs text-gray-500 sub-header">
                                    {{ auth()->guard('officer')->check() ? 'Officer' : (auth()->user()->roles->first()->name ?? 'User') }}
                                </p>
                            </div>
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-cyan-500 rounded-full flex items-center justify-center text-white font-semibold">
                                {{ substr(auth()->guard('officer')->user()->name ?? auth()->user()->name, 0, 1) }}
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
                                <p class="text-sm text-gray-500 sub-header">Active Book Loans</p>
                                <h3 class="text-3xl font-bold text-gray-800 mt-1">{{ \App\Models\Book::where('status', 'borrowed')->count() }}</h3>
                            </div>
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                <x-heroicon-o-book-open class="h-6 w-6 text-blue-600" />
                            </div>
                        </div>
                        <div class="mt-4">
                            <span class="text-sm text-gray-500 sub-header">Books currently on loan</span>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500 sub-header">Active Product Loans</p>
                                <h3 class="text-3xl font-bold text-gray-800 mt-1">{{ \App\Models\BookProduct::where('status', 'borrowed')->count() }}</h3>
                            </div>
                            <div class="w-12 h-12 bg-cyan-100 rounded-lg flex items-center justify-center">
                                <x-heroicon-o-cube class="h-6 w-6 text-cyan-600" />
                            </div>
                        </div>
                        <div class="mt-4">
                            <span class="text-sm text-gray-500 sub-header">Products currently on loan</span>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500 sub-header">Pending Returns</p>
                                <h3 class="text-3xl font-bold text-gray-800 mt-1">
                                    {{ \App\Models\Book::where('status', 'borrowed')->where('checkout_appointment_end', '<', now())->count() + \App\Models\BookProduct::where('status', 'borrowed')->where('checkout_appointment_end', '<', now())->count() }}
                                </h3>
                            </div>
                            <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                                <x-heroicon-o-clock class="h-6 w-6 text-orange-600" />
                            </div>
                        </div>
                        <div class="mt-4">
                            <span class="text-sm text-red-600 font-medium">Overdue items</span>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500 sub-header">Pending Payments</p>
                                <h3 class="text-3xl font-bold text-gray-800 mt-1">{{ \App\Models\Payment::where('status', 'pending')->count() }}</h3>
                            </div>
                            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                <x-heroicon-o-credit-card class="h-6 w-6 text-green-600" />
                            </div>
                        </div>
                        <div class="mt-4">
                            <span class="text-sm text-gray-500 sub-header">Awaiting confirmation</span>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity & Quick Actions -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Recent Book Loans -->
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Recent Book Loans</h3>
                        <div class="space-y-4">
                            @foreach(\App\Models\Book::where('status', 'borrowed')->latest()->take(5)->get() as $book)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-cyan-400 rounded-lg flex items-center justify-center">
                                        <x-heroicon-o-book-open class="h-5 w-5 text-white" />
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-800">{{ $book->user->name ?? 'N/A' }}</p>
                                        <p class="text-xs text-gray-500 sub-header">Due: {{ $book->checkout_appointment_end ? \Carbon\Carbon::parse($book->checkout_appointment_end)->format('d M Y') : 'N/A' }}</p>
                                    </div>
                                </div>
                                <span class="text-xs px-2 py-1 bg-blue-100 text-blue-700 rounded-full">Active</span>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <a href="{{ route('officer.books.create') }}" class="flex flex-col items-center justify-center p-6 border-2 border-dashed border-gray-300 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition">
                                <x-heroicon-o-plus-circle class="h-8 w-8 text-blue-600 mb-2" />
                                <span class="text-sm font-medium text-gray-700">New Book Loan</span>
                            </a>
                            <a href="{{ route('officer.products.create') }}" class="flex flex-col items-center justify-center p-6 border-2 border-dashed border-gray-300 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition">
                                <x-heroicon-o-cube class="h-8 w-8 text-blue-600 mb-2" />
                                <span class="text-sm font-medium text-gray-700">New Product Loan</span>
                            </a>
                            <a href="{{ route('officer.returns.index') }}" class="flex flex-col items-center justify-center p-6 border-2 border-dashed border-gray-300 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition">
                                <x-heroicon-o-arrow-path class="h-8 w-8 text-blue-600 mb-2" />
                                <span class="text-sm font-medium text-gray-700">Process Return</span>
                            </a>
                            <a href="{{ route('officer.payments.index') }}" class="flex flex-col items-center justify-center p-6 border-2 border-dashed border-gray-300 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition">
                                <x-heroicon-o-credit-card class="h-8 w-8 text-blue-600 mb-2" />
                                <span class="text-sm font-medium text-gray-700">Verify Payment</span>
                            </a>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
