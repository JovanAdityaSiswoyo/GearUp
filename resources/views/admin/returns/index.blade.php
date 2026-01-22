<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Returns Management - Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-50">
    @include('sweetalert::alert')
    <div class="min-h-screen">
        @include('admin.partials.sidebar')

        <div class="ml-64">
            @include('admin.partials.header', ['title' => 'Returns Management'])

            <main class="p-8">
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">Manage Returns</h2>
                    <p class="text-gray-600">Process product returns and completions</p>
                </div>

                <!-- Filter & Search -->
                <form method="GET" action="{{ route('admin.returns.index') }}" class="mb-6 bg-white p-6 rounded-xl shadow-sm">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by code or name..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                <option value="">All Status</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            </select>
                        </div>
                        <div class="flex items-end gap-2">
                            <button type="submit" class="bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 transition-colors flex-1">
                                <x-heroicon-o-funnel class="h-5 w-5 inline" />
                                Filter
                            </button>
                            <a href="{{ route('admin.returns.index') }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 transition-colors">
                                Reset
                            </a>
                        </div>
                    </div>
                </form>

                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Book Code</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rental Period</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($returns as $return)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-gray-900">{{ $return->book_code }}</div>
                                    <div class="text-xs text-gray-500">{{ $return->created_at->format('M d, Y') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $return->user->name ?? 'N/A' }}</div>
                                    <div class="text-xs text-gray-500">{{ $return->booker_email }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $return->product->name ?? 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $return->checkin_appointment_start->format('M d') }}</div>
                                    <div class="text-xs text-gray-500">to {{ $return->checkout_appointment_end->format('M d, Y') }}</div>
                                    @php
                                        $daysLeft = now()->diffInDays($return->checkout_appointment_end, false);
                                    @endphp
                                    @if($daysLeft < 0)
                                        <span class="text-xs text-red-600 font-semibold">Overdue {{ abs($daysLeft) }} days</span>
                                    @elseif($daysLeft <= 3)
                                        <span class="text-xs text-orange-600 font-semibold">{{ $daysLeft }} days left</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $return->amount }} pcs
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        @if($return->status === 'active') bg-green-100 text-green-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ ucfirst($return->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                    <a href="{{ route('admin.returns.show', $return->id) }}" class="text-blue-600 hover:text-blue-900">View</a>
                                    @if($return->status === 'active')
                                    <button onclick="processReturn('{{ $return->id }}')" class="text-green-600 hover:text-green-900">Complete</button>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-gray-500">No returns found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>                    </div>
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $returns->links() }}
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        function processReturn(id) {
            Swal.fire({
                title: 'Complete Return?',
                text: "Mark this rental as completed and returned?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, complete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Create form and submit
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '/admin/returns/' + id + '/process';
                    
                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';
                    form.appendChild(csrfToken);
                    
                    const statusInput = document.createElement('input');
                    statusInput.type = 'hidden';
                    statusInput.name = 'status';
                    statusInput.value = 'completed';
                    form.appendChild(statusInput);
                    
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    </script>
</body>
</html>
