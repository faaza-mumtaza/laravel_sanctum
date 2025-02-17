<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id',
        'name',
        'description',
        'price',
        'stock',
        'image',
        'status',
        'criteria',
        'favorite',
    ];

    protected $casts = [
        'favorite' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
