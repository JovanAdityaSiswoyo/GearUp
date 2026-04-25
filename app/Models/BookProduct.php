<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Payment;
use App\Enums\ItemStatus;
use App\Enums\OrderStatus;

class BookProduct extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'book_products';

    // For compatibility with old blade: $loan->equipment
    public function getEquipmentAttribute()
    {
        return $this->product;
    }

    protected $fillable = [
        'id_user',
        'id_product',
        'book_code',
        'code',
        'status',
        'item_status',
        'order_status',
        'pickup_photo',
        'return_photo',
        'issue_photo',
        'issue_notes',
        'issue_condition',
        'fine_percentage',
        'fine_amount',
        'checkin_appointment_start',
        'checkout_appointment_end',
        'delivery_at',
        'returned_at',
        'rental_date',
        'amount',
        'total_price',
        'booker_name',
        'booker_email',
        'booker_telp',
    ];

    protected $casts = [
        'checkin_appointment_start' => 'datetime',
        'checkout_appointment_end' => 'datetime',
        'delivery_at' => 'datetime',
        'returned_at' => 'datetime',
        'fine_percentage' => 'integer',
        'fine_amount' => 'integer',
        'item_status' => ItemStatus::class,
        'order_status' => OrderStatus::class,
    ];

    public function getRentalDaysAttribute(): int
    {
        if (!$this->checkin_appointment_start || !$this->checkout_appointment_end) {
            return 0;
        }

        $startDate = $this->checkin_appointment_start->copy()->startOfDay();
        $endDate = $this->checkout_appointment_end->copy()->startOfDay();

        return max(1, $startDate->diffInDays($endDate) + 1);
    }

    public function getUnitRentalPriceAttribute(): float
    {
        return (float) ($this->product?->price_per_day ?? $this->product?->price ?? 0);
    }

    public function getDailyRentalTotalAttribute(): float
    {
        $quantity = max(1, (int) ($this->amount ?? 1));

        return $this->unit_rental_price * $quantity;
    }

    public function getRentalTotalAttribute(): float
    {
        return $this->daily_rental_total * $this->rental_days;
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'id_product');
    }

    public function detailBookProducts()
    {
        return $this->hasMany(DetailBookProduct::class, 'id_book_product');
    }

    public function detailBookProduct()
    {
        return $this->hasOne(DetailBookProduct::class, 'id_book_product');
    }

    public function payments()
    {
        return $this->morphMany(Payment::class, 'payable');
    }
}
