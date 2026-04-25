<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Payment;
use App\Enums\ItemStatus;
use App\Enums\OrderStatus;

class Book extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'id_package',
        'id_user',
        'book_code',
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
        'amount',
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

    public function package()
    {
        return $this->belongsTo(Package::class, 'id_package');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function bookPackageProducts()
    {
        return $this->hasMany(BookPackageProduct::class, 'id_book', 'id');
    }

    public function detailBooks()
    {
        return $this->hasMany(DetailBook::class, 'id_book');
    }

    public function detailBook()
    {
        return $this->hasOne(DetailBook::class, 'id_book');
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
