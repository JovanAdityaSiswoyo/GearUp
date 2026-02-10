<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookPackageProduct extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id', 'id_book', 'id_product', 'id_unit', 'qty', 'is_packed', 'packed_at', 'packed_by'
    ];

    protected $casts = [
        'is_packed' => 'boolean',
        'packed_at' => 'datetime',
    ];

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class, 'id_book', 'id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'id_product', 'id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'id_unit', 'id');
    }

    public function packedByOfficer(): BelongsTo
    {
        return $this->belongsTo(Officer::class, 'packed_by', 'id');
    }
}
