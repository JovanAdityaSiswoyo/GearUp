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
        'name',
        'email',
        'telp',
        'price',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function book()
    {
        return $this->belongsTo(Book::class, 'id_book');
    }
}
