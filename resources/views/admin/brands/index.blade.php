<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Brands - Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    @include('sweetalert::alert')
    <div class="min-h-screen">
        @include('admin.partials.sidebar')

        <div class="ml-64">
            @include('admin.partials.header', ['title' => 'Brands'])

            <main class="p-8">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">Manage Brands</h2>
                    <a href="{{ route('admin.brands.create') }}" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition">
                        + Create Brand
                    </a>
                </div>

                @if($brands->count())
                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <table class="w-full">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Name</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Description</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Logo</th>
                                <th class="px-6 py-3 text-center text-sm font-semibold text-gray-700">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($brands as $brand)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <span class="font-medium text-gray-900">{{ $brand->name }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-gray-600 text-sm">{{ Str::limit($brand->description, 50) }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($brand->logo)
                                        <img src="{{ asset('storage/' . $brand->logo) }}" alt="{{ $brand->name }}" class="h-10 w-10 object-cover rounded">
                                    @else
                                        <span class="text-gray-400 text-sm">No logo</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('admin.brands.edit', $brand->id) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                            Edit
                                        </a>
                                        <form action="{{ route('admin.brands.destroy', $brand->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $brands->links() }}
                </div>
                @else
                <div class="bg-white rounded-xl shadow-sm p-8 text-center">
                    <p class="text-gray-600 mb-4">No brands found</p>
                    <a href="{{ route('admin.brands.create') }}" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition inline-block">
                        Create First Brand
                    </a>
                </div>
                @endif
            </main>
        </div>
    </div>
</body>
</html>
