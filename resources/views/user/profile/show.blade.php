<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya - GearUp</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex flex-col">
        <!-- Header/Navbar dari Home -->
        <nav class="px-6 lg:px-16 py-4 flex items-center justify-between border-b bg-white shadow-sm">
            <!-- Logo -->
            <a href="{{ route('home') }}" class="flex items-center space-x-3 hover:opacity-80 transition">
                <img src="/gallery/GearUpLogo.png" alt="GearUp Logo" class="h-12 w-auto">
                <span class="text-gray-800 font-bold text-xl hidden lg:block">GearUp</span>
            </a>

            <!-- Contact Info (Desktop) -->
            <div class="hidden lg:flex items-center space-x-6 text-gray-600 text-sm">
                <div class="flex items-center space-x-2">
                    <x-heroicon-o-phone class="h-5 w-5" />
                    <span>0877 7603 4179</span>
                </div>
                <div class="flex items-center space-x-2">
                    <x-heroicon-o-envelope class="h-5 w-5" />
                    <span>gearup@gmail.com</span>
                </div>
            </div>

            <!-- Right Actions -->
            <div class="flex items-center space-x-4">
                <div class="flex items-center space-x-3">
                    @if (auth()->user()->profile_photo)
                        <img 
                            src="{{ asset('storage/' . auth()->user()->profile_photo) }}" 
                            alt="{{ auth()->user()->name }}"
                            class="w-10 h-10 rounded-full object-cover border-2 border-green-500"
                        >
                    @else
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-green-600 to-teal-500 flex items-center justify-center text-white font-semibold text-sm border-2 border-green-500">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                    @endif
                    <span class="text-gray-800 text-sm hidden sm:block">{{ auth()->user()->name }}</span>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-full font-semibold transition flex items-center space-x-2">
                        <span>{{ __('profile.logout') }}</span>
                        <x-heroicon-o-arrow-right-on-rectangle class="h-4 w-4" />
                    </button>
                </form>
            </div>
        </nav>

        <!-- Main Content Area -->
        <div class="flex flex-1">
            <!-- Mini Sidebar -->
            <aside class="w-64 bg-white border-r border-gray-200 px-6 py-8 shadow-sm">
                <div class="space-y-2">
                    <!-- Setting Menu -->
                    <a href="{{ route('profile.show') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg bg-green-50 border-l-4 border-green-600 text-green-700 font-semibold hover:bg-green-100 transition">
                        <x-heroicon-o-user class="h-5 w-5" />
                        <span>{{ __('profile.my_profile') }}</span>
                    </a>
                    
                    <!-- My Booking Menu -->
                    <a href="{{ route('user.my-booking') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-100 transition">
                        <x-heroicon-o-calendar class="h-5 w-5" />
                        <span>{{ __('profile.my_booking') }}</span>
                    </a>
                    
                    <!-- Setting Menu -->
                    <a href="#settings" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-100 transition">
                        <x-heroicon-o-cog-6-tooth class="h-5 w-5" />
                        <span>{{ __('profile.settings') }}</span>
                    </a>
                </div>

                <!-- Language Switcher -->
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <h4 class="text-sm font-semibold text-gray-700 mb-4 flex items-center space-x-2">
                        <x-heroicon-o-language class="h-5 w-5" />
                        <span>{{ __('profile.language_setting') }}</span>
                    </h4>
                    <div class="flex space-x-2">
                        <form action="{{ route('profile.switch-language') }}" method="POST" class="flex-1">
                            @csrf
                            <input type="hidden" name="language" value="id">
                            <button type="submit" class="w-full px-3 py-2 text-sm rounded-lg font-medium transition {{ app()->getLocale() == 'id' ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                                🇮🇩 ID
                            </button>
                        </form>
                        <form action="{{ route('profile.switch-language') }}" method="POST" class="flex-1">
                            @csrf
                            <input type="hidden" name="language" value="en">
                            <button type="submit" class="w-full px-3 py-2 text-sm rounded-lg font-medium transition {{ app()->getLocale() == 'en' ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                                🇬🇧 EN
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Account Stats -->
                <div class="mt-8 pt-6 border-t border-gray-200 space-y-4">
                    <div class="text-sm">
                        <p class="text-gray-500">{{ __('profile.account_status') }}</p>
                        <p class="font-semibold text-green-600 flex items-center space-x-1">
                            <x-heroicon-o-check-circle class="h-4 w-4" />
                            <span>{{ __('profile.active') }}</span>
                        </p>
                    </div>
                    <div class="text-sm">
                        <p class="text-gray-500">{{ __('profile.total_booking') }}</p>
                        <p class="font-semibold text-gray-800">12 {{ __('profile.orders') }}</p>
                    </div>
                </div>
            </aside>

            <!-- Main Content -->
            <main class="flex-1 px-8 py-8">
            <!-- Success Message -->
            @if ($message = Session::get('success'))
                <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center space-x-2">
                    <x-heroicon-o-check-circle class="h-5 w-5" />
                    <span>{{ $message }}</span>
                </div>
            @endif

                <!-- Success Message -->
                @if ($message = Session::get('success'))
                    <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center space-x-2">
                        <x-heroicon-o-check-circle class="h-5 w-5" />
                        <span>{{ $message }}</span>
                    </div>
                @endif

                <!-- Profile Card -->
                <div class="bg-white rounded-xl shadow-sm p-8 mb-8 max-w-2xl">
                <!-- Profile Photo Section -->
                <div class="flex flex-col items-center mb-8 pb-8 border-b">
                    <div class="relative mb-4">
                        @if ($user->profile_photo)
                            <img 
                                src="{{ asset('storage/' . $user->profile_photo) }}" 
                                alt="{{ $user->name }}"
                                class="w-32 h-32 rounded-full object-cover border-4 border-green-500 shadow-lg"
                            >
                        @else
                            <div class="w-32 h-32 rounded-full bg-gradient-to-br from-green-600 to-teal-500 flex items-center justify-center border-4 border-green-500 shadow-lg">
                                <span class="text-white text-4xl font-bold">{{ substr($user->name, 0, 1) }}</span>
                            </div>
                        @endif
                        <button 
                            type="button"
                            onclick="document.getElementById('photo-input').click()"
                            class="absolute bottom-0 right-0 bg-green-600 hover:bg-green-700 text-white p-3 rounded-full shadow-lg transition"
                        >
                            <x-heroicon-o-camera class="h-5 w-5" />
                        </button>
                    </div>

                    <!-- Hidden File Input -->
                    <form id="photo-form" action="{{ route('profile.update-photo') }}" method="POST" enctype="multipart/form-data" class="hidden">
                        @csrf
                        <input 
                            id="photo-input"
                            type="file" 
                            name="profile_photo" 
                            accept="image/*"
                            onchange="document.getElementById('photo-form').submit()"
                        >
                    </form>

                    <h2 class="text-2xl font-bold text-gray-800 text-center mt-4">{{ $user->name }}</h2>
                    <p class="text-gray-500 text-center">{{ $user->email }}</p>
                </div>

                <!-- Profile Info -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('profile.full_name') }}</label>
                        <p class="text-lg text-gray-800">{{ $user->name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('profile.email') }}</label>
                        <p class="text-lg text-gray-800">{{ $user->email }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('profile.phone_number') }}</label>
                        <p class="text-lg text-gray-800">{{ $user->phone ?? __('profile.not_filled') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('profile.verification_status') }}</label>
                        <p class="text-lg">
                            @if ($user->email_verified_at)
                                <span class="text-green-600 flex items-center space-x-1">
                                    <x-heroicon-o-check-circle class="h-5 w-5" />
                                    <span>{{ __('profile.verified') }}</span>
                                </span>
                            @else
                                <span class="text-yellow-600 flex items-center space-x-1">
                                    <x-heroicon-o-clock class="h-5 w-5" />
                                    <span>{{ __('profile.not_verified') }}</span>
                                </span>
                            @endif
                        </p>
                    </div>
                </div>
                </div>

                <!-- Edit Profile Card -->
                <div class="bg-white rounded-xl shadow-sm p-8 max-w-2xl">
                <h3 class="text-xl font-bold text-gray-800 mb-6">{{ __('profile.edit_profile') }}</h3>

                <form action="{{ route('profile.update') }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Nama -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">{{ __('profile.full_name') }}</label>
                        <input 
                            type="text" 
                            id="name"
                            name="name"
                            value="{{ old('name', $user->name) }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition"
                        >
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">{{ __('profile.email') }}</label>
                        <input 
                            type="email" 
                            id="email"
                            name="email"
                            value="{{ old('email', $user->email) }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition"
                        >
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nomor Telepon -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">{{ __('profile.phone_number') }}</label>
                        <input 
                            type="tel" 
                            id="phone"
                            name="phone"
                            value="{{ old('phone', $user->phone) }}"
                            placeholder="{{ __('profile.placeholder_phone') }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition"
                        >
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <button 
                        type="submit"
                        class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 rounded-lg transition flex items-center justify-center space-x-2"
                    >
                        <x-heroicon-o-check class="h-5 w-5" />
                        <span>{{ __('profile.save_changes') }}</span>
                    </button>
                </form>
            </div>

            <!-- Account Info Card -->
            <div class="bg-white rounded-xl shadow-sm p-8 mt-8">
                <h3 class="text-xl font-bold text-gray-800 mb-4">{{ __('profile.account_info') }}</h3>
                <div class="space-y-3 text-sm text-gray-600">
                    <p><strong>{{ __('profile.user_id') }}:</strong> {{ $user->id }}</p>
                    <p><strong>{{ __('profile.joined') }}:</strong> {{ $user->created_at->format('d M Y H:i') }}</p>
                    <p><strong>{{ __('profile.last_updated') }}:</strong> {{ $user->updated_at->format('d M Y H:i') }}</p>
                </div>
            </div>
        </main>
    </div>

    <!-- Footer -->
    <footer class="bg-black text-white py-16 px-6 lg:px-16 relative mt-16">
        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-16">
                <!-- Logo and Company Info -->
                <div>
                    <img src="/gallery/Gearup.png" alt="GearUp Logo" class="h-40 w-auto mb-6">
                    
                    <div class="space-y-4 text-gray-400 text-sm">
                        <div class="flex items-start space-x-2">
                            <x-heroicon-o-map-pin class="h-5 w-5 flex-shrink-0 mt-0.5" />
                            <p>Jl. H. Jian No.38, RT.9/RW.3, Cipete Utara,<br>Kec. Kby. Baru, Kota Jakarta Selatan,<br>Daerah Khusus Ibukota Jakarta 12150</p>
                        </div>
                        
                        <div class="flex items-center space-x-2">
                            <x-heroicon-o-clock class="h-5 w-5 flex-shrink-0" />
                            <p>Setiap hari, 09:00 - 20:00 WIB</p>
                        </div>
                        
                        <div class="flex items-center space-x-2">
                            <x-heroicon-o-envelope class="h-5 w-5 flex-shrink-0" />
                            <p>gearup@gmail.com</p>
                        </div>
                    </div>
                </div>

                <!-- Informasi -->
                <div>
                    <h3 class="text-xl font-semibold mb-6">Informasi</h3>
                    <ul class="space-y-3 text-gray-400">
                        <li><a href="#" class="hover:text-white transition">Cara sewa</a></li>
                        <li><a href="#" class="hover:text-white transition">Cara jadi member</a></li>
                        <li><a href="#" class="hover:text-white transition">Cara pengembalian</a></li>
                        <li><a href="#" class="hover:text-white transition">Syarat dan Ketentuan</a></li>
                    </ul>
                </div>

                <!-- Tentang GearUp -->
                <div>
                    <h3 class="text-xl font-semibold mb-6">Tentang GearUp</h3>
                    <ul class="space-y-3 text-gray-400">
                        <li><a href="#" class="hover:text-white transition">Tentang Kami</a></li>
                    </ul>
                </div>

                <!-- Layanan Bantuan -->
                <div>
                    <h3 class="text-xl font-semibold mb-6">Layanan Bantuan</h3>
                    <div class="text-gray-400 mb-4">
                        <p>Kontak Kami</p>
                    </div>
                    <div class="flex items-center space-x-2 mb-2">
                        <x-heroicon-o-phone class="h-5 w-5 text-gray-400" />
                        <span class="text-sm text-gray-400">WhatsApp Kami</span>
                    </div>
                    <a href="https://wa.me/6287812000155" class="text-green-500 text-2xl font-bold hover:text-green-400 transition">0878 1200 0155</a>
                </div>
            </div>

            <!-- Bottom Bar -->
            <div class="border-t border-gray-800 pt-8">
                <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                    <p class="text-gray-400 text-sm">&copy; 2026 GearUp. All rights reserved</p>
                    
                    <div class="flex items-center space-x-4">
                        <span class="text-gray-400 text-sm">Follow us</span>
                        <a href="#" class="text-gray-400 hover:text-white transition">
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition">
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition">
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Loading Spinner (optional) -->
    <style>
        .loading {
            display: none;
        }
    </style>
</body>
</html>
