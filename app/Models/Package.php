<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name_package',
        'start_publish',
        'end_publish',
        'price_publish',
    ];

    protected $casts = [
        'start_publish' => 'datetime',
        'end_publish' => 'datetime',
        'price_publish' => 'datetime',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'package_products', 'id_package', 'id_product');
    }

    public function books()
    {
        return $this->hasMany(Book::class, 'id_package');
    }
}
