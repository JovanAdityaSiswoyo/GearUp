<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Unit - Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <div class="min-h-screen">
        @include('admin.partials.sidebar')

        <div class="ml-64">
            @include('admin.partials.header', ['title' => 'Edit Unit'])

            <main class="p-8">
                <div class="max-w-3xl mx-auto">
                    <div class="mb-6">
                        <a href="{{ route('admin.units.index') }}" class="text-purple-600 hover:text-purple-800 flex items-center space-x-2">
                            <x-heroicon-o-arrow-left class="h-5 w-5" />
                            <span>Back to Units</span>
                        </a>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm p-8">
                        <h2 class="text-2xl font-bold text-gray-800 mb-6">Edit Unit Information</h2>

                        <form action="{{ route('admin.units.update', $unit->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="space-y-6">
                                <!-- Product Selection -->
                                <div>
                                    <label for="id_product" class="block text-sm font-medium text-gray-700 mb-2">
                                        Product <span class="text-red-500">*</span>
                                    </label>
                                    <select id="id_product" name="id_product" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('id_product') border-red-500 @enderror">
                                        <option value="">Select a product</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}" {{ (old('id_product', $unit->id_product) == $product->id) ? 'selected' : '' }}>
                                                {{ $product->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('id_product')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Serial Number -->
                                <div>
                                    <label for="serial_number" class="block text-sm font-medium text-gray-700 mb-2">
                                        Serial Number <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" id="serial_number" name="serial_number" value="{{ old('serial_number', $unit->serial_number) }}" required 
                                           placeholder="e.g., CAM-2024-0001"
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('serial_number') border-red-500 @enderror">
                                    @error('serial_number')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-1 text-sm text-gray-500">Enter a unique serial number for this unit</p>
                                </div>

                                <!-- Status -->
                                <div>
                                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                        Status <span class="text-red-500">*</span>
                                    </label>
                                    <select id="status" name="status" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('status') border-red-500 @enderror">
                                        <option value="available" {{ old('status', $unit->status) == 'available' ? 'selected' : '' }}>Available</option>
                                        <option value="maintenance" {{ old('status', $unit->status) == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                        <option value="in_inspection" {{ old('status', $unit->status) == 'in_inspection' ? 'selected' : '' }}>In Inspection</option>
                                        <option value="booked" {{ old('status', $unit->status) == 'booked' ? 'selected' : '' }}>Booked</option>
                                        <option value="deployed" {{ old('status', $unit->status) == 'deployed' ? 'selected' : '' }}>Deployed</option>
                                        <option value="returning" {{ old('status', $unit->status) == 'returning' ? 'selected' : '' }}>Returning</option>
                                        <option value="lost_scrapped" {{ old('status', $unit->status) == 'lost_scrapped' ? 'selected' : '' }}>Lost/Scrapped</option>
                                    </select>
                                    @error('status')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Last Maintenance Date -->
                                <div>
                                    <label for="last_maintenance_at" class="block text-sm font-medium text-gray-700 mb-2">
                                        Last Maintenance Date
                                    </label>
                                    <input type="date" id="last_maintenance_at" name="last_maintenance_at" 
                                           value="{{ old('last_maintenance_at', $unit->last_maintenance_at ? $unit->last_maintenance_at->format('Y-m-d') : '') }}"
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('last_maintenance_at') border-red-500 @enderror">
                                    @error('last_maintenance_at')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Notes -->
                                <div>
                                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                                        Notes
                                    </label>
                                    <textarea id="notes" name="notes" rows="4"
                                              placeholder="Add any notes about this unit's condition, maintenance history, etc."
                                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('notes') border-red-500 @enderror">{{ old('notes', $unit->notes) }}</textarea>
                                    @error('notes')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Unit History -->
                                <div class="border-t pt-6">
                                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Unit History</h3>
                                    <div class="grid grid-cols-2 gap-4 text-sm">
                                        <div>
                                            <span class="text-gray-600">Created:</span>
                                            <span class="font-medium text-gray-900">{{ $unit->created_at->format('M d, Y H:i') }}</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-600">Last Updated:</span>
                                            <span class="font-medium text-gray-900">{{ $unit->updated_at->format('M d, Y H:i') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end space-x-4 mt-8">
                                <a href="{{ route('admin.units.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                                    Cancel
                                </a>
                                <button type="submit" class="px-6 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition">
                                    Update Unit
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
