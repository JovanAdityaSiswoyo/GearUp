<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'payment_id',
        'provider',
        'payment_type',
        'bank',
        'status',
        'transaction_id',
        'order_id',
        'amount',
        'currency',
        'expires_at',
        'request_payload',
        'response_payload',
    ];

    protected $casts = [
        'amount' => 'integer',
        'expires_at' => 'datetime',
        'request_payload' => 'array',
        'response_payload' => 'array',
    ];

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}
