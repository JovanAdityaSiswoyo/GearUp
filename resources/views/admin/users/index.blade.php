<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users Management - Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-50">
    @include('sweetalert::alert')
    <div class="min-h-screen">
        @include('admin.partials.sidebar')

        <div class="ml-64">
            @include('admin.partials.header', ['title' => 'Users Management'])

            <main class="p-8">
                <div class="mb-6 flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">Manage Users</h2>
                        <p class="text-gray-600">Add, edit, or remove users</p>
                    </div>
                    <a href="{{ route('admin.users.create') }}" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg flex items-center space-x-2 transition">
                        <x-heroicon-o-plus class="h-5 w-5" />
                        <span>Add User</span>
                    </a>
                </div>

                <!-- Filter & Search -->
                <form method="GET" action="{{ route('admin.users.index') }}" class="mb-6 bg-white p-6 rounded-xl shadow-sm">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or email..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                            <select name="role" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                <option value="">All Roles</option>
                                <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>User</option>
                                <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="officer" {{ request('role') == 'officer' ? 'selected' : '' }}>Officer</option>
                            </select>
                        </div>
                        <div class="flex items-end gap-2">
                            <button type="submit" class="bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 transition-colors flex-1">
                                <x-heroicon-o-funnel class="h-5 w-5 inline" />
                                Filter
                            </button>
                            <a href="{{ route('admin.users.index') }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 transition-colors">
                                Reset
                            </a>
                        </div>
                    </div>
                </form>

                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800">Users List</h3>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joined</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($users as $user)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-gradient-to-br from-purple-400 to-pink-400 rounded-full flex items-center justify-center text-white font-semibold">
                                                {{ substr($user->name, 0, 1) }}
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $user->email }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            @if($user->user_type === 'admin') bg-purple-100 text-purple-800
                                            @elseif($user->user_type === 'officer') bg-orange-100 text-orange-800
                                            @else bg-blue-100 text-blue-800
                                            @endif">
                                            {{ ucfirst($user->user_type) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Active
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $user->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                        <button onclick='showDetail("{{ $user->id }}", "{{ addslashes($user->name) }}", "{{ $user->email }}", "{{ $user->phone ?? 'N/A' }}", "{{ $user->created_at->format('M d, Y') }}")' class="text-blue-600 hover:text-blue-900">View</button>
                                        @if($user->user_type === 'user')
                                        <a href="{{ route('admin.users.edit', $user->id) }}" class="text-purple-600 hover:text-purple-900">Edit</a>
                                        @endif
                                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline" id="delete-form-{{ $user->id }}">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="type" value="{{ $user->user_type }}">
                                            <button type="button" class="text-red-600 hover:text-red-900" onclick='confirmDelete("{{ $user->id }}")'>Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $users->links() }}
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Detail Modal -->
    <div id="detailModal" class="fixed inset-0 overflow-y-auto h-full w-full z-50" style="display: none;">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-2xl rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">User Details</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="mt-2">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Name</label>
                    <p id="modal-name" class="mt-1 text-sm text-gray-900"></p>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Email</label>
                    <p id="modal-email" class="mt-1 text-sm text-gray-900"></p>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Phone</label>
                    <p id="modal-phone" class="mt-1 text-sm text-gray-900"></p>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Joined Date</label>
                    <p id="modal-date" class="mt-1 text-sm text-gray-900"></p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showDetail(id, name, email, phone, date) {
            document.getElementById('modal-name').textContent = name;
            document.getElementById('modal-email').textContent = email;
            document.getElementById('modal-phone').textContent = phone || 'N/A';
            document.getElementById('modal-date').textContent = date;
            document.getElementById('detailModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('detailModal').style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('detailModal');
            if (event.target == modal) {
                closeModal();
            }
        }

        function confirmDelete(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
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
