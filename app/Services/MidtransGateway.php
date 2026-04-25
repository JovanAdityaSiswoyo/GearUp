<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class MidtransGateway
{
    private function baseUrl(): string
    {
        return config('midtrans.api_url') ?? (
            config('midtrans.is_production')
                ? 'https://api.midtrans.com/v2'
                : 'https://api.sandbox.midtrans.com/v2'
        );
    }

    private function snapUrl(): string
    {
        return config('midtrans.is_production')
            ? 'https://app.midtrans.com/snap/v1/transactions'
            : 'https://app.sandbox.midtrans.com/snap/v1/transactions';
    }

    public function createSnapToken(string $orderId, int $amount, array $customer): string
    {
        $grossAmount = (int) round($amount / 100);

        $payload = [
            'transaction_details' => [
                'order_id'     => $orderId,
                'gross_amount' => $grossAmount,
            ],
            'customer_details' => [
                'first_name' => $customer['first_name'] ?? null,
                'email'      => $customer['email'] ?? null,
                'phone'      => $customer['phone'] ?? null,
            ],
        ];

        $response = Http::withBasicAuth(config('midtrans.server_key'), '')
            ->acceptJson()
            ->post($this->snapUrl(), $payload);

        $response->throw();

        $token = $response->json('token');

        if (! $token) {
            throw new \RuntimeException('Midtrans Snap token kosong. Response: ' . $response->body());
        }

        return $token;
    }

    public function createBankTransferCharge(string $orderId, int $amount, array $customer, string $bank = null): array
    {
        $grossAmount = (int) round($amount / 100);

        $payload = [
            'payment_type' => 'bank_transfer',
            'transaction_details' => [
                'order_id'     => $orderId,
                'gross_amount' => $grossAmount,
            ],
            'customer_details' => [
                'first_name' => $customer['first_name'] ?? null,
                'email'      => $customer['email'] ?? null,
                'phone'      => $customer['phone'] ?? null,
            ],
        ];

        if ($bank) {
            $payload['bank_transfer'] = ['bank' => $bank];
        }

        $response = Http::withBasicAuth(config('midtrans.server_key'), '')
            ->acceptJson()
            ->post($this->baseUrl() . '/charge', $payload);

        $response->throw();

        return $response->json();
    }

    public function validateNotificationSignature(array $payload): bool
    {
        $serverKey = config('midtrans.server_key');

        if (! isset($payload['order_id'], $payload['status_code'], $payload['gross_amount'], $payload['signature_key'])) {
            return false;
        }

        $signature = hash('sha512', $payload['order_id'] . $payload['status_code'] . $payload['gross_amount'] . $serverKey);

        return hash_equals($signature, $payload['signature_key']);
    }
}