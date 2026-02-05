<div @openAuthModal.window="openModal($event.detail.tab ?? 'login')">
    @if($isOpen)
        <!-- Modal Overlay -->
        <div 
            class="fixed inset-0 bg-black/40 backdrop-blur-sm z-40 animate-in fade-in duration-200"
            wire:click="closeModal()"
        ></div>

        <!-- Modal Container -->
        <div 
            class="fixed inset-0 z-50 flex items-center justify-center p-4 animate-in fade-in zoom-in duration-200"
        >
            <div 
                class="bg-white rounded-2xl shadow-2xl max-w-md w-full max-h-[90vh] overflow-y-auto transition-all duration-300"
                @click.stop
            >
            <!-- Header with Close Button -->
            <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between rounded-t-2xl">
                <h2 class="text-xl font-bold text-gray-900">
                    {{ $activeTab === 'login' ? 'Masuk ke Akun' : 'Daftar Akun Baru' }}
                </h2>
                <button
                    type="button"
                    wire:click="closeModal()"
                    class="text-gray-500 hover:text-gray-700 transition"
                >
                    <x-heroicon-o-x-mark class="h-6 w-6" />
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-6">
                <!-- Tab Navigation -->
                <div class="flex gap-4 mb-6">
                    <button
                        wire:click="setActiveTab('login')"
                        class="flex-1 pb-2 text-sm font-medium transition-colors {{ $activeTab === 'login' ? 'text-gray-900 border-b-2 border-green-500' : 'text-gray-500 border-b-2 border-transparent' }}"
                    >
                        Masuk
                    </button>
                    <button
                        wire:click="setActiveTab('register')"
                        class="flex-1 pb-2 text-sm font-medium transition-colors {{ $activeTab === 'register' ? 'text-gray-900 border-b-2 border-green-500' : 'text-gray-500 border-b-2 border-transparent' }}"
                    >
                        Daftar
                    </button>
                </div>

                <!-- Login Form -->
                @if($activeTab === 'login')
                    <form wire:submit="handleLogin" class="space-y-4">
                        <!-- Email Input -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                Email atau Nomor Telepon
                            </label>
                            <input
                                type="text"
                                id="email"
                                wire:model="loginEmail"
                                placeholder="Masukkan email atau nomor telepon"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition"
                            />
                            @error('loginEmail')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Password Input -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                Kata Sandi
                            </label>
                            <input
                                type="password"
                                id="password"
                                wire:model="loginPassword"
                                placeholder="Masukkan kata sandi"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition"
                            />
                            @error('loginPassword')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Remember Me -->
                        <div class="flex items-center">
                            <input
                                type="checkbox"
                                id="remember"
                                wire:model="loginRemember"
                                class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500"
                            />
                            <label for="remember" class="ml-2 text-sm text-gray-600">
                                Ingat saya
                            </label>
                        </div>

                        <!-- Login Button -->
                        <button
                            type="submit"
                            wire:loading.attr="disabled"
                            class="w-full bg-green-500 hover:bg-green-600 disabled:bg-gray-400 text-white font-semibold py-3 rounded-lg transition duration-200 flex items-center justify-center"
                        >
                            <span wire:loading.remove>Masuk</span>
                            <span wire:loading>
                                <x-heroicon-o-arrow-path class="h-5 w-5 animate-spin" />
                            </span>
                        </button>

                        <!-- Forgot Password Link -->
                        <div class="text-center">
                            <a href="#" class="text-green-600 hover:text-green-700 text-sm font-medium">
                                Lupa kata sandi?
                            </a>
                        </div>
                    </form>
                @endif

                <!-- Register Form -->
                @if($activeTab === 'register')
                    <form wire:submit="handleRegister" class="space-y-4">
                        <!-- Name Input -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                Nama Lengkap
                            </label>
                            <input
                                type="text"
                                id="name"
                                wire:model="registerName"
                                placeholder="Masukkan nama lengkap"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition"
                            />
                            @error('registerName')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email Input -->
                        <div>
                            <label for="register-email" class="block text-sm font-medium text-gray-700 mb-2">
                                Email
                            </label>
                            <input
                                type="email"
                                id="register-email"
                                wire:model="registerEmail"
                                placeholder="Masukkan email"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition"
                            />
                            @error('registerEmail')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Password Input -->
                        <div>
                            <label for="register-password" class="block text-sm font-medium text-gray-700 mb-2">
                                Kata Sandi
                            </label>
                            <input
                                type="password"
                                id="register-password"
                                wire:model="registerPassword"
                                placeholder="Minimal 8 karakter"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition"
                            />
                            @error('registerPassword')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Confirm Password Input -->
                        <div>
                            <label for="register-password-confirm" class="block text-sm font-medium text-gray-700 mb-2">
                                Konfirmasi Kata Sandi
                            </label>
                            <input
                                type="password"
                                id="register-password-confirm"
                                wire:model="registerPasswordConfirm"
                                placeholder="Ulangi kata sandi"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition"
                            />
                            @error('registerPasswordConfirm')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Terms Checkbox -->
                        <div class="flex items-start">
                            <input
                                type="checkbox"
                                id="terms"
                                wire:model="registerTerms"
                                class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500 mt-1"
                            />
                            <label for="terms" class="ml-2 text-sm text-gray-600">
                                Saya setuju dengan 
                                <a href="#" class="text-green-600 hover:text-green-700 font-medium">Syarat & Ketentuan</a>
                            </label>
                        </div>
                        @error('registerTerms')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror

                        <!-- Register Button -->
                        <button
                            type="submit"
                            wire:loading.attr="disabled"
                            class="w-full bg-green-500 hover:bg-green-600 disabled:bg-gray-400 text-white font-semibold py-3 rounded-lg transition duration-200 flex items-center justify-center"
                        >
                            <span wire:loading.remove>Daftar</span>
                            <span wire:loading>
                                <x-heroicon-o-arrow-path class="h-5 w-5 animate-spin" />
                            </span>
                        </button>
                    </form>
                @endif

                <!-- Divider -->
                <div class="relative my-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-white text-gray-500">atau</span>
                    </div>
                </div>

                <!-- Social Login Buttons -->
                <div class="space-y-3">
                    <button
                        type="button"
                        class="w-full flex items-center justify-center gap-2 border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium py-3 rounded-lg transition"
                    >
                        <svg class="w-5 h-5" viewBox="0 0 24 24">
                            <path fill="#1F2937" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        </svg>
                        Google
                    </button>
                    <button
                        type="button"
                        class="w-full flex items-center justify-center gap-2 border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium py-3 rounded-lg transition"
                    >
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="#1F2937">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                        Facebook
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

<script>
    window.openAuthModal = function(tab = 'login') {
        @this.openModal(tab);
    };
</script>