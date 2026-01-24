@include('layouts.app')

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Booking - GearUp</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-50">
    @include('sweetalert::alert')
    
    @if ($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const errors = {!! json_encode($errors->all()) !!};
                const errorList = errors.map((error, index) => `<div style="text-align: left; margin: 8px 0;">${index + 1}. ${error}</div>`).join('');
                
                Swal.fire({
                    icon: 'error',
                    title: 'Validasi Data Gagal',
                    html: `<div style="text-align: left; color: #666; margin-top: 10px;">${errorList}</div>`,
                    confirmButtonText: 'Perbaiki Data',
                    confirmButtonColor: '#dc2626',
                    allowOutsideClick: false,
                    didClose: function() {
                        // Scroll ke field pertama yang error
                        const firstErrorField = document.querySelector('input:invalid, select:invalid, textarea:invalid');
                        if (firstErrorField) {
                            firstErrorField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            firstErrorField.focus();
                        }
                    }
                });
            });
        </script>
    @endif
    
    <div class="min-h-screen flex flex-col">
        <!-- Header/Navbar -->
        

        <!-- Main Content -->
        <main class="flex-1 px-6 lg:px-16 py-8">
            <div class="max-w-5xl mx-auto">
                <!-- Page Header -->
                <div class="mb-8">
                    <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-2">Form Booking</h1>
                    <p class="text-gray-600">Isi form di bawah untuk memesan gear outdoor pilihan Anda</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Product Info Card -->
                    <div class="lg:col-span-1">
                        <div class="bg-white rounded-xl shadow-sm p-6 sticky top-4">
                            <h2 class="text-lg font-bold text-gray-800 mb-1">Produk yang Dipilih</h2>
                            <p class="text-sm text-gray-500 mb-4">Total produk dipilih: <span class="font-semibold text-green-700">{{ count($products) }}</span></p>
                            <div class="relative">
                                <div id="carousel-container" class="relative w-full flex items-center justify-center">
                                    <button type="button" id="carousel-prev" class="absolute left-0 z-10 bg-white border border-gray-300 rounded-full shadow p-2 hover:bg-gray-100 disabled:opacity-30" style="top: 50%; transform: translateY(-50%);">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                                    </button>
                                    <div id="carousel-card-wrapper" class="w-full flex items-center justify-center">
                                        @foreach($products as $i => $product)
                                            <div class="carousel-card" data-index="{{ $i }}" style="display: {{ $i === 0 ? 'block' : 'none' }}; width: 100%;">
                                                <div class="min-w-[260px] max-w-sm bg-gray-50 rounded-2xl shadow-lg p-5 border-2 border-green-200 mx-auto">
                                                    <div class="aspect-square rounded-2xl overflow-hidden bg-gray-100 mb-4">
                                                        @if($product->image)
                                                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                                                        @else
                                                            <div class="w-full h-full flex items-center justify-center bg-gray-200">
                                                                <x-heroicon-o-photo class="h-20 w-20 text-gray-400" />
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <h3 class="text-lg font-bold text-gray-900 mb-2">{{ $product->name }}</h3>
                                                    @if($product->category)
                                                        <p class="text-sm text-gray-500 mb-2">{{ $product->category->categories }}</p>
                                                    @endif
                                                    @if($product->brand)
                                                        <div class="flex items-center space-x-2 mb-3">
                                                            <span class="text-sm text-gray-600 font-medium">Brand:</span>
                                                            <span class="inline-block bg-green-100 text-green-800 text-sm font-semibold px-3 py-1 rounded-full">{{ $product->brand->name }}</span>
                                                        </div>
                                                    @endif
                                                    <div class="border-t pt-3 mt-3">
                                                        <div class="flex justify-between items-center mb-2">
                                                            <span class="text-sm text-gray-600">Harga/hari:</span>
                                                            <span class="text-lg font-bold text-teal-700">Rp {{ number_format($product->price_per_day, 0, ',', '.') }}</span>
                                                        </div>
                                                        <div class="flex justify-between items-center">
                                                            <span class="text-sm text-gray-600">Stok:</span>
                                                            <span class="font-semibold text-gray-900 text-sm">{{ $product->stock }} unit</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button type="button" id="carousel-next" class="absolute right-0 z-10 bg-white border border-gray-300 rounded-full shadow p-2 hover:bg-gray-100 disabled:opacity-30" style="top: 50%; transform: translateY(-50%);">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                                    </button>
                                </div>
                                                    <script>
                                                    document.addEventListener('DOMContentLoaded', function() {
                                                        const cards = document.querySelectorAll('.carousel-card');
                                                        const prevBtn = document.getElementById('carousel-prev');
                                                        const nextBtn = document.getElementById('carousel-next');
                                                        let current = 0;
                                                        function updateCarousel() {
                                                            cards.forEach((card, i) => {
                                                                card.style.display = (i === current) ? 'block' : 'none';
                                                            });
                                                            prevBtn.disabled = (current === 0);
                                                            nextBtn.disabled = (current === cards.length - 1);
                                                        }
                                                        prevBtn.addEventListener('click', function() {
                                                            if (current > 0) { current--; updateCarousel(); }
                                                        });
                                                        nextBtn.addEventListener('click', function() {
                                                            if (current < cards.length - 1) { current++; updateCarousel(); }
                                                        });
                                                        updateCarousel();
                                                    });
                                                    </script>
                            </div>
                        </div>
                    </div>

                    <!-- Booking Form -->
                    <div class="lg:col-span-2">
                        <div class="bg-white rounded-xl shadow-sm p-8">
                            <h2 class="text-2xl font-bold text-gray-800 mb-6">Data Penyewa</h2>

                            <form action="{{ route('user.booking.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                                @csrf

                                <!-- Hidden input for products[] -->
                                @foreach($products as $product)
                                    <input type="hidden" name="products[]" value="{{ $product->id }}">
                                @endforeach


                                <!-- Section: Data Dasar -->
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-700 mb-4 flex items-center space-x-2">
                                        <x-heroicon-o-user class="h-5 w-5" />
                                        <span>Data Dasar</span>
                                    </h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <!-- Booker Name -->
                                        <div>
                                            <label for="booker_name" class="block text-sm font-medium text-gray-700 mb-2">
                                                Nama Pemesan <span class="text-red-500">*</span>
                                            </label>
                                            <input 
                                                type="text" 
                                                id="booker_name"
                                                name="booker_name"
                                                value="{{ old('booker_name', auth()->user()->name) }}"
                                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition"
                                                required
                                            >
                                            @error('booker_name')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <!-- Booker Email -->
                                        <div>
                                            <label for="booker_email" class="block text-sm font-medium text-gray-700 mb-2">
                                                Email Pemesan <span class="text-red-500">*</span>
                                            </label>
                                            <input 
                                                type="email" 
                                                id="booker_email"
                                                name="booker_email"
                                                value="{{ old('booker_email', auth()->user()->email) }}"
                                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition"
                                                required
                                            >
                                            @error('booker_email')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <!-- Booker Phone -->
                                        <div>
                                            <label for="booker_telp" class="block text-sm font-medium text-gray-700 mb-2">
                                                Nomor Telepon Pemesan <span class="text-red-500">*</span>
                                            </label>
                                            <input 
                                                type="tel" 
                                                id="booker_telp"
                                                name="booker_telp"
                                                value="{{ old('booker_telp', auth()->user()->phone) }}"
                                                placeholder="08xx xxxx xxxx"
                                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition"
                                                required
                                            >
                                            @error('booker_telp')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <!-- Amount -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                Jumlah Unit per Produk <span class="text-red-500">*</span>
                                            </label>
                                            <div class="space-y-3">
                                                @foreach($products as $product)
                                                    <div class="flex items-center space-x-3">
                                                        <span class="w-40 font-medium text-gray-700">{{ $product->name }}</span>
                                                        <input 
                                                            type="number" 
                                                            name="amount[{{ $product->id }}]"
                                                            value="{{ old('amount.' . $product->id, 1) }}"
                                                            min="1"
                                                            max="{{ $product->stock }}"
                                                            class="w-24 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition"
                                                            required
                                                        >
                                                        <span class="text-gray-500 text-sm">/ stok: {{ $product->stock }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                            @error('amount')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Section: Data Penyewa -->
                                <div class="border-t pt-6">
                                    <h3 class="text-lg font-semibold text-gray-700 mb-4 flex items-center space-x-2">
                                        <x-heroicon-o-identification class="h-5 w-5" />
                                        <span>Data Penyewa Lengkap</span>
                                    </h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <!-- Full Name -->
                                        <div>
                                            <label for="full_name" class="block text-sm font-medium text-gray-700 mb-2">
                                                Nama Lengkap Penyewa <span class="text-red-500">*</span>
                                            </label>
                                            <input 
                                                type="text" 
                                                id="full_name"
                                                name="full_name"
                                                value="{{ old('full_name', auth()->user()->name) }}"
                                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition"
                                                required
                                            >
                                            @error('full_name')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <!-- Phone Number -->
                                        <div>
                                            <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-2">
                                                Nomor HP Penyewa <span class="text-red-500">*</span>
                                            </label>
                                            <input 
                                                type="tel" 
                                                id="phone_number"
                                                name="phone_number"
                                                value="{{ old('phone_number', auth()->user()->phone) }}"
                                                placeholder="08xx xxxx xxxx"
                                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition"
                                                required
                                            >
                                            @error('phone_number')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <!-- Emergency Phone -->
                                        <div>
                                            <label for="emergency_phone_number" class="block text-sm font-medium text-gray-700 mb-2">
                                                Nomor Orang Tua/Wali <span class="text-red-500">*</span>
                                            </label>
                                            <input 
                                                type="tel" 
                                                id="emergency_phone_number"
                                                name="emergency_phone_number"
                                                value="{{ old('emergency_phone_number') }}"
                                                placeholder="08xx xxxx xxxx"
                                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition"
                                                required
                                            >
                                            @error('emergency_phone_number')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <!-- Instagram Handle -->
                                        <div>
                                            <label for="instagram_handle" class="block text-sm font-medium text-gray-700 mb-2">
                                                Instagram Handle
                                            </label>
                                            <input 
                                                type="text" 
                                                id="instagram_handle"
                                                name="instagram_handle"
                                                value="{{ old('instagram_handle') }}"
                                                placeholder="@username"
                                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition"
                                            >
                                            @error('instagram_handle')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <!-- Other Socials -->
                                        <div class="md:col-span-2">
                                            <label for="other_socials" class="block text-sm font-medium text-gray-700 mb-2">
                                                Media Sosial Lainnya
                                            </label>
                                            <input 
                                                type="text" 
                                                id="other_socials"
                                                name="other_socials"
                                                value="{{ old('other_socials') }}"
                                                placeholder="Twitter, Facebook, dll"
                                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition"
                                            >
                                            @error('other_socials')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <!-- Renter Address -->
                                        <div class="md:col-span-2">
                                            <label for="renter_address" class="block text-sm font-medium text-gray-700 mb-2">
                                                Alamat Lengkap <span class="text-red-500">*</span>
                                            </label>
                                            <textarea 
                                                id="renter_address"
                                                name="renter_address"
                                                rows="3"
                                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition"
                                                placeholder="Masukkan alamat lengkap untuk pengiriman"
                                                required
                                            >{{ old('renter_address') }}</textarea>
                                            @error('renter_address')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <!-- Identity Document -->
                                        <div class="md:col-span-2">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                Foto KTP/Identitas <span class="text-red-500">*</span>
                                            </label>
                                            <div class="space-y-4">
                                                <!-- Camera Button -->
                                                <button 
                                                    type="button"
                                                    id="camera-btn"
                                                    onclick="openCamera()"
                                                    class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-4 rounded-lg transition flex items-center justify-center space-x-2"
                                                >
                                                    <x-heroicon-o-camera class="h-5 w-5" />
                                                    <span>Ambil Foto dari Kamera</span>
                                                </button>

                                                <!-- Camera Modal -->
                                                <div id="camera-modal" class="hidden fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center p-4">
                                                    <div class="bg-white rounded-xl shadow-lg max-w-2xl w-full">
                                                        <div class="p-6">
                                                            <h3 class="text-xl font-bold text-gray-800 mb-4">Ambil Foto KTP/Identitas</h3>
                                                            
                                                            <!-- Video Stream -->
                                                            <video id="camera-stream" class="w-full rounded-lg bg-black mb-4" autoplay playsinline></video>
                                                            
                                                            <div class="flex items-center space-x-3">
                                                                <button 
                                                                    type="button"
                                                                    onclick="capturePhoto()"
                                                                    class="flex-1 bg-green-600 hover:bg-green-700 text-white font-semibold py-3 rounded-lg transition flex items-center justify-center space-x-2"
                                                                >
                                                                    <x-heroicon-o-check-circle class="h-5 w-5" />
                                                                    <span>Ambil Foto</span>
                                                                </button>
                                                                <button 
                                                                    type="button"
                                                                    onclick="closeCamera()"
                                                                    class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-3 rounded-lg transition"
                                                                >
                                                                    Batal
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Hidden Canvas for Capture -->
                                                <canvas id="capture-canvas" class="hidden"></canvas>

                                                <!-- Hidden File Input -->
                                                <input 
                                                    type="file" 
                                                    id="identity_document"
                                                    name="identity_document"
                                                    accept="image/*"
                                                    class="hidden"
                                                    required
                                                >

                                                <!-- Preview Section -->
                                                <div id="preview-container" class="hidden">
                                                    <p class="text-sm font-medium text-gray-700 mb-2">Preview Foto:</p>
                                                    <img id="preview-image" src="" alt="Preview" class="w-full max-w-sm rounded-lg border border-gray-300 mb-3">
                                                    <button 
                                                        type="button"
                                                        onclick="clearCapture()"
                                                        class="w-full bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded-lg transition"
                                                    >
                                                        Ambil Ulang
                                                    </button>
                                                </div>

                                                <!-- File Picker Fallback -->
                                                <div class="border-t pt-4">
                                                    <p class="text-sm text-gray-600 mb-3">Atau pilih file dari perangkat:</p>
                                                    <label class="flex items-center justify-center w-full px-4 py-3 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 transition">
                                                        <div class="flex flex-col items-center space-y-2">
                                                            <x-heroicon-o-arrow-up-tray class="h-6 w-6 text-gray-400" />
                                                            <span class="text-sm text-gray-600">Klik untuk pilih file</span>
                                                        </div>
                                                        <input 
                                                            type="file" 
                                                            id="file-picker"
                                                            accept="image/*"
                                                            class="hidden"
                                                            onchange="handleFilePick(this)"
                                                        >
                                                    </label>
                                                </div>
                                            </div>
                                            @error('identity_document')
                                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Section: Informasi Sewa -->
                                <div class="border-t pt-6">
                                    <h3 class="text-lg font-semibold text-gray-700 mb-4 flex items-center space-x-2">
                                        <x-heroicon-o-calendar class="h-5 w-5" />
                                        <span>Informasi Penyewaan</span>
                                    </h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <!-- Shipping Method -->
                                        <div>
                                            <label for="shipping_method" class="block text-sm font-medium text-gray-700 mb-2">
                                                Metode Pengiriman <span class="text-red-500">*</span>
                                            </label>
                                            <select 
                                                id="shipping_method"
                                                name="shipping_method"
                                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition"
                                                required
                                            >
                                                <option value="">Pilih Metode</option>
                                                <option value="pickup" {{ old('shipping_method') == 'pickup' ? 'selected' : '' }}>Ambil Sendiri</option>
                                                <option value="delivery" {{ old('shipping_method') == 'delivery' ? 'selected' : '' }}>Antar ke Alamat</option>
                                            </select>
                                            @error('shipping_method')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror

                                            <!-- Dropdown kurir, hanya muncul jika Antar ke Alamat -->
                                            <div id="courier-wrapper" class="mt-4" style="display: none;">
                                                <label for="courier" class="block text-sm font-medium text-gray-700 mb-2">Pilih Kurir</label>
                                                <select id="courier" name="courier" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition">
                                                    <option value="">Pilih Kurir</option>
                                                    <option value="JNE" {{ old('courier') == 'JNE' ? 'selected' : '' }}>JNE</option>
                                                    <option value="J&T" {{ old('courier') == 'J&T' ? 'selected' : '' }}>J&T</option>
                                                    <option value="SiCepat" {{ old('courier') == 'SiCepat' ? 'selected' : '' }}>SiCepat</option>
                                                    <option value="AnterAja" {{ old('courier') == 'AnterAja' ? 'selected' : '' }}>AnterAja</option>
                                                    <option value="GrabExpress" {{ old('courier') == 'GrabExpress' ? 'selected' : '' }}>GrabExpress</option>
                                                    <option value="Gojek" {{ old('courier') == 'Gojek' ? 'selected' : '' }}>Gojek</option>
                                                </select>
                                                @error('courier')
                                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                                @enderror
                                            </div>
                                            <script>
                                            document.addEventListener('DOMContentLoaded', function() {
                                                var shippingSelect = document.getElementById('shipping_method');
                                                var courierWrapper = document.getElementById('courier-wrapper');
                                                function toggleCourier() {
                                                    if (shippingSelect.value === 'delivery') {
                                                        courierWrapper.style.display = '';
                                                    } else {
                                                        courierWrapper.style.display = 'none';
                                                        document.getElementById('courier').value = '';
                                                    }
                                                }
                                                shippingSelect.addEventListener('change', toggleCourier);
                                                // Initial state
                                                toggleCourier();
                                            });
                                            </script>
                                        </div>

                                        <!-- Shipping Date -->
                                        <div>
                                            <label for="shipping_date" class="block text-sm font-medium text-gray-700 mb-2">
                                                Tanggal Pengiriman <span class="text-red-500">*</span>
                                            </label>
                                            <input 
                                                type="date" 
                                                id="shipping_date"
                                                name="shipping_date"
                                                value="{{ old('shipping_date') }}"
                                                min="{{ date('Y-m-d') }}"
                                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition"
                                                required
                                            >
                                            @error('shipping_date')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <!-- Rental Start -->
                                        <div>
                                            <label for="rental_start_at" class="block text-sm font-medium text-gray-700 mb-2">
                                                Tanggal Mulai Sewa <span class="text-red-500">*</span>
                                            </label>
                                            <input 
                                                type="date" 
                                                id="rental_start_at"
                                                name="rental_start_at"
                                                value="{{ old('rental_start_at') }}"
                                                min="{{ date('Y-m-d') }}"
                                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition"
                                                required
                                            >
                                            @error('rental_start_at')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <!-- Rental End -->
                                        <div>
                                            <label for="rental_end_at" class="block text-sm font-medium text-gray-700 mb-2">
                                                Tanggal Selesai Sewa <span class="text-red-500">*</span>
                                            </label>
                                            <input 
                                                type="date" 
                                                id="rental_end_at"
                                                name="rental_end_at"
                                                value="{{ old('rental_end_at') }}"
                                                min="{{ date('Y-m-d') }}"
                                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition"
                                                required
                                            >
                                            @error('rental_end_at')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Notes Section -->
                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                    <div class="flex items-start space-x-3">
                                        <x-heroicon-o-information-circle class="h-5 w-5 text-blue-600 flex-shrink-0 mt-0.5" />
                                        <div class="text-sm text-blue-800">
                                            <p class="font-semibold mb-1">Catatan Penting:</p>
                                            <ul class="list-disc list-inside space-y-1">
                                                <li>Booking akan dikonfirmasi dalam 1x24 jam</li>
                                                <li>Pastikan nomor telepon yang Anda masukkan aktif</li>
                                                <li>Pembayaran dilakukan setelah booking dikonfirmasi</li>
                                                <li>Area pengiriman khusus JADETABEK</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <div class="flex items-center space-x-4 pt-6">
                                    <button 
                                        type="submit"
                                        class="flex-1 bg-green-600 hover:bg-green-700 text-white font-semibold py-4 rounded-lg transition flex items-center justify-center space-x-2 shadow-lg"
                                    >
                                        <x-heroicon-o-check class="h-5 w-5" />
                                        <span>Kirim Booking</span>
                                    </button>
                                    <a 
                                        href="{{ route('home') }}"
                                        class="px-6 py-4 border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-lg transition"
                                    >
                                        Batal
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </main>

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
                        <p class="text-gray-400 text-sm">© 2026 GearUp. All rights reserved</p>
                        
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
    </div>

    <script>
        let stream = null;
        const videoElement = document.getElementById('camera-stream');
        const canvas = document.getElementById('capture-canvas');
        const fileInput = document.getElementById('identity_document');
        const previewImage = document.getElementById('preview-image');
        const previewContainer = document.getElementById('preview-container');
        const cameraModal = document.getElementById('camera-modal');

        async function openCamera() {
            try {
                // Request camera access
                stream = await navigator.mediaDevices.getUserMedia({
                    video: {
                        facingMode: 'environment', // Use back camera
                        width: { ideal: 1280 },
                        height: { ideal: 720 }
                    }
                });
                
                // Show camera modal
                cameraModal.classList.remove('hidden');
                videoElement.srcObject = stream;
            } catch (error) {
                console.error('Error accessing camera:', error);
                alert('Tidak dapat mengakses kamera. Pastikan Anda telah memberikan izin akses kamera.');
                // Fallback to file picker
                document.getElementById('file-picker').click();
            }
        }

        function closeCamera() {
            // Stop camera stream
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
                stream = null;
            }
            cameraModal.classList.add('hidden');
        }

        function capturePhoto() {
            if (!videoElement.srcObject) {
                alert('Kamera tidak aktif');
                return;
            }

            // Set canvas size to match video
            canvas.width = videoElement.videoWidth;
            canvas.height = videoElement.videoHeight;

            // Draw video frame to canvas
            const context = canvas.getContext('2d');
            context.drawImage(videoElement, 0, 0);

            // Convert canvas to blob
            canvas.toBlob(blob => {
                // Create a File object from the blob
                const file = new File([blob], 'ktp-' + Date.now() + '.jpg', { type: 'image/jpeg' });
                
                // Create a DataTransfer object to set files
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                fileInput.files = dataTransfer.files;

                // Show preview
                const reader = new FileReader();
                reader.onload = e => {
                    previewImage.src = e.target.result;
                    previewContainer.classList.remove('hidden');
                    closeCamera();
                };
                reader.readAsDataURL(blob);
            }, 'image/jpeg', 0.95);
        }

        function clearCapture() {
            fileInput.value = '';
            previewContainer.classList.add('hidden');
            openCamera(); // Buka kamera lagi
        }

        function handleFilePick(input) {
            if (input.files && input.files[0]) {
                const file = input.files[0];
                
                // Set the main file input
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                fileInput.files = dataTransfer.files;

                // Show preview
                const reader = new FileReader();
                reader.onload = e => {
                    previewImage.src = e.target.result;
                    previewContainer.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            }
        }

        // Close camera when modal is closed
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape' && !cameraModal.classList.contains('hidden')) {
                closeCamera();
            }
        });
    </script>
</body>
</html>
