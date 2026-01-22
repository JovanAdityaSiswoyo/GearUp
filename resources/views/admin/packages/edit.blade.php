<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Package - Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    @include('sweetalert::alert')
    <div class="min-h-screen">
        @include('admin.partials.sidebar')

        <div class="ml-64">
            @include('admin.partials.header', ['title' => 'Edit Package'])

            <main class="p-8">
                <div class="max-w-2xl mx-auto">
                    <div class="bg-white rounded-xl shadow-sm p-8">
                        <h2 class="text-2xl font-bold text-gray-800 mb-6">Edit Package</h2>

                        <form action="{{ route('admin.packages.update', $package->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="space-y-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Package Name *</label>
                                    <input type="text" name="name" value="{{ old('name', $package->name) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" required>
                                    @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                                    <textarea name="description" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">{{ old('description', $package->description) }}</textarea>
                                    @error('description') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Duration (Days) *</label>
                                    <input type="number" id="duration_days" name="duration_days" value="{{ old('duration_days', $package->duration_days) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-gray-100" readonly>
                                    <p class="text-xs text-gray-500 mt-1">Durasi publikasi paket (otomatis dari tanggal start dan end)</p>
                                    @error('duration_days') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Start Publish *</label>
                                        <input type="datetime-local" id="start_publish" name="start_publish" value="{{ old('start_publish', $package->start_publish ? $package->start_publish->format('Y-m-d\TH:i') : '') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" required>
                                        @error('start_publish') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">End Publish *</label>
                                        <input type="datetime-local" id="end_publish" name="end_publish" value="{{ old('end_publish', $package->end_publish ? $package->end_publish->format('Y-m-d\TH:i') : '') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" required>
                                        @error('end_publish') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                    </div>
                                </div>

                                <div>

                                    <label class="block text-sm font-medium text-gray-700 mb-2">Package Image</label>
                                    @if($package->image)
                                        <div class="mb-3">
                                            <img src="{{ asset('storage/' . $package->image) }}" alt="{{ $package->name }}" class="w-40 h-40 object-cover rounded-lg shadow-sm">
                                        </div>
                                    @endif
                                    <input type="file" name="image" accept="image/*" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                    <p class="text-sm text-gray-500 mt-1">Leave blank to keep current image</p>
                                    @error('image') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Gallery Images</label>
                                    @if($package->images->count())
                                    <div class="mb-4">
                                        <p class="text-xs text-gray-600 mb-2">Current Gallery Images:</p>
                                        <div class="grid grid-cols-4 gap-2">
                                            @foreach($package->images as $image)
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

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Products *</label>
                                    <div class="border border-gray-300 rounded-lg p-4 max-h-60 overflow-y-auto">
                                        @php
                                            $selectedProducts = old('products', $package->products->pluck('id')->toArray());
                                        @endphp
                                        @foreach(\App\Models\Product::with('category')->get() as $product)
                                        <label class="flex items-center p-2 hover:bg-gray-50 rounded cursor-pointer">
                                            <input type="checkbox" name="products[]" value="{{ $product->id }}" 
                                                data-price-per-day="{{ $product->price_per_day }}"
                                                {{ in_array($product->id, $selectedProducts) ? 'checked' : '' }}
                                                class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500 product-checkbox">
                                            <div class="ml-3 flex-1">
                                                <span class="text-sm font-medium text-gray-900">{{ $product->name }}</span>
                                                <span class="text-xs text-gray-500 ml-2">({{ $product->category->categories ?? $product->category->name ?? 'N/A' }})</span>
                                                <span class="text-xs text-purple-600 ml-2">Rp {{ number_format($product->price_per_day, 0, ',', '.') }}/day</span>
                                            </div>
                                        </label>
                                        @endforeach
                                    </div>
                                    @error('products') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                    <p class="text-sm text-gray-500 mt-2">Select at least one product for this package</p>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Harga *</label>
                                        <input type="number" id="price" name="price" value="{{ old('price', $package->price ?? 0) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-gray-100" readonly>
                                        <p class="text-xs text-gray-500 mt-1">Total harga per hari dari produk terpilih + upsell</p>
                                        @error('price') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Upsell (Optional)</label>
                                        <input type="number" id="upsell" name="upsell" value="{{ old('upsell', $package->upsell ?? 0) }}" min="0" step="5000" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                        <p class="text-xs text-gray-500 mt-1">Tambahan harga di atas total otomatis.</p>
                                        @error('upsell') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                    </div>
                                </div>

                                <div class="flex items-center space-x-4 pt-4">
                                    <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded-lg transition">
                                        Update Package
                                    </button>
                                    <a href="{{ route('admin.packages.index') }}" class="text-gray-600 hover:text-gray-800 px-6 py-2">
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

        function calculateDuration() {
            const startInput = document.getElementById('start_publish')?.value;
            const endInput = document.getElementById('end_publish')?.value;
            
            if (!startInput || !endInput) {
                document.getElementById('duration_days').value = '';
                return;
            }

            const startDate = new Date(startInput);
            const endDate = new Date(endInput);
            
            if (isNaN(startDate) || isNaN(endDate) || startDate >= endDate) {
                document.getElementById('duration_days').value = '';
                return;
            }

            const diffTime = endDate - startDate;
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
            document.getElementById('duration_days').value = diffDays;
        }

        function calculatePrice() {
            const upsell = parseInt(document.getElementById('upsell').value) || 0;
            let totalPrice = 0;
            
            // Sum price_per_day from all selected products
            const checkboxes = document.querySelectorAll('input[name="products[]"]:checked');
            checkboxes.forEach(checkbox => {
                const pricePerDay = parseInt(checkbox.dataset.pricePerDay) || 0;
                totalPrice += pricePerDay;
            });

            // Add upsell to total
            totalPrice += upsell;
            document.getElementById('price').value = totalPrice;
        }

        document.addEventListener('DOMContentLoaded', function() {
            const startInput = document.getElementById('start_publish');
            const endInput = document.getElementById('end_publish');
            const upsellInput = document.getElementById('upsell');
            const productCheckboxes = document.querySelectorAll('input[name="products[]"]');

            if (startInput) startInput.addEventListener('change', calculateDuration);
            if (endInput) endInput.addEventListener('change', calculateDuration);
            if (upsellInput) upsellInput.addEventListener('input', calculatePrice);
            
            productCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', calculatePrice);
            });

            // Initial calculation if form is pre-filled
            calculateDuration();
            calculatePrice();
        });
    </script>
</body>
</html>
