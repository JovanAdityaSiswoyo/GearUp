<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookProduct;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Payment::query()->with('payable');

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->filled('payable_type') && $request->filled('payable_id')) {
            $mappedType = $this->mapPayableType($request->get('payable_type'));
            if ($mappedType) {
                $query->where('payable_type', $mappedType)
                      ->where('payable_id', $request->get('payable_id'));
            }
        }

        $payments = $query->paginate(15);
        return response()->json($payments);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'payable_type' => 'required|string|in:book,book_product',
            'payable_id' => 'required|uuid',
            'amount' => 'required|integer|min:0',
            'currency' => 'nullable|string|max:10',
            'status' => 'nullable|string|in:pending,paid,failed,refunded',
            'provider' => 'nullable|string|max:255',
            'provider_ref' => 'nullable|string|max:255',
            'method' => 'nullable|string|max:255',
            'paid_at' => 'nullable|date',
            'failed_at' => 'nullable|date',
            'refunded_at' => 'nullable|date',
            'meta' => 'nullable|array',
        ]);

        $validated['payable_type'] = $this->mapPayableType($validated['payable_type']);

        if (!$validated['payable_type']) {
            return response()->json(['message' => 'Invalid payable_type'], 422);
        }

        $payment = Payment::create($validated);

        return response()->json($payment->load('payable'), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $payment = Payment::with('payable')->findOrFail($id);
        return response()->json($payment);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $payment = Payment::findOrFail($id);

        $validated = $request->validate([
            'payable_type' => 'sometimes|string|in:book,book_product',
            'payable_id' => 'sometimes|uuid',
            'amount' => 'sometimes|integer|min:0',
            'currency' => 'sometimes|nullable|string|max:10',
            'status' => 'sometimes|nullable|string|in:pending,paid,failed,refunded',
            'provider' => 'sometimes|nullable|string|max:255',
            'provider_ref' => 'sometimes|nullable|string|max:255',
            'method' => 'sometimes|nullable|string|max:255',
            'paid_at' => 'sometimes|nullable|date',
            'failed_at' => 'sometimes|nullable|date',
            'refunded_at' => 'sometimes|nullable|date',
            'meta' => 'sometimes|nullable|array',
        ]);

        if (isset($validated['payable_type'])) {
            $validated['payable_type'] = $this->mapPayableType($validated['payable_type']);

            if (!$validated['payable_type']) {
                return response()->json(['message' => 'Invalid payable_type'], 422);
            }
        }

        $payment->update($validated);

        return response()->json($payment->load('payable'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $payment = Payment::findOrFail($id);
        $payment->delete();

        return response()->json(['message' => 'Payment deleted successfully'], 200);
    }

    private function mapPayableType(string $type): ?string
    {
        return match ($type) {
            'book' => Book::class,
            'book_product' => BookProduct::class,
            default => null,
        };
    }
}
