<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'id_product',
        'serial_number',
        'status',
        'notes',
        'last_maintenance_at',
    ];

    protected $casts = [
        'last_maintenance_at' => 'datetime',
    ];

    /**
     * Get the product that owns this unit
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'id_product');
    }

    /**
     * Get all bookings using this unit
     */
    public function bookPackageProducts()
    {
        return $this->hasMany(BookPackageProduct::class, 'id_unit');
    }

    /**
     * Scope untuk unit yang available
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    /**
     * Scope untuk unit berdasarkan product
     */
    public function scopeForProduct($query, $productId)
    {
        return $query->where('id_product', $productId);
    }

    /**
     * Lock unit untuk booking
     */
    public function lock()
    {
        $this->update(['status' => 'booked']);
    }

    /**
     * Release unit kembali ke available
     */
    public function release()
    {
        $this->update(['status' => 'available']);
    }
}
