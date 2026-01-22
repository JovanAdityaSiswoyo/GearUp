<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Packages Management - Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-50">
    @include('sweetalert::alert')
    <div class="min-h-screen">
        @include('admin.partials.sidebar')

        <div class="ml-64">
            @include('admin.partials.header', ['title' => 'Packages Management'])

            <main class="p-8">
                <div class="mb-6 flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">Manage Packages</h2>
                        <p class="text-gray-600">Add, edit, or remove packages</p>
                    </div>
                    <a href="{{ route('admin.packages.create') }}" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg flex items-center space-x-2 transition">
                        <x-heroicon-o-plus class="h-5 w-5" />
                        <span>Add Package</span>
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($packages as $package)
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-md transition">
                        @if($package->image)
                        <div class="h-48 overflow-hidden">
                            <img src="{{ asset('storage/' . $package->image) }}" alt="{{ $package->name }}" class="w-full h-full object-cover">
                        </div>
                        @else
                        <div class="h-48 bg-gradient-to-br from-purple-400 to-pink-400 flex items-center justify-center">
                            <x-heroicon-o-cube-transparent class="h-16 w-16 text-white opacity-50" />
                        </div>
                        @endif
                        
                        <div class="p-6">
                            <div class="flex items-start justify-between mb-3">
                                <h3 class="text-lg font-semibold text-gray-800 flex-1">{{ $package->name_package ?? $package->name }}</h3>
                                <div class="flex space-x-2">
                                    <button onclick="showDetail('{{ $package->name_package ?? $package->name }}', '{{ addslashes($package->description ?? 'No description') }}', '{{ $package->price }}', '{{ $package->duration_days }}', '{{ implode(', ', $package->products->pluck('name')->toArray()) }}', '{{ $package->image ? asset('storage/' . $package->image) : '' }}')" class="text-blue-600 hover:text-blue-900">
                                        <x-heroicon-o-eye class="h-5 w-5" />
                                    </button>
                                    <a href="{{ route('admin.packages.edit', $package->id) }}" class="text-purple-600 hover:text-purple-900">
                                        <x-heroicon-o-pencil class="h-5 w-5" />
                                    </a>
                                    <form action="{{ route('admin.packages.destroy', $package->id) }}" method="POST" class="inline" id="delete-form-{{ $package->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="text-red-600 hover:text-red-900" onclick='confirmDelete("{{ $package->id }}")'>
                                            <x-heroicon-o-trash class="h-5 w-5" />
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <p class="text-sm text-gray-600 mb-4">{{ Str::limit($package->description ?? 'No description', 80) }}</p>
                            <div class="mb-3">
                                <p class="text-xs text-gray-500 mb-1">Included Products:</p>
                                <div class="flex flex-wrap gap-1">
                                    @forelse($package->products as $product)
                                        <span class="px-2 py-1 text-xs bg-purple-100 text-purple-700 rounded">{{ $product->name }}</span>
                                    @empty
                                    <span class="text-xs text-gray-400">No products</span>
                                @endforelse
                            </div>
                        </div>
                        <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                            <div class="text-2xl font-bold text-purple-600">
                                Rp {{ number_format($package->price, 0, ',', '.') }}
                            </div>
                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                {{ $package->duration_days }} days
                            </span>
                        </div>
                        @if($package->discount > 0)
                        <div class="mt-2 text-sm text-green-600 font-medium">
                            {{ $package->discount }}% discount
                        </div>
                        @endif
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="mt-6">
                    {{ $packages->links() }}
                </div>
            </main>
        </div>
    </div>

    <!-- Detail Modal -->
    <div id="detailModal" class="fixed inset-0 overflow-y-auto h-full w-full z-50" style="display: none;">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-2xl rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Package Details</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="mt-2">
                <div id="modal-image-container" class="mb-4">
                    <img id="modal-image" src="" alt="Package Image" class="w-full h-48 object-cover rounded-lg">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Package Name</label>
                    <p id="modal-name" class="mt-1 text-sm text-gray-900"></p>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Description</label>
                    <p id="modal-description" class="mt-1 text-sm text-gray-900"></p>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Price</label>
                    <p id="modal-price" class="mt-1 text-sm text-gray-900"></p>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Duration</label>
                    <p id="modal-duration" class="mt-1 text-sm text-gray-900"></p>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Products</label>
                    <p id="modal-products" class="mt-1 text-sm text-gray-900"></p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showDetail(name, description, price, duration, products, image) {
            document.getElementById('modal-name').textContent = name;
            document.getElementById('modal-description').textContent = description;
            document.getElementById('modal-price').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(price);
            document.getElementById('modal-duration').textContent = duration + ' days';
            document.getElementById('modal-products').textContent = products || 'No products';
            
            const imageContainer = document.getElementById('modal-image-container');
            const imageElement = document.getElementById('modal-image');
            if (image) {
                imageElement.src = image;
                imageContainer.style.display = 'block';
            } else {
                imageContainer.style.display = 'none';
            }
            
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
