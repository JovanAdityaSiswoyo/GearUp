

<div class="min-h-screen flex">
    <!-- Left Side - Image -->
    <div class="hidden lg:block lg:w-1/2 relative">
        <img 
            src="https://images.unsplash.com/photo-1582268611958-ebfd161ef9cf?q=80&w=2070" 
            alt="Luxury Resort" 
            class="absolute inset-0 w-full h-full object-cover"
        >
        <div class="absolute inset-0 bg-gradient-to-r from-black/40 to-transparent"></div>
    </div>

    <!-- Right Side - Register Form -->
    <div class="w-full lg:w-1/2 flex items-center justify-center bg-white px-6 py-12 lg:px-16">
        <div class="w-full max-w-md">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold text-gray-900 mb-3 font-poppins">
                    Welcome To A World
                </h1>
                <h2 class="text-4xl font-bold text-gray-900 mb-4 font-poppins">
                    of Timeless Elegance
                </h2>
                <p class="text-gray-600 font-inter">
                    Enter your details below to register
                </p>
            </div>

            <!-- Register Form -->
            <form wire:submit.prevent="register" class="space-y-5">
                <!-- Name Input -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2 font-inter">
                        Full Name
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <x-icon name="o-user" class="w-5 h-5 text-gray-400" />
                        </div>
                        <input
                            type="text"
                            id="name"
                            wire:model="name"
                            placeholder="John Doe"
                            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all font-inter placeholder:text-gray-400"
                        />
                    </div>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600 font-inter">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email Input -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2 font-inter">
                        Email Address
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <x-icon name="o-envelope" class="w-5 h-5 text-gray-400" />
                        </div>
                        <input
                            type="email"
                            id="email"
                            wire:model="email"
                            placeholder="john@example.com"
                            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all font-inter placeholder:text-gray-400"
                        />
                    </div>
                    @error('email')
                        <p class="mt-1 text-sm text-red-600 font-inter">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Input -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2 font-inter">
                        Password
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <x-icon name="o-lock-closed" class="w-5 h-5 text-gray-400" />
                        </div>
                        <input
                            type="password"
                            id="password"
                            wire:model="password"
                            placeholder="Password"
                            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all font-inter placeholder:text-gray-400"
                        />
                    </div>
                    @error('password')
                        <p class="mt-1 text-sm text-red-600 font-inter">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirm Password Input -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2 font-inter">
                        Confirm Password
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <x-icon name="o-lock-closed" class="w-5 h-5 text-gray-400" />
                        </div>
                        <input
                            type="password"
                            id="password_confirmation"
                            wire:model="password_confirmation"
                            placeholder="Confirm Password"
                            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all font-inter placeholder:text-gray-400"
                        />
                    </div>
                    @error('password_confirmation')
                        <p class="mt-1 text-sm text-red-600 font-inter">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Terms Checkbox -->
                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input
                            id="terms"
                            type="checkbox"
                            wire:model="terms"
                            class="w-4 h-4 border border-gray-300 rounded bg-gray-50 focus:ring-2 focus:ring-blue-500"
                            required
                        />
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="terms" class="text-gray-700">
                            I agree to the <a href="#" class="text-blue-600 hover:text-blue-700 font-medium">Terms and Conditions</a>
                        </label>
                        @error('terms')
                            <p class="mt-1 text-sm text-red-600 font-inter">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Register Button -->
                <button 
                    type="submit"
                    class="w-full bg-gray-900 text-white py-3.5 rounded-lg font-semibold hover:bg-gray-800 transition-colors duration-200 font-poppins"
                >
                    Register
                </button>

                <!-- Login Link -->
                <p class="text-center text-gray-600 font-inter">
                    Already have an account? 
                    <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-700 font-semibold">
                        Login Here
                    </a>
                </p>

                <!-- Divider -->
                <div class="relative my-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-4 bg-white text-gray-500 font-inter">Or Continue With</span>
                    </div>
                </div>

                <!-- Social Login Buttons -->
                <div class="grid grid-cols-2 gap-4">
                    <!-- Google Button -->
                    <button 
                        type="button"
                        class="flex items-center justify-center px-4 py-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-200 font-inter"
                    >
                        <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24">
                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                        </svg>
                        Google
                    </button>

                    <!-- Facebook Button -->
                    <button 
                        type="button"
                        class="flex items-center justify-center px-4 py-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-200 font-inter"
                    >
                        <svg class="w-5 h-5 mr-2" fill="#1877F2" viewBox="0 0 24 24">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                        Facebook
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>