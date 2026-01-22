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
                </div>
            </main>
        </div>
    </div>

    <script>
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
