<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailBookProduct extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'detail_book_products';

    protected $fillable = [
        'id_book_product',
        'full_name',
        'instagram_handle',
        'other_socials',
        'phone_number',
        'emergency_phone_number',
        'shipping_method',
        'renter_address',
        'shipping_date',
        'rental_start_at',
        'rental_end_at',
        'identity_document_path',
    ];

    protected $casts = [
        'shipping_date' => 'date',
        'rental_start_at' => 'datetime',
        'rental_end_at' => 'datetime',
    ];

    public function bookProduct()
    {
        return $this->belongsTo(BookProduct::class, 'id_book_product');
    }
}
