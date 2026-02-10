<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product - Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    @include('sweetalert::alert')
    <div class="min-h-screen">
        @include('admin.partials.sidebar')

        <div class="ml-64">
            @include('admin.partials.header', ['title' => 'Edit Product'])

            <main class="p-8">
                <div class="max-w-2xl mx-auto">
                    <div class="bg-white rounded-xl shadow-sm p-8">
                        <h2 class="text-2xl font-bold text-gray-800 mb-6">Edit Product</h2>

                        <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="space-y-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Product Name *</label>
                                    <input type="text" name="name" value="{{ old('name', $product->name) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" required>
                                    @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Category *</label>
                                    <select name="id_category" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" required>
                                        <option value="">Select Category</option>
                                        @foreach(\App\Models\Category::all() as $category)
                                            <option value="{{ $category->id }}" {{ old('id_category', $product->id_category) == $category->id ? 'selected' : '' }}>
                                                {{ $category->categories ?? $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('id_category') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Brand</label>
                                    <select name="brand_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                        <option value="">Select Brand (Optional)</option>
                                        @foreach(\App\Models\Brand::all() as $brand)
                                            <option value="{{ $brand->id }}" {{ old('brand_id', $product->brand_id) == $brand->id ? 'selected' : '' }}>
                                                {{ $brand->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('brand_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                                    <textarea name="description" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">{{ old('description', $product->description) }}</textarea>
                                    @error('description') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Price per Day *</label>
                                        <input type="number" name="price_per_day" value="{{ old('price_per_day', $product->price_per_day) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" required>
                                        @error('price_per_day') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Stock *</label>
                                        <input type="number" name="stock" value="{{ old('stock', $product->stock) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" required>
                                        @error('stock') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Product Image</label>
                                    @if($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-32 h-32 object-cover rounded-lg mb-2">
                                    @endif
                                    <input type="file" name="image" accept="image/*" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                    <p class="text-sm text-gray-500 mt-1">Leave blank to keep current image</p>
                                    @error('image') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Gallery Images</label>
                                    @if($product->images->count())
                                    <div class="mb-4">
                                        <p class="text-xs text-gray-600 mb-2">Current Gallery Images:</p>
                                        <div class="grid grid-cols-4 gap-2">
                                            @foreach($product->images as $image)
                                            <div class="relative group">
                                                <img src="{{ asset('storage/' . $image->image) }}" alt="Gallery" class="w-full h-24 object-cover rounded-lg">
                                                <button type="button" onclick="deleteImage({{ $image->id }})" class="absolute top-1 right-1 bg-red-500 hover:bg-red-600 text-white p-1 rounded opacity-0 group-hover:opacity-100 transition">
                                                    <x-heroicon-o-trash class="h-4 w-4" />
                                                </button>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endif
                                    <input type="file" name="images[]" accept="image/*" multiple class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                    <p class="text-sm text-gray-500 mt-1">Add more images to gallery</p>
                                    @error('images') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                    @error('images.*') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div class="flex items-center space-x-4 pt-4">
                                    <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded-lg transition">
                                        Update Product
                                    </button>
                                    <a href="{{ route('admin.products.index') }}" class="text-gray-600 hover:text-gray-800 px-6 py-2">
                                        Cancel
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Units Section -->
                    <div class="bg-white rounded-xl shadow-sm p-8 mt-6">
                        <div class="flex justify-between items-center mb-6">
                            <div>
                                <h2 class="text-2xl font-bold text-gray-800">Product Units</h2>
                                <p class="text-gray-600 text-sm">Manage individual units for this product</p>
                            </div>
                            <div class="flex space-x-2">
                                <a href="{{ route('admin.units.create') }}?product={{ $product->id }}" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition text-sm">
                                    <x-heroicon-o-plus class="h-4 w-4" />
                                    <span>Add Single Unit</span>
                                </a>
                                <button onclick="showBulkCreateModal()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition text-sm">
                                    <x-heroicon-o-squares-plus class="h-4 w-4" />
                                    <span>Bulk Create</span>
                                </button>
                            </div>
                        </div>

                        <div class="mb-4 grid grid-cols-4 gap-4">
                            <div class="bg-blue-50 p-4 rounded-lg">
                                <p class="text-2xl font-bold text-blue-600">{{ $product->units()->count() }}</p>
                                <p class="text-sm text-gray-600">Total Units</p>
                            </div>
                            <div class="bg-green-50 p-4 rounded-lg">
                                <p class="text-2xl font-bold text-green-600">{{ $product->units()->where('status', 'available')->count() }}</p>
                                <p class="text-sm text-gray-600">Available</p>
                            </div>
                            <div class="bg-yellow-50 p-4 rounded-lg">
                                <p class="text-2xl font-bold text-yellow-600">{{ $product->units()->whereIn('status', ['booked', 'deployed'])->count() }}</p>
                                <p class="text-sm text-gray-600">In Use</p>
                            </div>
                            <div class="bg-red-50 p-4 rounded-lg">
                                <p class="text-2xl font-bold text-red-600">{{ $product->units()->where('status', 'maintenance')->count() }}</p>
                                <p class="text-sm text-gray-600">Maintenance</p>
                            </div>
                        </div>

                        @if($product->units()->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50 border-b border-gray-200">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Serial Number</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Last Maintenance</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($product->units()->latest()->take(10)->get() as $unit)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-sm font-mono">{{ $unit->serial_number }}</td>
                                        <td class="px-4 py-3">
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
                                            @endphp
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $statusColors[$unit->status] ?? 'bg-gray-100 text-gray-800' }}">
                                                {{ ucfirst(str_replace('_', ' ', $unit->status)) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-600">
                                            {{ $unit->last_maintenance_at ? $unit->last_maintenance_at->format('M d, Y') : 'Never' }}
                                        </td>
                                        <td class="px-4 py-3 text-sm space-x-2">
                                            <a href="{{ route('admin.units.edit', $unit->id) }}" class="text-purple-600 hover:text-purple-900">Edit</a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if($product->units()->count() > 10)
                        <div class="mt-4 text-center">
                            <a href="{{ route('admin.units.index') }}?product_id={{ $product->id }}" class="text-purple-600 hover:text-purple-800 text-sm font-medium">
                                View All {{ $product->units()->count() }} Units →
                            </a>
                        </div>
                        @endif
                        @else
                        <div class="text-center py-12">
                            <x-heroicon-o-cube class="h-12 w-12 text-gray-400 mx-auto mb-4" />
                            <p class="text-gray-600 mb-2">No units created yet</p>
                            <p class="text-sm text-gray-500">Start by adding units to this product</p>
                        </div>
                        @endif
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Bulk Create Modal -->
    <div id="bulkCreateModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-8 border w-full max-w-md shadow-lg rounded-xl bg-white">
            <div class="mb-6">
                <h3 class="text-xl font-bold text-gray-800">Bulk Create Units</h3>
                <p class="text-sm text-gray-600 mt-2">Create multiple units for {{ $product->name }}</p>
            </div>

            <form action="{{ route('admin.products.units.bulk', $product->id) }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Number of Units *</label>
                        <input type="number" name="quantity" min="1" max="100" value="10" required 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        <p class="text-xs text-gray-500 mt-1">Maximum 100 units at once</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Serial Number Prefix</label>
                        <input type="text" name="prefix" value="{{ strtoupper(substr($product->name, 0, 3)) }}" 
                               placeholder="e.g., CAM"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        <p class="text-xs text-gray-500 mt-1">Will be formatted as: PREFIX-TIMESTAMP-0001</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Initial Status *</label>
                        <select name="status" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                            <option value="available">Available</option>
                            <option value="maintenance">Maintenance</option>
                            <option value="in_inspection">In Inspection</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                        <textarea name="notes" rows="3" 
                                  placeholder="Add notes for all units..."
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"></textarea>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="hideBulkCreateModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition">
                        Create Units
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function showBulkCreateModal() {
            document.getElementById('bulkCreateModal').classList.remove('hidden');
        }

        function hideBulkCreateModal() {
            document.getElementById('bulkCreateModal').classList.add('hidden');
        }

        // Close modal when clicking outside
        document.getElementById('bulkCreateModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                hideBulkCreateModal();
            }
        });

        function deleteImage(imageId) {
            if (confirm('Are you sure you want to delete this image?')) {
                // In a real scenario, you'd send an AJAX request to delete the image
                // For now, we'll just remove it from the gallery visually
                fetch(`/admin/gallery-images/${imageId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                }).then(response => {
                    if (response.ok) {
                        location.reload();
                    } else {
                        alert('Failed to delete image');
                    }
                }).catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while deleting the image');
                });
            }
        }
    </script>
</body>
</html>
