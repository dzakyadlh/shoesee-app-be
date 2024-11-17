<?php

namespace App\Http\Controllers;

use App\helpers\ResponseFormatter;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index(Request $request)
    {
        try {
            $user = $request->user();
            $wishlist = Wishlist::firstOrCreate(
                ['user_id' => $user->id],
                ['wishlists' => []]
            );

            return ResponseFormatter::success(
                $wishlist,
                'Wishlist fetched successfully'
            );
        } catch (\Throwable $e) {
            return ResponseFormatter::error(
                null,
                'Internal Server Error: ' . $e->getMessage(),
                500
            );
        }
    }

    public function add(Request $request, $productId)
    {
        $validated = validator(['product_id' => $productId], [
            'product_id' => 'required|integer|exists:products,id',
        ])->validate();

        try {
            $user = $request->user();
            $product = Product::findOrFail($productId);

            $wishlist = Wishlist::firstOrCreate(
                ['user_id' => $user->id],
                ['wishlists' => []]
            );

            $userWishlists = $wishlist->wishlists ?? [];

            if (collect($userWishlists)->contains('product_id', $product->id)) {
                return ResponseFormatter::error(
                    null,
                    'Product already wishlisted',
                    400
                );
            }

            $userWishlists[] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'gallery' => $product->gallery,
            ];

            // Reassign the modified array to the model
            $wishlist->wishlists = $userWishlists;
            $wishlist->save();

            return ResponseFormatter::success(
                $wishlist,
                'Product added to wishlist successfully'
            );
        } catch (\Throwable $e) {
            return ResponseFormatter::error(
                null,
                'Internal Server Error: ' . $e->getMessage(),
                500
            );
        }
    }

    public function remove(Request $request, $productId)
    {
        $validated = validator(['product_id' => $productId], [
            'product_id' => 'required|integer|exists:products,id',
        ])->validate();

        try {
            $user = $request->user();
            $wishlist = Wishlist::firstOrCreate(
                ['user_id' => $user->id],
                ['wishlists' => []]
            );

            $userWishlists = $wishlist->wishlists ?? [];

            // Find the index of the product in the wishlist
            $productIndex = collect($userWishlists)->search(function ($item) use ($productId) {
                return $item['product_id'] == $productId;
            });

            // If the product is not found in the wishlist
            if ($productIndex === false) {
                return ResponseFormatter::error(
                    null,
                    'Product not found in wishlist',
                    404
                );
            }

            // Remove the product from the wishlist
            unset($userWishlists[$productIndex]);

            // Reindex the array to avoid gaps
            $userWishlists = array_values($userWishlists);

            // Reassign the modified array to the model
            $wishlist->wishlists = $userWishlists;
            $wishlist->save();

            return ResponseFormatter::success(
                $wishlist,
                'Product removed from wishlist successfully'
            );
        } catch (\Throwable $e) {
            return ResponseFormatter::error(
                null,
                'Internal Server Error: ' . $e->getMessage(),
                500
            );
        }
    }
}
