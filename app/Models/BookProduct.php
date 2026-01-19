<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookProduct extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'book_products';

    protected $fillable = [
        'id_user',
        'id_product',
        'book_code',
        'status',
        'checkin_appointment_start',
        'checkout_appointment_end',
        'amount',
        'booker_name',
        'booker_email',
        'booker_telp',
    ];

    protected $casts = [
        'checkin_appointment_start' => 'datetime',
        'checkout_appointment_end' => 'datetime',
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
}
