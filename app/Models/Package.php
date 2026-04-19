<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $id
 * @property string $name_package
 * @property string|null $description
 * @property int|null $duration_days
 * @property int|null $rental_duration
 * @property int|float|null $price
 * @property int|float|null $upsell
 * @property \Illuminate\Support\Carbon|null $start_publish
 * @property \Illuminate\Support\Carbon|null $end_publish
 * @property string|null $image
 */
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
        return $this->belongsToMany(Product::class, 'package_products', 'id_package', 'id_product')
            ->withPivot('qty')
            ->withTimestamps();
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
