<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'payable_type',
        'payable_id',
        'amount',
        'currency',
        'status',
        'provider',
        'provider_ref',
        'method',
        'paid_at',
        'failed_at',
        'refunded_at',
        'meta',
    ];

    protected $casts = [
        'amount' => 'integer',
        'paid_at' => 'datetime',
        'failed_at' => 'datetime',
        'refunded_at' => 'datetime',
        'meta' => 'array',
    ];

    public function payable()
    {
        return $this->morphTo();
    }
}
