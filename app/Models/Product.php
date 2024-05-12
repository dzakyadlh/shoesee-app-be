<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'price',
        'description',
        'tags',
        'categories_id',
    ];

    public function gallery()
    {
        return $this->hasMany(ProductGallery::class, 'product_id', 'id');
    }

    public function categories()
    {
        return $this->belongsTo(ProductCategory::class, 'categories_id', 'id');
    }
}
