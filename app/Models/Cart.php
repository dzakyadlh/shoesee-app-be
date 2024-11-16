<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_products', // JSON field to store cart items
        'user_id',
    ];

    protected $casts = [
        'cart_products' => 'array', // Cast to array automatically
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'product_id', 'id');
    }

    // Optionally, you can add a method to calculate the total price of the cart
    public function calculateTotal()
    {
        $total = 0;
        foreach ($this->cart_products as $product) {
            $total += $product['price'] * $product['quantity']; // Assuming each cart product has 'price' and 'quantity'
        }
        return $total;
    }
}
