<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Detail - Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <div class="min-h-screen">
        @include('admin.partials.sidebar')

        <div class="ml-64">
            @include('admin.partials.header', ['title' => 'Booking Detail'])

            <main class="p-8">
                <!-- Header -->
                <div class="mb-8 flex justify-between items-center">
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('admin.bookings.index') }}" class="text-purple-600 hover:text-purple-700 font-medium flex items-center">
                            <x-heroicon-o-arrow-left class="h-5 w-5 mr-2" />
                            Back to Bookings
                        </a>
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">Booking Detail</h1>
                            <p class="text-gray-600 mt-1">Code: <span class="font-mono font-semibold">{{ $booking->book_code }}</span></p>
                        </div>
                    </div>
                    <span class="px-4 py-2 inline-block text-sm font-semibold rounded
                        @if($booking->status === 'pending') bg-yellow-100 text-yellow-800
                        @elseif($booking->status === 'confirmed') bg-blue-100 text-blue-800
                        @elseif($booking->status === 'active') bg-green-100 text-green-800
                        @elseif($booking->status === 'completed') bg-gray-100 text-gray-800
                        @else bg-red-100 text-red-800
                        @endif">
                        {{ ucfirst($booking->status) }}
                    </span>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Main Content (2 cols) -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Customer Info -->
                        <div class="bg-white rounded-lg shadow p-6">
                            <h2 class="text-lg font-bold text-gray-900 mb-4">Customer Information</h2>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-600">Name</p>
                                    <p class="font-semibold text-gray-900">{{ $booking->booker_name }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Phone</p>
                                    <p class="font-semibold text-gray-900">{{ $booking->booker_telp }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Email</p>
                                    <p class="font-semibold text-gray-900">{{ $booking->booker_email }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">User Account</p>
                                    <p class="font-semibold text-gray-900">{{ $booking->user->name ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Product Info -->
                        <div class="bg-white rounded-lg shadow p-6">
                            <h2 class="text-lg font-bold text-gray-900 mb-4">Product Details</h2>
                            <div class="flex space-x-4">
                                @if($booking->product && $booking->product->image)
                                    <img src="{{ asset('storage/' . $booking->product->image) }}" alt="{{ $booking->product->name }}" class="w-24 h-24 rounded-lg object-cover">
                                @else
                                    <div class="w-24 h-24 rounded-lg bg-gray-200 flex items-center justify-center">
                                        <x-heroicon-o-photo class="h-8 w-8 text-gray-400" />
                                    </div>
                                @endif
                                <div class="flex-1">
                                    <p class="text-sm text-gray-600">Product</p>
                                    <h3 class="font-bold text-gray-900 text-lg">{{ $booking->product->name ?? 'Product Deleted' }}</h3>
                                    <p class="text-sm text-gray-700 mt-2">{{ $booking->product->description ?? '-' }}</p>
                                    <div class="mt-3 flex space-x-4">
                                        <div>
                                            <p class="text-xs text-gray-600">Category</p>
                                            <p class="font-semibold">{{ $booking->product->category->name ?? '-' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-600">Brand</p>
                                            <p class="font-semibold">{{ $booking->product->brand->name ?? '-' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Rental Period -->
                        <div class="bg-white rounded-lg shadow p-6">
                            <h2 class="text-lg font-bold text-gray-900 mb-4">Rental Period</h2>
                            <div class="grid grid-cols-3 gap-4">
                                <div>
                                    <p class="text-sm text-gray-600">Check-in</p>
                                    <p class="font-semibold text-gray-900 text-lg">{{ $booking->checkin_appointment_start->format('M d, Y') }}</p>
                                    <p class="text-xs text-gray-500">{{ $booking->checkin_appointment_start->format('H:i') }}</p>
                                </div>
                                <div class="flex items-center justify-center pt-6">
                                    <x-heroicon-o-arrow-right class="h-6 w-6 text-gray-400" />
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Check-out</p>
                                    <p class="font-semibold text-gray-900 text-lg">{{ $booking->checkout_appointment_end->format('M d, Y') }}</p>
                                    <p class="text-xs text-gray-500">{{ $booking->checkout_appointment_end->format('H:i') }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Financial Summary -->
                        <div class="bg-white rounded-lg shadow p-6">
                            <h2 class="text-lg font-bold text-gray-900 mb-4">Financial Summary</h2>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Amount</span>
                                    <span class="font-semibold">{{ $booking->amount }} pcs</span>
                                </div>
                                <div class="border-t pt-3 flex justify-between">
                                    <span class="font-bold text-gray-900">Total Cost</span>
                                    <span class="font-bold text-lg text-purple-600">Rp {{ number_format($booking->amount * 100000, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Timeline -->
                        <div class="bg-white rounded-lg shadow p-6">
                            <h2 class="text-lg font-bold text-gray-900 mb-4">Timeline</h2>
                            <div class="space-y-3 text-sm">
                                <div class="flex">
                                    <div class="w-32 text-gray-600">Created:</div>
                                    <div class="font-semibold">{{ $booking->created_at->format('M d, Y H:i') }}</div>
                                </div>
                                <div class="flex">
                                    <div class="w-32 text-gray-600">Last Updated:</div>
                                    <div class="font-semibold">{{ $booking->updated_at->format('M d, Y H:i') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar (1 col) -->
                    <div class="space-y-6">
                        <!-- Quick Info -->
                        <div class="bg-white rounded-lg shadow p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">Details</h3>
                            <div class="space-y-3 text-sm">
                                <div>
                                    <p class="text-gray-600">Book Code</p>
                                    <p class="font-semibold font-mono text-gray-900">{{ $booking->book_code }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-600">Status</p>
                                    <span class="px-2 py-1 inline-block text-xs font-semibold rounded
                                        @if($booking->status === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($booking->status === 'confirmed') bg-blue-100 text-blue-800
                                        @elseif($booking->status === 'active') bg-green-100 text-green-800
                                        @elseif($booking->status === 'completed') bg-gray-100 text-gray-800
                                        @else bg-red-100 text-red-800
                                        @endif">
                                        {{ ucfirst($booking->status) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="bg-white rounded-lg shadow p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">Actions</h3>
                            <div class="space-y-2">
                                <button onclick="openEditModal()" class="w-full bg-purple-600 hover:bg-purple-700 text-white font-medium py-2 px-3 rounded transition">
                                    ✏️ Edit
                                </button>
                                <form action="{{ route('admin.bookings.destroy', $booking->id) }}" method="POST" class="w-full" id="delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="w-full bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-3 rounded transition" onclick="confirmDeleteBooking()">
                                        🗑️ Delete
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Status Info -->
                        <div class="bg-blue-50 rounded-lg border border-blue-200 p-4 text-sm text-blue-800">
                            <p class="font-semibold mb-2">📋 Status Reference:</p>
                            <ul class="space-y-1 text-xs">
                                <li><strong>Pending:</strong> Booking created, awaiting payment</li>
                                <li><strong>Confirmed:</strong> Payment received, approved</li>
                                <li><strong>Active:</strong> Rental period is ongoing</li>
                                <li><strong>Completed:</strong> Rental finished, returned</li>
                                <li><strong>Cancelled:</strong> Booking cancelled</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Edit Modal -->
    <div id="editModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4 max-h-96 overflow-y-auto">
            <h3 class="text-xl font-bold text-gray-900 mb-4">Edit Status</h3>
            <form id="editForm">
                <div class="space-y-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select id="editStatus" name="status" required class="w-full border rounded p-2 text-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent" onchange="showAdminStatusInfo()">
                            <option value="">-- Select Status --</option>
                            <option value="pending">Pending</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="active">Active</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <!-- Status Info Box -->
                    <div id="statusInfo" class="bg-blue-50 border border-blue-200 rounded p-3 text-sm text-blue-800 hidden">
                        <p class="font-semibold mb-1" id="statusTitle"></p>
                        <p id="statusDescription" class="text-xs leading-relaxed"></p>
                    </div>
                </div>
                <div class="flex space-x-2">
                    <button type="button" onclick="closeEditModal()" 
                        class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-900 font-medium py-2 rounded transition">
                        Cancel
                    </button>
                    <button type="button" onclick="submitEdit()" 
                        class="flex-1 bg-purple-500 hover:bg-purple-600 text-white font-medium py-2 rounded transition">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openEditModal() {
            document.getElementById('editStatus').value = '{{ $booking->status }}';
            document.getElementById('editModal').classList.remove('hidden');
            showAdminStatusInfo();
        }

        function showAdminStatusInfo() {
            const select = document.getElementById('editStatus');
            const value = select.value;
            const infoBox = document.getElementById('statusInfo');
            const titleEl = document.getElementById('statusTitle');
            const descEl = document.getElementById('statusDescription');

            const statusDescriptions = {
                'pending': { title: '⏳ Pending', desc: 'Booking menunggu untuk diproses dan dikonfirmasi' },
                'confirmed': { title: '✅ Confirmed', desc: 'Booking sudah dikonfirmasi dan siap untuk diproses lebih lanjut' },
                'active': { title: '🔄 Active', desc: 'Proses booking sedang berjalan, periode sewa mulai' },
                'completed': { title: '🎉 Completed', desc: 'Proses booking selesai dengan sukses, semua transaksi selesai' },
                'cancelled': { title: '❌ Cancelled', desc: 'Booking dibatalkan, tidak ada transaksi lebih lanjut' }
            };

            if (value && statusDescriptions[value]) {
                const desc = statusDescriptions[value];
                titleEl.textContent = desc.title;
                descEl.textContent = desc.desc;
                infoBox.classList.remove('hidden');
            } else {
                infoBox.classList.add('hidden');
            }
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
            document.getElementById('editForm').reset();
        }

        function submitEdit() {
            const statusValue = document.getElementById('editStatus').value;
            console.log('Status value:', statusValue);
            
            if (!statusValue) {
                Swal.fire('Error!', 'Status is required', 'error');
                return;
            }

            const data = {
                status: statusValue
            };
            
            console.log('Sending data:', data);
            console.log('URL:', '{{ route("admin.booking-update.store", $booking->id) }}');

            fetch('{{ route("admin.booking-update.store", $booking->id) }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => {
                console.log('Response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                if (data.success) {
                    Swal.fire('Success!', data.message, 'success')
                        .then(() => location.reload());
                } else {
                    Swal.fire('Error!', data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                Swal.fire('Error!', 'An error occurred: ' + error.message, 'error');
            });
        }

        function confirmDeleteBooking() {
            Swal.fire({
                title: 'Delete Booking?',
                text: 'This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, Delete It!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form').submit();
                }
            });
        }
    </script>
</body>
</html>
