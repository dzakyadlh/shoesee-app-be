<?php

namespace App\Http\Controllers;

use App\helpers\ResponseFormatter;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    // Get the current user's cart
    public function index(Request $request)
    {
        try {
            $user = $request->user();
            $cart = Cart::firstOrCreate(
                ['user_id' => $user->id],
                ['cart_products' => []] // Set default value if a new cart is created
            );

            return ResponseFormatter::success(
                $cart,
                'Cart fetched successfully'
            );
        } catch (\Exception $e) {
            return ResponseFormatter::error(
                null,
                'Internal Server Error: ' . $e->getMessage(),
                500
            );
        }
    }

    // Add or update an item in the cart
    public function update(Request $request)
    {
        try {
            $request->validate([
                'product_id' => 'required|exists:products,id',
                'quantity' => 'required|integer', // No minimum limit, it could be +1 or -1
            ]);

            $user = $request->user();
            $product = Product::findOrFail($request->product_id);
            $cart = Cart::firstOrCreate(
                ['user_id' => $user->id],
                ['cart_products' => []] // Set default value if a new cart is created
            );

            // Get current cart products
            $cartProducts = $cart->cart_products ?? [];

            // Check if the product already exists in the cart
            $productExists = false;
            foreach ($cartProducts as &$item) {
                if ($item['product_id'] == $product->id) {
                    $item['quantity'] += $request->quantity; // Increase or decrease quantity
                    // Remove product from cart if the quantity is 0 or less
                    if ($item['quantity'] <= 0) {
                        $cartProducts = array_filter($cartProducts, function ($cartItem) use ($product) {
                            return $cartItem['product_id'] != $product->id;
                        });
                    }
                    $productExists = true;
                    break;
                }
            }

            // If the product doesn't exist, add it to the cart with the requested quantity
            if (!$productExists && $request->quantity > 0) {
                $cartProducts[] = [
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'quantity' => $request->quantity,
                    'gallery' => $product->gallery,
                ];
            }

            // Save the updated cart
            $cart->cart_products = array_values($cartProducts); // Re-index the array after filtering
            $cart->save();

            return ResponseFormatter::success(
                $cart,
                'Cart updated successfully'
            );
        } catch (\Exception $e) {
            return ResponseFormatter::error(
                null,
                'Internal Server Error: ' . $e->getMessage(),
                500
            );
        }
    }


    // Remove an item from the cart
    public function remove(Request $request)
    {
        try {
            $user = $request->user();
            $cart = Cart::where('user_id', $user->id)->first();

            if (!$cart) {
                return ResponseFormatter::error(
                    null,
                    'Cart not found',
                    404
                );
            }

            $cart->cart_products = []; // Reset the cart products
            $cart->save();

            return ResponseFormatter::success(
                $cart,
                'Cart has been reset successfully.'
            );
        } catch (\Exception $e) {
            return ResponseFormatter::error(
                null,
                'Internal Server Error: ' . $e->getMessage(),
                500
            );
        }
    }
}
