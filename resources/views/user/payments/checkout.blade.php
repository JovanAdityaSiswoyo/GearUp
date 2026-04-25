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
                        <h1 class="text-3xl font-bold text-gray-900">Pembayaran Midtrans</h1>
                        <p class="mt-2 text-sm text-gray-500">Transfer bank hanya tersedia untuk pembayaran booking dan denda.</p>
                    </div>

                    <div class="grid gap-4">
                        <div class="rounded-3xl border border-gray-200 bg-gray-50 p-6">
                            <h2 class="text-lg font-semibold text-gray-900">Ringkasan Pembayaran</h2>
                            <div class="mt-4 grid gap-3 text-sm text-gray-700">
                                <div class="flex justify-between">
                                    <span>Nomor Transaksi Midtrans</span>
                                    <span class="font-medium text-gray-900">{{ $transaction->transaction_id ?? '-' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Order ID</span>
                                    <span class="font-medium text-gray-900">{{ $transaction->order_id ?? '-' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Jumlah</span>
                                    <span class="font-semibold text-green-700">Rp {{ number_format($transaction->amount / 100, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Metode</span>
                                    <span class="font-semibold text-gray-900">Transfer Bank ({{ strtoupper($transaction->bank ?? config('midtrans.bank')) }})</span>
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

                        <div class="rounded-3xl border border-gray-200 bg-white p-6">
                            <h2 class="text-lg font-semibold text-gray-900">Instruksi Transfer</h2>
                            <div class="mt-4 space-y-4 text-sm text-gray-700">
                                <p>Silakan transfer ke virtual account di bawah ini sesuai jumlah pembayaran. Setelah transfer berhasil, sistem akan memproses verifikasi secara otomatis.</p>

                                @php
                                    $response = $transaction->response_payload ?? [];
                                    $vaNumbers = data_get($response, 'va_numbers', []);
                                    $bankNumber = data_get($vaNumbers, '0.va_number') ?? data_get($response, 'permata_va_number');
                                    $bankName = data_get($vaNumbers, '0.bank') ?? $transaction->bank;
                                @endphp

                                <div class="rounded-3xl border border-dashed border-gray-300 bg-gray-50 p-5">
                                    <div class="grid gap-3">
                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-500">Bank</span>
                                            <span class="text-lg font-semibold text-gray-900">{{ strtoupper($bankName ?? 'BANK TRANSFER') }}</span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-500">Nomor VA</span>
                                            <span class="text-lg font-semibold text-gray-900">{{ $bankNumber ?? '-' }}</span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-500">Jumlah transfer</span>
                                            <span class="text-lg font-semibold text-green-700">Rp {{ number_format($transaction->amount / 100, 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="rounded-3xl border border-gray-200 p-4 bg-white">
                                    <p class="text-sm font-semibold text-gray-900">Langkah berikutnya</p>
                                    <ol class="list-decimal list-inside mt-3 space-y-2 text-sm text-gray-700">
                                        <li>Salin nomor virtual account di atas.</li>
                                        <li>Buka aplikasi internet banking / mobile banking Anda.</li>
                                        <li>Pilih transfer bank ke virtual account.</li>
                                        <li>Masukkan nomor VA dan jumlah pembayaran yang ditampilkan.</li>
                                        <li>Konfirmasi transfer dan simpan bukti transaksi jika diperlukan.</li>
                                    </ol>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-3xl border border-gray-200 bg-white p-6 text-right">
                            <a href="{{ route('user.my-booking') }}" class="inline-flex items-center justify-center rounded-full bg-gray-800 px-6 py-3 text-sm font-semibold text-white hover:bg-gray-900 transition">Kembali ke Booking Saya</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
