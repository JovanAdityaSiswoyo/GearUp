<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Payment;

class Book extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'id_package',
        'id_user',
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


    public function package()
    {
        return $this->belongsTo(Package::class, 'id_package');
    }

    public function bookPackageProducts()
    {
        return $this->hasMany(BookPackageProduct::class, 'id_book', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function detailBooks()
    {
        return $this->hasMany(DetailBook::class, 'id_book');
    }

    public function detailBookProducts()
    {
        return $this->hasMany(DetailBookProduct::class, 'id_book_product');
    }

    public function payments()
    {
        return $this->morphMany(Payment::class, 'payable');
    }
}
