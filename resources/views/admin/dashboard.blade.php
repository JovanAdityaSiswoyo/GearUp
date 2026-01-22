<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - AplikasiPinjam</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    @include('sweetalert::alert')
    <div class="min-h-screen">
        @include('admin.partials.sidebar')

        <div class="ml-64">
            @include('admin.partials.header', ['title' => 'Dashboard'])

            <main class="p-8">
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Total Users Card -->
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="p-3 bg-purple-100 rounded-lg">
                                <x-heroicon-o-users class="h-6 w-6 text-purple-600" />
                            </div>
                            <span class="text-xs text-green-600 font-semibold">+12%</span>
                        </div>
                        <h3 class="text-gray-500 text-sm font-medium">Total Users</h3>
                        <p class="text-2xl font-bold text-gray-800">{{ \App\Models\User::count() }}</p>
                    </div>

                    <!-- Total Peminjaman Card -->
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="p-3 bg-blue-100 rounded-lg">
                                <x-heroicon-o-clipboard-document-list class="h-6 w-6 text-blue-600" />
                            </div>
                            <span class="text-xs text-green-600 font-semibold">+8%</span>
                        </div>
                        <h3 class="text-gray-500 text-sm font-medium">Total Peminjaman</h3>
                        <p class="text-2xl font-bold text-gray-800">{{ \App\Models\BookProduct::count() }}</p>
                    </div>

                    <!-- Total Products Card -->
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="p-3 bg-pink-100 rounded-lg">
                                <x-heroicon-o-cube class="h-6 w-6 text-pink-600" />
                            </div>
                            <span class="text-xs text-green-600 font-semibold">+5%</span>
                        </div>
                        <h3 class="text-gray-500 text-sm font-medium">Total Products</h3>
                        <p class="text-2xl font-bold text-gray-800">{{ \App\Models\Product::count() }}</p>
                    </div>

                    <!-- Revenue Card -->
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="p-3 bg-green-100 rounded-lg">
                                <x-heroicon-o-currency-dollar class="h-6 w-6 text-green-600" />
                            </div>
                            <span class="text-xs text-green-600 font-semibold">+23%</span>
                        </div>
                        <h3 class="text-gray-500 text-sm font-medium">Total Revenue</h3>
                        <p class="text-2xl font-bold text-gray-800">Rp {{ number_format(\App\Models\Payment::sum('amount') ?? 0, 0, ',', '.') }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Recent Users -->
                    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Recent Users</h3>
                        <div class="space-y-4">
                            @foreach(\App\Models\User::latest()->take(5)->get() as $user)
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div class="flex items-center space-x-4">
                                    <div class="w-12 h-12 bg-gradient-to-br from-purple-600 to-pink-500 rounded-full flex items-center justify-center text-white font-semibold">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-semibold text-gray-800">{{ $user->name }}</h4>
                                        <p class="text-xs text-gray-500">{{ $user->email }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs text-gray-500">Joined</p>
                                    <p class="text-sm font-medium text-gray-700">{{ $user->created_at->format('M d, Y') }}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-semibold text-gray-800">Quick Actions</h3>
                            <span class="text-xs text-purple-600 font-semibold">Shortcuts</span>
                        </div>
                        <div class="space-y-3">
                            <a href="{{ route('admin.users.create') }}" class="group flex items-center p-4 bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl hover:from-purple-100 hover:to-pink-100 transition-all duration-200 shadow-sm hover:shadow-md">
                                <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center mr-4 group-hover:scale-110 transition-transform">
                                    <x-heroicon-o-user-plus class="h-6 w-6 text-white" />
                                </div>
                                <div class="flex-1">
                                    <h4 class="text-sm font-semibold text-gray-800">Add New User</h4>
                                    <p class="text-xs text-gray-500">Create user account</p>
                                </div>
                                <x-heroicon-o-chevron-right class="h-5 w-5 text-gray-400 group-hover:text-purple-600 transition-colors" />
                            </a>

                            <a href="{{ route('admin.products.create') }}" class="group flex items-center p-4 bg-gradient-to-r from-blue-50 to-cyan-50 rounded-xl hover:from-blue-100 hover:to-cyan-100 transition-all duration-200 shadow-sm hover:shadow-md">
                                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center mr-4 group-hover:scale-110 transition-transform">
                                    <x-heroicon-o-cube class="h-6 w-6 text-white" />
                                </div>
                                <div class="flex-1">
                                    <h4 class="text-sm font-semibold text-gray-800">Add Product</h4>
                                    <p class="text-xs text-gray-500">Register new product</p>
                                </div>
                                <x-heroicon-o-chevron-right class="h-5 w-5 text-gray-400 group-hover:text-blue-600 transition-colors" />
                            </a>

                            <a href="{{ route('admin.bookings.index') }}" class="group flex items-center p-4 bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl hover:from-green-100 hover:to-emerald-100 transition-all duration-200 shadow-sm hover:shadow-md">
                                <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center mr-4 group-hover:scale-110 transition-transform">
                                    <x-heroicon-o-clipboard-document-list class="h-6 w-6 text-white" />
                                </div>
                                <div class="flex-1">
                                    <h4 class="text-sm font-semibold text-gray-800">Peminjaman</h4>
                                    <p class="text-xs text-gray-500">Manage bookings</p>
                                </div>
                                <x-heroicon-o-chevron-right class="h-5 w-5 text-gray-400 group-hover:text-green-600 transition-colors" />
                            </a>

                            <a href="{{ route('admin.payments.index') }}" class="group flex items-center p-4 bg-gradient-to-r from-orange-50 to-amber-50 rounded-xl hover:from-orange-100 hover:to-amber-100 transition-all duration-200 shadow-sm hover:shadow-md">
                                <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center mr-4 group-hover:scale-110 transition-transform">
                                    <x-heroicon-o-credit-card class="h-6 w-6 text-white" />
                                </div>
                                <div class="flex-1">
                                    <h4 class="text-sm font-semibold text-gray-800">Payments</h4>
                                    <p class="text-xs text-gray-500">View all payments</p>
                                </div>
                                <x-heroicon-o-chevron-right class="h-5 w-5 text-gray-400 group-hover:text-orange-600 transition-colors" />
                            </a>

                            <a href="{{ route('admin.reconciliation.index') }}" class="group flex items-center p-4 bg-gradient-to-r from-indigo-50 to-violet-50 rounded-xl hover:from-indigo-100 hover:to-violet-100 transition-all duration-200 shadow-sm hover:shadow-md">
                                <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl flex items-center justify-center mr-4 group-hover:scale-110 transition-transform">
                                    <x-heroicon-o-calculator class="h-6 w-6 text-white" />
                                </div>
                                <div class="flex-1">
                                    <h4 class="text-sm font-semibold text-gray-800">Rekonsiliasi</h4>
                                    <p class="text-xs text-gray-500">Payment reconciliation</p>
                                </div>
                                <x-heroicon-o-chevron-right class="h-5 w-5 text-gray-400 group-hover:text-indigo-600 transition-colors" />
                            </a>

                            <a href="{{ route('admin.activity-log.index') }}" class="group flex items-center p-4 bg-gradient-to-r from-pink-50 to-rose-50 rounded-xl hover:from-pink-100 hover:to-rose-100 transition-all duration-200 shadow-sm hover:shadow-md">
                                <div class="w-12 h-12 bg-gradient-to-br from-pink-500 to-pink-600 rounded-xl flex items-center justify-center mr-4 group-hover:scale-110 transition-transform">
                                    <x-heroicon-o-clock class="h-6 w-6 text-white" />
                                </div>
                                <div class="flex-1">
                                    <h4 class="text-sm font-semibold text-gray-800">Activity Log</h4>
                                    <p class="text-xs text-gray-500">View system logs</p>
                                </div>
                                <x-heroicon-o-chevron-right class="h-5 w-5 text-gray-400 group-hover:text-pink-600 transition-colors" />
                            </a>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
