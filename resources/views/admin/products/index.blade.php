<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products Management - Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-50">
    @include('sweetalert::alert')
    <div class="min-h-screen">
        @include('admin.partials.sidebar')

        <div class="ml-64">
            @include('admin.partials.header', ['title' => 'Products Management'])

            <main class="p-8">
                <div class="mb-6 flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">Manage Products</h2>
                        <p class="text-gray-600">Add, edit, or remove products</p>
                    </div>
                    <a href="{{ route('admin.products.create') }}" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg flex items-center space-x-2 transition">
                        <x-heroicon-o-plus class="h-5 w-5" />
                        <span>Add Product</span>
                    </a>
                </div>

                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-gray-200">
                        <input type="text" placeholder="Search products..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price/Day</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($products as $product)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            @if($product->image)
                                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-12 h-12 rounded-lg object-cover">
                                            @else
                                            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                                                <x-heroicon-o-cube class="h-6 w-6 text-purple-600" />
                                            </div>
                                            @endif
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                                                <div class="text-sm text-gray-500">{{ Str::limit($product->description, 40) }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            {{ $product->category->categories ?? $product->category->name ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        Rp {{ number_format($product->price_per_day, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <div class="flex items-center space-x-2">
                                            <span>{{ $product->stock }}</span>
                                            <form action="{{ route('admin.products.stock', $product->id) }}" method="POST" class="flex items-center space-x-1">
                                                @csrf
                                                @method('PATCH')
                                                <input type="number" name="amount" min="1" value="1" class="w-16 px-2 py-1 border border-gray-300 rounded focus:ring-1 focus:ring-purple-500 focus:border-purple-500 text-sm" />
                                                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-2 py-1 rounded text-xs font-semibold">+ Stock</button>
                                            </form>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $product->stock > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $product->stock > 0 ? 'Available' : 'Out of Stock' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                        <button type="button" onclick='showDetail(@json($product->name), @json($product->category->categories ?? $product->category->name ?? "N/A"), @json($product->description), @json($product->price_per_day), @json($product->stock))' class="text-blue-600 hover:text-blue-900">View</button>
                                        <a href="{{ route('admin.products.edit', $product->id) }}" class="text-purple-600 hover:text-purple-900">Edit</a>
                                        <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="inline" id="delete-form-{{ $product->id }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="text-red-600 hover:text-red-900" onclick='confirmDelete("{{ $product->id }}")'>Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $products->links() }}
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Detail Modal -->
    <div id="detailModal" class="fixed inset-0 overflow-y-auto h-full w-full z-50" style="display: none;">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-2xl rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Product Details</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="mt-2">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Product Name</label>
                    <p id="modal-name" class="mt-1 text-sm text-gray-900"></p>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Category</label>
                    <p id="modal-category" class="mt-1 text-sm text-gray-900"></p>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Description</label>
                    <p id="modal-description" class="mt-1 text-sm text-gray-900"></p>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Price per Day</label>
                    <p id="modal-price" class="mt-1 text-sm text-gray-900"></p>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Stock</label>
                    <p id="modal-stock" class="mt-1 text-sm text-gray-900"></p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showDetail(name, category, description, price, stock) {
            document.getElementById('modal-name').textContent = name;
            document.getElementById('modal-category').textContent = category;
            document.getElementById('modal-description').textContent = description || 'No description';
            document.getElementById('modal-price').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(price);
            document.getElementById('modal-stock').textContent = stock;
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
