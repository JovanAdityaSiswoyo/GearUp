<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Content Management - Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-50">
    @include('sweetalert::alert')
    <div class="min-h-screen">
        @include('admin.partials.sidebar')

        <div class="ml-64">
            @include('admin.partials.header', ['title' => 'Content Management System'])

            <main class="p-8">
                <div class="mb-6 flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">Manage Website Content</h2>
                        <p class="text-gray-600">Edit homepage content, hero sections, and more</p>
                    </div>
                    <a href="{{ route('admin.cms.create') }}" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg flex items-center space-x-2 transition">
                        <x-heroicon-o-plus class="h-5 w-5" />
                        <span>Add Content</span>
                    </a>
                </div>

                <!-- Filter by Group -->
                <div class="mb-6 bg-white rounded-lg shadow-sm p-4">
                    <div class="flex items-center space-x-4">
                        <label class="text-sm font-medium text-gray-700">Filter by Group:</label>
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('admin.cms.index') }}" class="px-4 py-2 rounded-lg text-sm {{ !request('group') ? 'bg-purple-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }} transition">
                                All
                            </a>
                            @foreach($groups as $group)
                                <a href="{{ route('admin.cms.index', ['group' => $group]) }}" class="px-4 py-2 rounded-lg text-sm {{ request('group') === $group ? 'bg-purple-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }} transition">
                                    {{ ucfirst($group) }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Content Table -->
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Key</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Label</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Group</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Value</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($contents as $content)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900">{{ $content->key }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $content->label }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                            @if($content->type === 'image') bg-blue-100 text-blue-800
                                            @elseif($content->type === 'textarea') bg-green-100 text-green-800
                                            @elseif($content->type === 'url') bg-yellow-100 text-yellow-800
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            {{ $content->type }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded-full text-xs font-semibold">
                                            {{ ucfirst($content->group) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        @if($content->type === 'image')
                                            @if($content->value)
                                                <img src="{{ asset('storage/' . $content->value) }}" alt="{{ $content->label }}" class="h-10 w-10 object-cover rounded">
                                            @else
                                                <span class="text-gray-400">No image</span>
                                            @endif
                                        @else
                                            <div class="max-w-xs truncate">{{ $content->value ?: '-' }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $content->order }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end space-x-2">
                                            <a href="{{ route('admin.cms.edit', $content->id) }}" class="text-purple-600 hover:text-purple-900">
                                                <x-heroicon-o-pencil class="h-5 w-5" />
                                            </a>
                                            <form action="{{ route('admin.cms.destroy', $content->id) }}" method="POST" class="inline" id="delete-form-{{ $content->id }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="text-red-600 hover:text-red-900" onclick='confirmDelete("{{ $content->id }}")'>
                                                    <x-heroicon-o-trash class="h-5 w-5" />
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                        <x-heroicon-o-document-text class="h-12 w-12 mx-auto mb-3 text-gray-400" />
                                        <p>No content found. Start by adding your first content.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $contents->links() }}
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
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
