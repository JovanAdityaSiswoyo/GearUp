<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'id_admins',
        'id_category',
        'brand_id',
        'name',
        'desc',
        'description',
        'status',
        'price',
        'price_per_day',
        'stock',
        'image',
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'id_admins');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'id_category');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function bookPackageProducts()
    {
        return $this->hasMany(BookPackageProduct::class, 'id_product', 'id');
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('order');
    }

    public function packages()
    {
        return $this->belongsToMany(Package::class, 'package_products', 'id_product', 'id_package');
    }
}
