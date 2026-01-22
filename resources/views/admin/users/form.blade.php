<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ isset($user) ? 'Edit' : 'Create' }} User - Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex">
        @include('admin.partials.sidebar')

        <div class="flex-1">
            @include('admin.partials.header', ['title' => isset($user) ? 'Edit User' : 'Create User'])

            <main class="p-8">
                <div class="max-w-2xl mx-auto">
                    <div class="bg-white rounded-xl shadow-sm p-8">
                        <h2 class="text-2xl font-bold text-gray-800 mb-6">{{ isset($user) ? 'Edit User' : 'Add New User' }}</h2>

                        <form action="{{ isset($user) ? route('admin.users.update', $user->id) : route('admin.users.store') }}" method="POST">
                            @csrf
                            @if(isset($user))
                                @method('PUT')
                            @endif

                            <div class="space-y-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                                    <input type="text" name="name" value="{{ old('name', $user->name ?? '') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" required>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                    <input type="email" name="email" value="{{ old('email', $user->email ?? '') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" required>
                                </div>

                                @if(!isset($user))
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                                    <input type="password" name="password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" required>
                                </div>
                                @endif

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                                    <input type="text" name="phone" value="{{ old('phone', $user->phone ?? '') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                </div>

                                <div class="flex items-center space-x-4 pt-4">
                                    <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded-lg transition">
                                        {{ isset($user) ? 'Update' : 'Create' }} User
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
