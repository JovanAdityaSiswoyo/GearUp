@extends('layouts.officer')

@section('title', 'Dashboard')

@section('content')
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
                            <a href="{{ route('officer.bookings.index') }}" class="flex flex-col items-center justify-center p-6 border-2 border-dashed border-gray-300 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition">
                                <x-heroicon-o-clipboard-document-list class="h-8 w-8 text-blue-600 mb-2" />
                                <span class="text-sm font-medium text-gray-700">Booking Management</span>
                            </a>
                            <a href="{{ route('officer.returns.monitor') }}" class="flex flex-col items-center justify-center p-6 border-2 border-dashed border-gray-300 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition">
                                <x-heroicon-o-arrow-path class="h-8 w-8 text-blue-600 mb-2" />
                                <span class="text-sm font-medium text-gray-700">Monitor Returns</span>
                            </a>
                            <a href="{{ route('officer.reports.print') }}" class="flex flex-col items-center justify-center p-6 border-2 border-dashed border-gray-300 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition">
                                <x-heroicon-o-printer class="h-8 w-8 text-blue-600 mb-2" />
                                <span class="text-sm font-medium text-gray-700">Print Report</span>
                            </a>
                            <a href="{{ route('officer.payments.index') }}" class="flex flex-col items-center justify-center p-6 border-2 border-dashed border-gray-300 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition">
                                <x-heroicon-o-credit-card class="h-8 w-8 text-blue-600 mb-2" />
                                <span class="text-sm font-medium text-gray-700">Verify Payment</span>
                            </a>
                        </div>
                    </div>
                </div>
@endsection
