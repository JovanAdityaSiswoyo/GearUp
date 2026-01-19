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
        'name',
        'email',
        'telp',
        'price',
    ];

    public function bookProduct()
    {
        return $this->belongsTo(BookProduct::class, 'id_book_product');
    }
}
