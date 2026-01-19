<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailBook extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'detail_books';

    protected $fillable = [
        'id_book',
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
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'shipping_date' => 'date',
        'rental_start_at' => 'datetime',
        'rental_end_at' => 'datetime',
    ];

    public function book()
    {
        return $this->belongsTo(Book::class, 'id_book');
    }
}
