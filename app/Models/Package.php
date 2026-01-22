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
        'description',
        'duration_days',
        'rental_duration',
        'price',
        'upsell',
        'start_publish',
        'end_publish',
        'image',
    ];

    protected $casts = [
        'start_publish' => 'datetime',
        'end_publish' => 'datetime',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'package_products', 'id_package', 'id_product');
    }

    public function images()
    {
        return $this->hasMany(PackageImage::class)->orderBy('order');
    }

    public function books()
    {
        return $this->hasMany(Book::class, 'id_package');
    }
}
