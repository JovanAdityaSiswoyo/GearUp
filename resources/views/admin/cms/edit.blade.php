<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Content - Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <div class="min-h-screen">
        @include('admin.partials.sidebar')

        <div class="ml-64">
            @include('admin.partials.header', ['title' => 'Edit Content'])

            <main class="p-8">
                <div class="max-w-3xl mx-auto">
                    <div class="bg-white rounded-lg shadow-sm p-8">
                        <h2 class="text-2xl font-bold text-gray-800 mb-6">Edit Content</h2>

                        <form action="{{ route('admin.cms.update', $cms->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="space-y-6">
                                <!-- Key -->
                                <div>
                                    <label for="key" class="block text-sm font-medium text-gray-700 mb-2">
                                        Content Key <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="key" id="key" value="{{ old('key', $cms->key) }}"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                        placeholder="e.g., hero_title, contact_phone" required>
                                    <p class="mt-1 text-sm text-gray-500">Unique identifier for this content</p>
                                    @error('key')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Label -->
                                <div>
                                    <label for="label" class="block text-sm font-medium text-gray-700 mb-2">
                                        Label <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="label" id="label" value="{{ old('label', $cms->label) }}"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                        placeholder="e.g., Hero Title, Contact Phone Number" required>
                                    @error('label')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Type -->
                                <div>
                                    <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                                        Content Type <span class="text-red-500">*</span>
                                    </label>
                                    <select name="type" id="type" 
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                        required onchange="toggleFields()">
                                        <option value="text" {{ old('type', $cms->type) === 'text' ? 'selected' : '' }}>Text</option>
                                        <option value="textarea" {{ old('type', $cms->type) === 'textarea' ? 'selected' : '' }}>Textarea</option>
                                        <option value="image" {{ old('type', $cms->type) === 'image' ? 'selected' : '' }}>Image</option>
                                        <option value="url" {{ old('type', $cms->type) === 'url' ? 'selected' : '' }}>URL</option>
                                        <option value="number" {{ old('type', $cms->type) === 'number' ? 'selected' : '' }}>Number</option>
                                    </select>
                                    @error('type')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Group -->
                                <div>
                                    <label for="group" class="block text-sm font-medium text-gray-700 mb-2">
                                        Group <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="group" id="group" value="{{ old('group', $cms->group) }}"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                        placeholder="e.g., hero, about, contact" required>
                                    @error('group')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Value (text/textarea/url/number) -->
                                <div id="valueField">
                                    <label for="value" class="block text-sm font-medium text-gray-700 mb-2">
                                        Value
                                    </label>
                                    <textarea name="value" id="value" rows="3"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                        placeholder="Enter content value">{{ old('value', $cms->value) }}</textarea>
                                    @error('value')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Image Upload -->
                                <div id="imageField" style="display: none;">
                                    <label for="image" class="block text-sm font-medium text-gray-700 mb-2">
                                        Upload New Image
                                    </label>
                                    
                                    @if($cms->type === 'image' && $cms->value)
                                        <div class="mb-4">
                                            <p class="text-sm text-gray-600 mb-2">Current Image:</p>
                                            <img src="{{ asset('storage/' . $cms->value) }}" alt="{{ $cms->label }}" class="h-32 w-auto rounded-lg border">
                                        </div>
                                    @endif
                                    
                                    <input type="file" name="image" id="image" accept="image/*"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                    <p class="mt-1 text-sm text-gray-500">Supported: JPEG, PNG, JPG, GIF (Max: 2MB). Leave empty to keep current image.</p>
                                    @error('image')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Description -->
                                <div>
                                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                        Description
                                    </label>
                                    <textarea name="description" id="description" rows="2"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                        placeholder="Optional description for this content">{{ old('description', $cms->description) }}</textarea>
                                    @error('description')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Order -->
                                <div>
                                    <label for="order" class="block text-sm font-medium text-gray-700 mb-2">
                                        Display Order
                                    </label>
                                    <input type="number" name="order" id="order" value="{{ old('order', $cms->order) }}"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                        placeholder="0">
                                    @error('order')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Buttons -->
                                <div class="flex items-center space-x-4 pt-4">
                                    <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded-lg font-medium transition">
                                        Update Content
                                    </button>
                                    <a href="{{ route('admin.cms.index') }}" class="text-gray-600 hover:text-gray-800 px-6 py-2">
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
        function toggleFields() {
            const type = document.getElementById('type').value;
            const valueField = document.getElementById('valueField');
            const imageField = document.getElementById('imageField');
            const valueInput = document.getElementById('value');

            if (type === 'image') {
                valueField.style.display = 'none';
                imageField.style.display = 'block';
                valueInput.removeAttribute('required');
            } else {
                valueField.style.display = 'block';
                imageField.style.display = 'none';
                
                // Change textarea rows based on type
                if (type === 'textarea') {
                    valueInput.rows = 5;
                } else {
                    valueInput.rows = 3;
                }
            }
        }

        // Initialize on page load
        toggleFields();
    </script>
</body>
</html>
