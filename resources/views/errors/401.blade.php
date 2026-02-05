@extends('components.layouts.guest')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-red-50 to-pink-50 flex items-center justify-center px-4">
    <div class="max-w-sm w-full text-center">
        <!-- 401 Icon -->
        <div class="mb-4">
            <svg class="mx-auto h-16 w-16 text-red-500 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4v2m0 0v2m-6-6v2m0 4v2m12-6v2m0 4v2m-6-6v2m0 4v2" />
            </svg>
        </div>

        <!-- Error Title -->
        <h1 class="text-3xl font-bold text-gray-900 mb-1">
            Akses Ditolak
        </h1>
        
        <h2 class="text-sm text-gray-600 mb-3">
            (401 Unauthorized)
        </h2>

        <!-- Error Message -->
        <p class="text-gray-600 text-sm mb-5 leading-relaxed">
            Anda harus login terlebih dahulu untuk mengakses halaman ini.
        </p>

        <!-- Action Buttons -->
        <div class="space-y-2 mb-5">
            <!-- Login Modal Button -->
            <a 
                href="{{ route('home') }}?openModal=login"
                class="w-full bg-green-500 hover:bg-green-600 text-white font-semibold py-2.5 px-4 rounded-lg transition duration-200 flex items-center justify-center space-x-2 group text-sm"
            >
                <svg class="h-4 w-4 group-hover:scale-110 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Login Sekarang</span>
            </a>

            <!-- Back to Home Button -->
            <a 
                href="{{ route('home') }}" 
                class="w-full bg-gray-300 hover:bg-gray-400 text-gray-900 font-semibold py-2.5 px-4 rounded-lg transition duration-200 flex items-center justify-center space-x-2 group block text-sm"
            >
                <svg class="h-4 w-4 group-hover:scale-110 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>Kembali ke Beranda</span>
            </a>
        </div>

        <!-- Additional Info -->
        <div class="bg-white border-l-4 border-red-500 rounded p-3 text-left">
            <p class="text-xs text-gray-600">
                <span class="font-semibold text-gray-900">💡 Info:</span> Belum punya akun? Daftar gratis melalui login.
            </p>
        </div>
    </div>
</div>

<!-- Auth Modal -->
<livewire:auth-modal />
@endsection
