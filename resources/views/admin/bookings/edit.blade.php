<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Booking - Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    @include('sweetalert::alert')
    <div class="min-h-screen">
        @include('admin.partials.sidebar')

        <div class="ml-64">
            @include('admin.partials.header', ['title' => 'Edit Booking'])

            <main class="p-8">
                <div class="max-w-3xl mx-auto">
                    <div class="bg-white rounded-xl shadow-sm p-8">
                        <h2 class="text-2xl font-bold text-gray-800 mb-6">Edit Booking: {{ $booking->book_code }}</h2>

                        <form action="{{ route('admin.bookings.update', $booking->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">User *</label>
                                    <select name="id_user" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500" required>
                                        @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ $booking->id_user == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ $user->email }})
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('id_user') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Product *</label>
                                    <select name="id_product" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500" required>
                                        @foreach($products as $product)
                                        <option value="{{ $product->id }}" {{ $booking->id_product == $product->id ? 'selected' : '' }}>
                                            {{ $product->name }} - Stock: {{ $product->stock }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('id_product') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Check-in Date *</label>
                                    <input type="datetime-local" name="checkin_appointment_start" value="{{ $booking->checkin_appointment_start->format('Y-m-d\TH:i') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500" required>
                                    @error('checkin_appointment_start') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Check-out Date *</label>
                                    <input type="datetime-local" name="checkout_appointment_end" value="{{ $booking->checkout_appointment_end->format('Y-m-d\TH:i') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500" required>
                                    @error('checkout_appointment_end') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Amount *</label>
                                    <input type="number" name="amount" value="{{ $booking->amount }}" min="1" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500" required>
                                    @error('amount') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                                    <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500" required>
                                        <option value="pending" {{ $booking->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="confirmed" {{ $booking->status == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                        <option value="active" {{ $booking->status == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="completed" {{ $booking->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="cancelled" {{ $booking->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                    @error('status') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Booker Name *</label>
                                    <input type="text" name="booker_name" value="{{ $booking->booker_name }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500" required>
                                    @error('booker_name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Booker Email *</label>
                                    <input type="email" name="booker_email" value="{{ $booking->booker_email }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500" required>
                                    @error('booker_email') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Booker Phone *</label>
                                    <input type="text" name="booker_telp" value="{{ $booking->booker_telp }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500" required>
                                    @error('booker_telp') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            <div class="flex items-center space-x-4 mt-8">
                                <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded-lg transition">
                                    Update Booking
                                </button>
                                <a href="{{ route('admin.bookings.index') }}" class="text-gray-600 hover:text-gray-800 px-6 py-2">
                                    Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
