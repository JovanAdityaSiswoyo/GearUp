<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Units Management - Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-50">
    @include('sweetalert::alert')
    <div class="min-h-screen">
        @include('admin.partials.sidebar')

        <div class="ml-64">
            @include('admin.partials.header', ['title' => 'Units Management'])

            <main class="p-8">
                <div class="mb-6 flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">Manage Units</h2>
                        <p class="text-gray-600">Track individual product units and their status</p>
                    </div>
                    <a href="{{ route('admin.units.create') }}" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg flex items-center space-x-2 transition">
                        <x-heroicon-o-plus class="h-5 w-5" />
                        <span>Add Unit</span>
                    </a>
                </div>

                <!-- Filters -->
                <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                    <form method="GET" action="{{ route('admin.units.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Product</label>
                            <select name="product_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                <option value="">All Products</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>
                                        {{ $product->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                <option value="">All Status</option>
                                <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Available</option>
                                <option value="booked" {{ request('status') == 'booked' ? 'selected' : '' }}>Booked</option>
                                <option value="deployed" {{ request('status') == 'deployed' ? 'selected' : '' }}>Deployed</option>
                                <option value="returning" {{ request('status') == 'returning' ? 'selected' : '' }}>Returning</option>
                                <option value="in_inspection" {{ request('status') == 'in_inspection' ? 'selected' : '' }}>In Inspection</option>
                                <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                <option value="lost_scrapped" {{ request('status') == 'lost_scrapped' ? 'selected' : '' }}>Lost/Scrapped</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Serial Number</label>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by serial..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        </div>
                        <div class="flex items-end space-x-2">
                            <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded-lg transition flex-1">
                                Filter
                            </button>
                            <a href="{{ route('admin.units.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-2 rounded-lg transition">
                                Reset
                            </a>
                        </div>
                    </form>
                </div>

                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Serial Number</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Maintenance</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($units as $unit)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-mono font-semibold text-gray-900">{{ $unit->serial_number }}</div>
                                        <div class="text-xs text-gray-500">Created {{ $unit->created_at->diffForHumans() }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            @if($unit->product->image)
                                            <img src="{{ asset('storage/' . $unit->product->image) }}" alt="{{ $unit->product->name }}" class="w-10 h-10 rounded-lg object-cover">
                                            @else
                                            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                                                <x-heroicon-o-cube class="h-5 w-5 text-purple-600" />
                                            </div>
                                            @endif
                                            <div class="ml-3">
                                                <div class="text-sm font-medium text-gray-900">{{ $unit->product->name }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusColors = [
                                                'available' => 'bg-green-100 text-green-800',
                                                'booked' => 'bg-blue-100 text-blue-800',
                                                'deployed' => 'bg-yellow-100 text-yellow-800',
                                                'returning' => 'bg-orange-100 text-orange-800',
                                                'in_inspection' => 'bg-purple-100 text-purple-800',
                                                'maintenance' => 'bg-red-100 text-red-800',
                                                'lost_scrapped' => 'bg-gray-100 text-gray-800',
                                            ];
                                            $statusLabels = [
                                                'available' => 'Available',
                                                'booked' => 'Booked',
                                                'deployed' => 'Deployed',
                                                'returning' => 'Returning',
                                                'in_inspection' => 'In Inspection',
                                                'maintenance' => 'Maintenance',
                                                'lost_scrapped' => 'Lost/Scrapped',
                                            ];
                                        @endphp
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$unit->status] ?? 'bg-gray-100 text-gray-800' }}">
                                            {{ $statusLabels[$unit->status] ?? ucfirst($unit->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $unit->last_maintenance_at ? $unit->last_maintenance_at->format('M d, Y') : 'Never' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 max-w-xs truncate">
                                            {{ $unit->notes ?? '-' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                        <a href="{{ route('admin.units.edit', $unit->id) }}" class="text-purple-600 hover:text-purple-900">Edit</a>
                                        <form action="{{ route('admin.units.destroy', $unit->id) }}" method="POST" class="inline" id="delete-form-{{ $unit->id }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="text-red-600 hover:text-red-900" onclick='confirmDelete("{{ $unit->id }}")'>Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                        <x-heroicon-o-cube class="h-12 w-12 text-gray-400 mx-auto mb-4" />
                                        <p class="text-lg font-medium">No units found</p>
                                        <p class="text-sm">Start by adding your first unit</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($units->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $units->links() }}
                    </div>
                    @endif
                </div>
            </main>
        </div>
    </div>

    <script>
        function confirmDelete(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this! This will also update product stock.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        }
    </script>
</body>
</html>
