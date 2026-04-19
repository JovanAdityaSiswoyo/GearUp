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
        'item_status' => ItemStatus::class,
        'order_status' => OrderStatus::class,
    ];

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
