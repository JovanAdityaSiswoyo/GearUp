@extends('layouts.courier')

@section('title', 'Dashboard')

@section('content')
<div class="py-8 px-4 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Dashboard Kurir</h1>
            <p class="text-gray-600 mt-2">Selamat datang, {{ auth()->user()->courier->nama ?? auth()->user()->name }}!</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Active Deliveries</p>
                        <h3 class="text-3xl font-bold text-gray-800 mt-1">0</h3>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <x-heroicon-o-truck class="h-6 w-6 text-green-600" />
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Pending Pickups</p>
                        <h3 class="text-3xl font-bold text-gray-800 mt-1">0</h3>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <x-heroicon-o-arrow-path class="h-6 w-6 text-blue-600" />
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Completed Today</p>
                        <h3 class="text-3xl font-bold text-gray-800 mt-1">0</h3>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <x-heroicon-o-check-circle class="h-6 w-6 text-green-600" />
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Returns</p>
                        <h3 class="text-3xl font-bold text-gray-800 mt-1">0</h3>
                    </div>
                    <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                        <x-heroicon-o-arrow-left-end-on-rectangle class="h-6 w-6 text-orange-600" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Recent Deliveries</h3>
                <div class="space-y-4">
                    <div class="text-center py-8">
                        <x-heroicon-o-inbox class="h-12 w-12 text-gray-300 mx-auto mb-2" />
                        <p class="text-gray-500">No active deliveries yet</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h3>
                <div class="grid grid-cols-2 gap-4">
                    <a href="{{ route('courier.deliveries.index') }}" class="flex flex-col items-center justify-center p-6 border-2 border-dashed border-gray-300 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition">
                        <x-heroicon-o-truck class="h-8 w-8 text-blue-600 mb-2" />
                        <span class="text-sm font-medium text-gray-700">Pengiriman</span>
                    </a>
                    <a href="{{ route('courier.deliveries.history') }}" class="flex flex-col items-center justify-center p-6 border-2 border-dashed border-gray-300 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition">
                        <x-heroicon-o-clock class="h-8 w-8 text-blue-600 mb-2" />
                        <span class="text-sm font-medium text-gray-700">History</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
