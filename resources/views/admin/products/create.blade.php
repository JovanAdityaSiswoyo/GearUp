<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Product - Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    @include('sweetalert::alert')
    <div class="min-h-screen">
        @include('admin.partials.sidebar')

        <div class="ml-64">
            @include('admin.partials.header', ['title' => 'Create Product'])

            <main class="p-8">
                <div class="max-w-2xl mx-auto">
                    <div class="bg-white rounded-xl shadow-sm p-8">
                        <h2 class="text-2xl font-bold text-gray-800 mb-6">Add New Product</h2>

                        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="space-y-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Product Name *</label>
                                    <input type="text" name="name" value="{{ old('name') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" required>
                                    @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Category *</label>
                                    <select name="id_category" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" required>
                                        <option value="">Select Category</option>
                                        @foreach(\App\Models\Category::all() as $category)
                                            <option value="{{ $category->id }}" {{ old('id_category') == $category->id ? 'selected' : '' }}>
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
                                            <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>
                                                {{ $brand->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('brand_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                                    <textarea name="description" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">{{ old('description') }}</textarea>
                                    @error('description') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Price per Day *</label>
                                        <input type="number" name="price_per_day" value="{{ old('price_per_day') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" required>
                                        @error('price_per_day') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Stock *</label>
                                        <input type="number" name="stock" value="{{ old('stock') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" required>
                                        @error('stock') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Product Image</label>
                                    <input type="file" name="image" accept="image/*" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                    @error('image') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Gallery Images (Multiple)</label>
                                    <input type="file" name="images[]" accept="image/*" multiple class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                    <p class="text-sm text-gray-500 mt-1">Select multiple images for gallery (JPG, PNG, max 2MB per file)</p>
                                    @error('images') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                    @error('images.*') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div class="flex items-center space-x-4 pt-4">
                                    <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded-lg transition">
                                        Create Product
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
</body>
</html>
