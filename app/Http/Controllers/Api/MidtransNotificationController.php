<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Services\MidtransGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MidtransNotificationController extends Controller
{
    public function handle(Request $request, MidtransGateway $midtrans)
    {
        $payload = $request->all();

        if (! $midtrans->validateNotificationSignature($payload)) {
            Log::warning('Midtrans notification failed signature validation.', ['payload' => $payload]);
            return response()->json(['message' => 'Invalid signature'], 400);
        }

        $transaction = Transaction::where('transaction_id', $payload['transaction_id'] ?? null)
            ->orWhere('order_id', $payload['order_id'] ?? null)
            ->first();

        if (! $transaction) {
            Log::warning('Midtrans notification transaction not found.', ['payload' => $payload]);
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        $status = $payload['transaction_status'] ?? $transaction->status;

        $transaction->update([
            'status' => $status,
            'response_payload' => $payload,
        ]);

        $payment = $transaction->payment;
        if ($payment) {
            $paymentStatus = match ($status) {
                'settlement', 'capture' => 'paid',
                'pending', 'challenge' => 'pending',
                default => 'failed',
            };

            $payment->update([
                'status' => $paymentStatus,
                'paid_at' => in_array($paymentStatus, ['paid']) ? now() : null,
                'failed_at' => $paymentStatus === 'failed' ? now() : null,
            ]);
        }

        return response()->json(['message' => 'Notification processed']);
    }
}
