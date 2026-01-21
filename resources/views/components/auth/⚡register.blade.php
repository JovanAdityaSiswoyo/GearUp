<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

new class extends Component
{
    #[Validate('required|string|max:255')]
    public string $name = '';
    
    #[Validate('required|string|email|max:255|unique:users,email')]
    public string $email = '';
    
    #[Validate('required|string|min:8|confirmed')]
    public string $password = '';
    
    #[Validate('required|string|min:8')]
    public string $password_confirmation = '';
    
    #[Validate('accepted')]
    public bool $terms = false;

    public function register()
    {
        $validated = $this->validate();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        Auth::login($user);

        return redirect()->route('dashboard');
    }
};
?>

<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-purple-600 via-pink-500 to-red-500 p-4" style="font-family: 'Poppins', sans-serif;">
    <div class="bg-white rounded-3xl shadow-2xl overflow-hidden max-w-5xl w-full flex flex-col md:flex-row">
        <!-- Left Side - Register Form -->
        <div class="w-full md:w-1/2 p-8 md:p-12">
            <div class="mb-8">
                <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-2">
                    Create Account
                </h1>
                <p class="text-gray-500" style="font-family: 'Inter', sans-serif;">
                    Join us and start your journey
                </p>
            </div>

            <form wire:submit="register" class="space-y-6">
                <!-- Name Input -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Full Name
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <x-heroicon-o-user class="h-5 w-5 text-gray-400" />
                        </div>
                        <input
                            type="text"
                            id="name"
                            wire:model="name"
                            class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition"
                            placeholder="John Doe"
                            required
                        />
                    </div>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email Input -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Email Address
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <x-heroicon-o-envelope class="h-5 w-5 text-gray-400" />
                        </div>
                        <input
                            type="email"
                            id="email"
                            wire:model="email"
                            class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition"
                            placeholder="john@example.com"
                            required
                        />
                    </div>
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Input -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        Password
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <x-heroicon-o-lock-closed class="h-5 w-5 text-gray-400" />
                        </div>
                        <input
                            type="password"
                            id="password"
                            wire:model="password"
                            class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition"
                            placeholder="••••••••"
                            required
                        />
                    </div>
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirm Password Input -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                        Confirm Password
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <x-heroicon-o-lock-closed class="h-5 w-5 text-gray-400" />
                        </div>
                        <input
                            type="password"
                            id="password_confirmation"
                            wire:model="password_confirmation"
                            class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition"
                            placeholder="••••••••"
                            required
                        />
                    </div>
                    @error('password_confirmation')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Terms Checkbox -->
                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input
                            id="terms"
                            type="checkbox"
                            wire:model="terms"
                            class="w-4 h-4 border border-gray-300 rounded bg-gray-50 focus:ring-2 focus:ring-purple-500"
                            required
                        />
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="terms" class="text-gray-700">
                            I agree to the <a href="#" class="text-purple-600 hover:text-purple-500 font-medium">Terms and Conditions</a>
                        </label>
                        @error('terms')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Register Button -->
                <button
                    type="submit"
                    class="w-full bg-gradient-to-r from-purple-600 to-pink-500 text-white py-3 rounded-xl font-semibold hover:from-purple-700 hover:to-pink-600 transition duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5"
                >
                    Create Account
                </button>

                <!-- Divider -->
                <div class="relative my-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-4 bg-white text-gray-500" style="font-family: 'Inter', sans-serif;">Or register with</span>
                    </div>
                </div>

                <!-- Social Login Buttons -->
                <div class="grid grid-cols-3 gap-3">
                    <button
                        type="button"
                        class="flex items-center justify-center py-3 border border-gray-300 rounded-xl hover:bg-gray-50 transition duration-200"
                    >
                        <svg class="w-5 h-5" viewBox="0 0 24 24">
                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                        </svg>
                    </button>
                    <button
                        type="button"
                        class="flex items-center justify-center py-3 border border-gray-300 rounded-xl hover:bg-gray-50 transition duration-200"
                    >
                        <svg class="w-5 h-5" fill="#1877F2" viewBox="0 0 24 24">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                    </button>
                    <button
                        type="button"
                        class="flex items-center justify-center py-3 border border-gray-300 rounded-xl hover:bg-gray-50 transition duration-200"
                    >
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/>
                        </svg>
                    </button>
                </div>

                <!-- Sign In Link -->
                <div class="text-center mt-6">
                    <p class="text-gray-600" style="font-family: 'Inter', sans-serif;">
                        Already have an account? 
                        <a href="{{ route('login') }}" class="text-purple-600 hover:text-purple-500 font-semibold" wire:navigate>
                            Sign in
                        </a>
                    </p>
                </div>
            </form>
        </div>

        <!-- Right Side - Illustration -->
        <div class="hidden md:flex md:w-1/2 bg-gradient-to-br from-purple-600 via-pink-500 to-red-500 p-12 items-center justify-center">
            <div class="text-white text-center">
                <div class="mb-8">
                    <svg class="w-64 h-64 mx-auto" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="100" cy="100" r="80" fill="white" opacity="0.1"/>
                        <path d="M100 40 L120 80 L160 90 L130 120 L140 160 L100 140 L60 160 L70 120 L40 90 L80 80 Z" fill="white" opacity="0.8"/>
                    </svg>
                </div>
                <h2 class="text-3xl font-bold mb-4">
                    Join Our Community
                </h2>
                <p class="text-lg opacity-90" style="font-family: 'Inter', sans-serif;">
                    Start managing your books and products with ease. Get access to powerful features and insights.
                </p>
            </div>
        </div>
    </div>
</div>