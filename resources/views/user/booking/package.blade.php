@extends('layouts.app')

@section('content')
<div class="min-h-screen flex flex-col">
    <main class="flex-1 px-6 lg:px-16 py-8">
        <div class="max-w-5xl mx-auto">
            <!-- Page Header -->
            <div class="mb-8">
                <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-2">Form Booking Paket</h1>
                <p class="text-gray-600">Isi form di bawah untuk memesan paket outdoor pilihan Anda</p>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Package Info Card -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl shadow-lg p-6 sticky top-4 border-2 border-green-200">
                        <h2 class="text-xl font-bold text-gray-800 mb-4 tracking-tight">Paket yang Dipilih</h2>
                        <div class="bg-white rounded-2xl border-2 border-green-300 p-4 flex flex-col items-center mb-4">
                            <div class="w-full flex justify-center mb-4">
                                <div class="aspect-square w-40 h-40 rounded-2xl overflow-hidden bg-gray-100 flex items-center justify-center border border-green-200">
                                    @if($package->image)
                                        <img src="{{ asset('storage/' . $package->image) }}" alt="{{ $package->name_package }}" class="object-cover w-full h-full">
                                    @else
                                        <x-heroicon-o-photo class="h-20 w-20 text-gray-400" />
                                    @endif
                                </div>
                            </div>
                            <div class="text-center w-full">
                                <h3 class="text-xl font-bold text-gray-900 mb-1 leading-tight">{{ $package->name_package }}</h3>
                                <div class="text-sm text-gray-500 mb-2">{{ $package->description }}</div>
                                <div class="flex justify-center items-center gap-2 mb-2">
                                    <span class="text-sm text-gray-600">Harga Paket:</span>
                                    <span class="text-xl font-bold text-green-700">Rp {{ number_format($package->price, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4">
                            <h4 class="font-semibold text-gray-700 mb-2">Daftar Produk:</h4>
                            <ul class="space-y-2">
                                @foreach($package->products as $product)
                                <li class="flex items-center gap-2 text-sm text-gray-700">
                                    @if($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-8 h-8 object-cover rounded border">
                                    @endif
                                    <span>{{ $product->name }}</span>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        <!-- Payment Summary -->
                        <div class="mt-6 bg-green-50 border border-green-200 rounded-xl p-4">
                            <h4 class="font-bold text-green-800 mb-3 flex items-center gap-2">
                                <x-heroicon-o-currency-dollar class="h-5 w-5" />
                                Ringkasan Pembayaran
                            </h4>
                            <div class="flex justify-between text-sm mb-2">
                                <span>Harga Paket / Hari</span>
                                <span>Rp {{ number_format($package->price, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-sm mb-2">
                                <span>Estimasi Hari Sewa</span>
                                <span id="summary-days">-</span>
                            </div>
                            <div class="flex justify-between text-base font-bold border-t pt-2 mt-2">
                                <span>Subtotal</span>
                                <span id="summary-total">Rp {{ number_format($package->price, 0, ',', '.') }}</span>
                            </div>
                            <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                const start = document.getElementById('rental_start_at');
                                const end = document.getElementById('rental_end_at');
                                const daysSpan = document.getElementById('summary-days');
                                const totalSpan = document.getElementById('summary-total');
                                const price = {{ $package->price }};
                                function updateSummary() {
                                    let days = 0;
                                    if (start && end && start.value && end.value) {
                                        const d1 = new Date(start.value);
                                        const d2 = new Date(end.value);
                                        days = Math.floor((d2 - d1) / (1000*60*60*24)) + 1;
                                    }
                                    if (isNaN(days) || days < 1) {
                                        daysSpan.textContent = '-';
                                        totalSpan.textContent = 'Rp 0';
                                    } else {
                                        daysSpan.textContent = days + ' hari';
                                        totalSpan.textContent = 'Rp ' + (price * days).toLocaleString('id-ID');
                                    }
                                }
                                if (start && end) {
                                    start.addEventListener('change', updateSummary);
                                    end.addEventListener('change', updateSummary);
                                    updateSummary();
                                }
                            });
                            </script>
                        </div>
                    </div>
                </div>
                <!-- Booking Form -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl shadow-sm p-8">
                        <h2 class="text-2xl font-bold text-gray-800 mb-6">Data Penyewa</h2>
                        <form action="{{ route('user.booking.package.store', $package->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                            @csrf
                            <!-- Hidden input for package -->
                            <input type="hidden" name="package_id" value="{{ $package->id }}">
                            <!-- Section: Data Dasar -->
                            <div class="mt-8">
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
                                                        <div class="w-full flex justify-center items-center mb-4">
                                                            <video id="camera-stream" class="rounded-lg bg-black aspect-video max-w-full max-h-[400px] object-contain" style="aspect-ratio: 4/3; background:#000;" autoplay playsinline></video>
                                                        </div>
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
</div>
<script>
// Camera logic for booking package
let stream = null;
function openCamera() {
    const modal = document.getElementById('camera-modal');
    const video = document.getElementById('camera-stream');
    modal.classList.remove('hidden');
    if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
        navigator.mediaDevices.getUserMedia({ video: true })
            .then(function(s) {
                stream = s;
                video.srcObject = stream;
                video.play();
            })
            .catch(function(err) {
                alert('Tidak dapat mengakses kamera: ' + err.message);
                closeCamera();
            });
    } else {
        alert('Browser tidak mendukung kamera.');
        closeCamera();
    }
}
function closeCamera() {
    const modal = document.getElementById('camera-modal');
    modal.classList.add('hidden');
    const video = document.getElementById('camera-stream');
    if (stream) {
        stream.getTracks().forEach(track => track.stop());
        stream = null;
    }
    video.srcObject = null;
}
function capturePhoto() {
    const video = document.getElementById('camera-stream');
    const canvas = document.getElementById('capture-canvas');
    const preview = document.getElementById('preview-image');
    const previewContainer = document.getElementById('preview-container');
    const input = document.getElementById('identity_document');
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
    canvas.toBlob(function(blob) {
        const file = new File([blob], 'capture.jpg', { type: 'image/jpeg' });
        // Set file to input (simulate file upload)
        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(file);
        input.files = dataTransfer.files;
        // Show preview
        preview.src = URL.createObjectURL(blob);
        previewContainer.classList.remove('hidden');
    }, 'image/jpeg', 0.95);
    closeCamera();
}
function clearCapture() {
    const preview = document.getElementById('preview-image');
    const previewContainer = document.getElementById('preview-container');
    const input = document.getElementById('identity_document');
    preview.src = '';
    previewContainer.classList.add('hidden');
    input.value = '';
    openCamera();
}
function handleFilePick(input) {
    const file = input.files[0];
    const preview = document.getElementById('preview-image');
    const previewContainer = document.getElementById('preview-container');
    if (file) {
        preview.src = URL.createObjectURL(file);
        previewContainer.classList.remove('hidden');
        // Set file to main input
        document.getElementById('identity_document').files = input.files;
    } else {
        preview.src = '';
        previewContainer.classList.add('hidden');
    }
}
</script>
@endsection
