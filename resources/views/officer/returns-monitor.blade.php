<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitor Returns | Officer</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .sub-header { font-family: 'Inter', sans-serif; }
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
                <a href="{{ route('officer.dashboard') }}" class="flex items-center px-6 py-3 hover:bg-white/10 transition">
                    <x-heroicon-o-home class="h-5 w-5 mr-3" />
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('officer.loan-approvals.index') }}" class="flex items-center px-6 py-3 hover:bg-white/10 transition">
                    <x-heroicon-o-clipboard-document-list class="h-5 w-5 mr-3" />
                    <span>Loan Approvals</span>
                </a>
                <a href="{{ route('officer.packing.index') }}" class="flex items-center px-6 py-3 hover:bg-white/10 transition">
                    <x-heroicon-o-square-3-stack-3d class="h-5 w-5 mr-3" />
                    <span>Packing</span>
                </a>
                <a href="{{ route('officer.returns.monitor') }}" class="flex items-center px-6 py-3 bg-white/20 border-r-4 border-white">
                    <x-heroicon-o-arrow-path class="h-5 w-5 mr-3" />
                    <span>Monitor Returns</span>
                </a>
                <a href="{{ route('officer.reports.print') }}" class="flex items-center px-6 py-3 hover:bg-white/10 transition">
                    <x-heroicon-o-printer class="h-5 w-5 mr-3" />
                    <span>Print Report</span>
                </a>
                <a href="{{ route('officer.payments.index') }}" class="flex items-center px-6 py-3 hover:bg-white/10 transition">
                    <x-heroicon-o-credit-card class="h-5 w-5 mr-3" />
                    <span>Payments</span>
                </a>
            </nav>
        </aside>
        <!-- Main Content -->
        <div class="flex-1">
            <header class="bg-white shadow-sm">
                <div class="flex items-center justify-between px-8 py-4">
                    <div>
                        <h2 class="text-2xl font-semibold text-gray-800">Monitor Returns</h2>
                        <p class="text-sm text-gray-500 sub-header">Monitor and process equipment returns</p>
                    </div>
                </div>
            </header>
            <main class="p-8">
                <div class="bg-white rounded-xl shadow-md p-6">
                    <h3 class="text-xl font-semibold mb-4 text-gray-800">Returns Monitoring</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Equipment</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Returned At</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                            @foreach($returns as $return)
                                <tr>
                                    <td class="px-4 py-2">{{ $return->user->name }}</td>
                                    <td class="px-4 py-2">{{ $return->equipment->name }}</td>
                                    <td class="px-4 py-2">{{ $return->due_date->format('d M Y') }}</td>
                                    <td class="px-4 py-2">
                                        @if($return->is_overdue)
                                            <span class="inline-block px-2 py-1 text-xs font-semibold bg-red-100 text-red-800 rounded">Overdue</span>
                                        @elseif($return->returned_at)
                                            <span class="inline-block px-2 py-1 text-xs font-semibold bg-green-100 text-green-800 rounded">Returned</span>
                                        @else
                                            <span class="inline-block px-2 py-1 text-xs font-semibold bg-yellow-100 text-yellow-800 rounded">Pending</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2">{{ $return->returned_at ? $return->returned_at->format('d M Y') : '-' }}</td>
                                    <td class="px-4 py-2">
                                        @if(!$return->returned_at)
                                        <form action="{{ route('officer.returns.process', $return->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="px-3 py-1 bg-blue-500 hover:bg-blue-600 text-white text-xs rounded shadow">Mark as Returned</button>
                                        </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($returns->isEmpty())
                        <div class="mt-4 p-4 bg-blue-50 text-blue-700 rounded">No returns to monitor.</div>
                    @endif
                    <div class="mt-4">
                        {{ $returns->links() }}
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
