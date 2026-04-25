<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout Pembayaran - GearUp</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    @include('layouts.app')
    <div class="min-h-screen py-12">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-3xl overflow-hidden">
                <div class="px-6 py-8 sm:px-10">
                    <div class="mb-8 text-center">
                        <h1 class="text-3xl font-bold text-gray-900">Pembayaran</h1>
                        <p class="mt-2 text-sm text-gray-500">Klik tombol di bawah untuk menyelesaikan pembayaran.</p>
                    </div>

                    <div class="grid gap-4">
                        {{-- Ringkasan --}}
                        <div class="rounded-3xl border border-gray-200 bg-gray-50 p-6">
                            <h2 class="text-lg font-semibold text-gray-900">Ringkasan Pembayaran</h2>
                            <div class="mt-4 grid gap-3 text-sm text-gray-700">
                                <div class="flex justify-between">
                                    <span>Order ID</span>
                                    <span class="font-medium text-gray-900">{{ $transaction->order_id ?? '-' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Jumlah</span>
                                    <span class="font-semibold text-green-700">Rp {{ number_format($transaction->amount / 100, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Status</span>
                                    <span class="font-semibold text-gray-900">{{ ucfirst($transaction->status) }}</span>
                                </div>
                                @if($transaction->expires_at)
                                    <div class="flex justify-between">
                                        <span>Batas Waktu</span>
                                        <span class="font-semibold text-gray-900">{{ $transaction->expires_at->format('d M Y H:i') }} WIB</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Tombol Bayar --}}
                        <div class="rounded-3xl border border-gray-200 bg-white p-6 text-center">
                            <p class="text-sm text-gray-500 mb-6">
                                Pembayaran diproses secara aman oleh <strong>Midtrans</strong>. Anda bisa memilih metode pembayaran (transfer bank, dompet digital, kartu kredit, dll) di popup.
                            </p>

                            <button
                                id="pay-button"
                                class="inline-flex items-center justify-center gap-2 rounded-full bg-green-600 px-8 py-3.5 text-base font-semibold text-white hover:bg-green-700 active:scale-95 transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                </svg>
                                Bayar Sekarang
                            </button>

                            <p class="mt-4 text-xs text-gray-400">Anda akan diarahkan ke halaman pembayaran Midtrans yang aman.</p>
                        </div>

                        {{-- Kembali --}}
                        <div class="rounded-3xl border border-gray-200 bg-white p-6 text-right">
                            <a href="{{ route('user.my-booking') }}" class="inline-flex items-center justify-center rounded-full bg-gray-800 px-6 py-3 text-sm font-semibold text-white hover:bg-gray-900 transition">
                                Kembali ke Booking Saya
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Midtrans Snap JS --}}
    <script
        src="{{ config('midtrans.is_production')
            ? 'https://app.midtrans.com/snap/snap.js'
            : 'https://app.sandbox.midtrans.com/snap/snap.js' }}"
        data-client-key="{{ config('midtrans.client_key') }}"
    ></script>

    <script>
        const payButton = document.getElementById('pay-button');

        payButton.addEventListener('click', function () {
            payButton.disabled = true;
            payButton.innerHTML = `
                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                </svg>
                Memuat...
            `;

            snap.pay('{{ $snapToken }}', {
                onSuccess: function (result) {
                    window.location.href = '{{ route('user.my-booking') }}';
                },
                onPending: function (result) {
                    window.location.href = '{{ route('user.my-booking') }}';
                },
                onError: function (result) {
                    alert('Pembayaran gagal. Silakan coba lagi.');
                    payButton.disabled = false;
                    payButton.innerHTML = `
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                        Bayar Sekarang
                    `;
                },
                onClose: function () {
                    // User tutup popup tanpa bayar, re-enable tombol
                    payButton.disabled = false;
                    payButton.innerHTML = `
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                        Bayar Sekarang
                    `;
                },
            });
        });
    </script>
</body>
</html>