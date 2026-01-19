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
        'name',
        'desc',
        'status',
        'price',
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'id_admins');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'id_category');
    }

    public function packages()
    {
        return $this->belongsToMany(Package::class, 'package_products', 'id_product', 'id_package');
    }
}
