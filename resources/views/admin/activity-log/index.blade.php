<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Log - Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-50">
    @include('sweetalert::alert')
    <div class="min-h-screen">
        @include('admin.partials.sidebar')

        <div class="ml-64">
            @include('admin.partials.header', ['title' => 'Activity Log'])

            <main class="p-8">
                <!-- Statistics Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                                <x-heroicon-o-clipboard-document-list class="h-8 w-8 text-white" />
                            </div>
                            <span class="text-sm font-medium bg-white/20 px-3 py-1 rounded-full">Total</span>
                        </div>
                        <div>
                            <p class="text-sm text-blue-100 mb-1">All Activities</p>
                            <p class="text-4xl font-bold">{{ $stats['total'] }}</p>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                                <x-heroicon-o-calendar class="h-8 w-8 text-white" />
                            </div>
                            <span class="text-sm font-medium bg-white/20 px-3 py-1 rounded-full">Today</span>
                        </div>
                        <div>
                            <p class="text-sm text-green-100 mb-1">Today's Activities</p>
                            <p class="text-4xl font-bold">{{ $stats['today'] }}</p>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                                <x-heroicon-o-chart-bar class="h-8 w-8 text-white" />
                            </div>
                            <span class="text-sm font-medium bg-white/20 px-3 py-1 rounded-full">Week</span>
                        </div>
                        <div>
                            <p class="text-sm text-purple-100 mb-1">This Week</p>
                            <p class="text-4xl font-bold">{{ $stats['this_week'] }}</p>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-lg p-6 text-white">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                                <x-heroicon-o-calendar-days class="h-8 w-8 text-white" />
                            </div>
                            <span class="text-sm font-medium bg-white/20 px-3 py-1 rounded-full">Month</span>
                        </div>
                        <div>
                            <p class="text-sm text-orange-100 mb-1">This Month</p>
                            <p class="text-4xl font-bold">{{ $stats['this_month'] }}</p>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                    <form method="GET" action="{{ route('admin.activity-log.index') }}" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                            <div>
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search description..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                            </div>
                            <div>
                                <select name="log_name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                    <option value="">All Logs</option>
                                    @foreach($logNames as $logName)
                                        <option value="{{ $logName }}" {{ request('log_name') == $logName ? 'selected' : '' }}>
                                            {{ ucfirst($logName) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <select name="event" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                    <option value="">All Events</option>
                                    @foreach($events as $event)
                                        <option value="{{ $event }}" {{ request('event') == $event ? 'selected' : '' }}>
                                            {{ ucfirst($event) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                            </div>
                            <div>
                                <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded-lg transition">
                                Apply Filters
                            </button>
                            <a href="{{ route('admin.activity-log.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-lg transition">
                                Reset
                            </a>
                        </div>
                    </form>
                </div>

                <!-- Activity Log Table -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Log Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Causer</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($logs as $log)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <div class="font-medium">{{ $log->created_at->format('d M Y') }}</div>
                                        <div class="text-xs text-gray-500">{{ $log->created_at->format('H:i:s') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $logColors = [
                                                'admin' => 'bg-purple-100 text-purple-800',
                                                'user' => 'bg-blue-100 text-blue-800',
                                                'booking' => 'bg-green-100 text-green-800',
                                                'payment' => 'bg-yellow-100 text-yellow-800',
                                                'product' => 'bg-pink-100 text-pink-800',
                                                'officer' => 'bg-indigo-100 text-indigo-800',
                                            ];
                                        @endphp
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $logColors[$log->log_name] ?? 'bg-gray-100 text-gray-800' }}">
                                            {{ ucfirst($log->log_name ?? 'System') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        {{ Str::limit($log->description, 60) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($log->event)
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            {{ ucfirst($log->event) }}
                                        </span>
                                        @else
                                        <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        @if($log->causer)
                                            <div class="flex items-center">
                                                <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center mr-2">
                                                    <span class="text-xs font-semibold text-purple-600">
                                                        {{ substr(class_basename($log->causer_type), 0, 1) }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <div class="text-sm font-medium">{{ class_basename($log->causer_type) }}</div>
                                                    <div class="text-xs text-gray-500">ID: {{ $log->causer_id }}</div>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-gray-400">System</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        @if($log->subject_type)
                                            <div class="text-sm">{{ class_basename($log->subject_type) }}</div>
                                            <div class="text-xs text-gray-500">ID: {{ $log->subject_id }}</div>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                        <div class="flex flex-col items-center">
                                            <x-heroicon-o-clipboard-document-list class="h-12 w-12 text-gray-300 mb-2" />
                                            <p>No activity logs found.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $logs->links() }}
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
