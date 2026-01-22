<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categories Management - Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-50">
    @include('sweetalert::alert')
    <div class="min-h-screen">
        @include('admin.partials.sidebar')

        <div class="ml-64">
            @include('admin.partials.header', ['title' => 'Categories Management'])

            <main class="p-8">
                <div class="mb-6 flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">Manage Categories</h2>
                        <p class="text-gray-600">Add, edit, or remove categories</p>
                    </div>
                    <a href="{{ route('admin.categories.create') }}" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg flex items-center space-x-2 transition">
                        <x-heroicon-o-plus class="h-5 w-5" />
                        <span>Add Category</span>
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($categories as $category)
                    <div class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition">
                        <div class="flex items-start justify-between mb-4">
                            <div class="w-12 h-12 bg-gradient-to-br from-purple-400 to-pink-400 rounded-lg flex items-center justify-center">
                                <x-heroicon-o-tag class="h-6 w-6 text-white" />
                            </div>
                            <div class="flex space-x-2">
                                <button onclick="showDetail('{{ $category->categories ?? $category->name }}', '{{ addslashes($category->description ?? 'No description') }}', '{{ $category->products_count }}')" class="text-blue-600 hover:text-blue-900">
                                    <x-heroicon-o-eye class="h-5 w-5" />
                                </button>
                                <a href="{{ route('admin.categories.edit', $category->id) }}" class="text-purple-600 hover:text-purple-900">
                                    <x-heroicon-o-pencil class="h-5 w-5" />
                                </a>
                                <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" class="inline" id="delete-form-{{ $category->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="text-red-600 hover:text-red-900" onclick='confirmDelete("{{ $category->id }}")'>
                                        <x-heroicon-o-trash class="h-5 w-5" />
                                    </button>
                                </form>
                            </div>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">{{ $category->categories ?? $category->name }}</h3>
                        <p class="text-sm text-gray-600 mb-4">{{ $category->description ?? 'No description' }}</p>
                        <div class="flex items-center text-sm text-gray-500">
                            <x-heroicon-o-cube class="h-4 w-4 mr-1" />
                            <span>{{ $category->products_count }} Products</span>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="mt-6">
                    {{ $categories->links() }}
                </div>
            </main>
        </div>
    </div>

    <!-- Detail Modal -->
    <div id="detailModal" class="fixed inset-0 overflow-y-auto h-full w-full z-50" style="display: none;">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-2xl rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Category Details</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="mt-2">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Category Name</label>
                    <p id="modal-name" class="mt-1 text-sm text-gray-900"></p>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Description</label>
                    <p id="modal-description" class="mt-1 text-sm text-gray-900"></p>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Total Products</label>
                    <p id="modal-count" class="mt-1 text-sm text-gray-900"></p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showDetail(name, description, count) {
            document.getElementById('modal-name').textContent = name;
            document.getElementById('modal-description').textContent = description;
            document.getElementById('modal-count').textContent = count + ' products';
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
