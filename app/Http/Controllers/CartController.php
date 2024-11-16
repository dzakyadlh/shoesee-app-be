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
            $cart = Cart::where('user_id', $user->id)->first();

            if (!$cart) {
                return ResponseFormatter::error(
                    null,
                    'Cart not found',
                    404
                );
            }

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
                'quantity' => 'required|integer|min:1',
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
                    $item['quantity'] += $request->quantity; // Update quantity if product exists
                    $productExists = true;
                    break;
                }
            }

            // If product doesn't exist, add it to the cart
            if (!$productExists) {
                $cartProducts[] = [
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'quantity' => $request->quantity,
                    'gallery' => $product->gallery,
                ];
            }

            // Save the updated cart
            $cart->cart_products = $cartProducts;
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
    public function remove(Request $request, $productId)
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

            // Remove product from cart
            $cart->cart_products = array_filter($cart->cart_products, function ($item) use ($productId) {
                return $item['product_id'] != $productId;
            });

            // Save the updated cart
            $cart->save();

            return ResponseFormatter::success(
                $cart,
                'Cart removed successfully'
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
