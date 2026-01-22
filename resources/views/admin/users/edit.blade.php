<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User - Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    @include('sweetalert::alert')
    <div class="min-h-screen">
        @include('admin.partials.sidebar')

        <div class="ml-64">
            @include('admin.partials.header', ['title' => 'Edit User'])

            <main class="p-8">
                <div class="max-w-2xl mx-auto">
                    <div class="bg-white rounded-xl shadow-sm p-8">
                        <h2 class="text-2xl font-bold text-gray-800 mb-6">Edit User</h2>

                        <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="space-y-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Name *</label>
                                    <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" required>
                                    @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                                    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" required>
                                    @error('email') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Password (Leave blank to keep current)</label>
                                    <input type="password" name="password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                    @error('password') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                </div>

                                <div class="flex items-center space-x-4 pt-4">
                                    <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded-lg transition">
                                        Update User
                                    </button>
                                    <a href="{{ route('admin.users.index') }}" class="text-gray-600 hover:text-gray-800 px-6 py-2">
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
