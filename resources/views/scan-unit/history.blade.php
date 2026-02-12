<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scan History - {{ $unit->serial_number }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <div class="min-h-screen py-8">
        <div class="max-w-4xl mx-auto px-4">
            <!-- Header -->
            <div class="mb-8">
                <a href="{{ route('scan-unit.show', $unit) }}" class="text-purple-600 hover:text-purple-800 flex items-center gap-2 mb-4">
                    <x-heroicon-o-arrow-left class="h-5 w-5" />
                    Back to Unit
                </a>
                <h1 class="text-4xl font-bold text-gray-900">Scan History</h1>
                <p class="text-gray-600 mt-2">{{ $unit->serial_number }}</p>
            </div>

            <!-- Timeline -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="bg-gradient-to-r from-purple-600 to-purple-700 text-white px-6 py-4">
                    <h2 class="text-xl font-semibold">📋 Complete Activity Log</h2>
                    <p class="text-purple-100 text-sm mt-1">Total: {{ count($history) }} scan records</p>
                </div>

                <div class="divide-y divide-gray-200">
                    @forelse($history as $index => $log)
                    <div class="p-6 hover:bg-gray-50 transition">
                        <div class="flex gap-4">
                            <!-- Timeline Marker -->
                            <div class="flex flex-col items-center">
                                <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center font-semibold text-purple-600">
                                    {{ count($history) - $index }}
                                </div>
                                @if(!$loop->last)
                                <div class="w-1 h-12 bg-purple-200 mt-2"></div>
                                @endif
                            </div>

                            <!-- Content -->
                            <div class="flex-1 pb-2">
                                <div class="flex items-start justify-between mb-2">
                                    <div>
                                        <h3 class="font-semibold text-gray-900 text-lg">
                                            {{ ucfirst(str_replace('_', ' ', $log['action'])) }}
                                        </h3>
                                        <p class="text-sm text-gray-600 mt-1">
                                            <strong>{{ $log['actor'] }}</strong> 
                                            <span class="mx-2">•</span>
                                            <span class="text-purple-600 font-medium">{{ $log['actor_type'] }}</span>
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-mono text-gray-500">{{ $log['at'] }}</p>
                                    </div>
                                </div>

                                @if($log['notes'])
                                <div class="mt-3 bg-gray-50 rounded-lg p-3 border-l-4 border-purple-300">
                                    <p class="text-sm text-gray-700">{{ $log['notes'] }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="p-12 text-center">
                        <x-heroicon-o-inbox class="h-12 w-12 text-gray-400 mx-auto mb-4" />
                        <p class="text-gray-500 text-lg">No scan history yet</p>
                        <p class="text-gray-400 text-sm mt-2">This unit hasn't been scanned</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Stats -->
            @if(count($history) > 0)
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <p class="text-gray-600 text-sm font-medium mb-2">Total Scans</p>
                    <p class="text-3xl font-bold text-purple-600">{{ count($history) }}</p>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6">
                    <p class="text-gray-600 text-sm font-medium mb-2">First Scan</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $history[array_key_last($history)]['at'] ?? 'N/A' }}</p>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6">
                    <p class="text-gray-600 text-sm font-medium mb-2">Last Scan</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $history[0]['at'] ?? 'N/A' }}</p>
                </div>
            </div>
            @endif

            <!-- Action Buttons -->
            <div class="mt-6 flex gap-3">
                <a href="{{ route('scan-unit.show', $unit) }}" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded-lg transition inline-flex items-center gap-2">
                    <x-heroicon-o-arrow-left class="h-4 w-4" />
                    Back to Unit
                </a>
                <a href="/" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-2 rounded-lg transition inline-flex items-center gap-2">
                    <x-heroicon-o-home class="h-4 w-4" />
                    Home
                </a>
            </div>
        </div>
    </div>
</body>
</html>
