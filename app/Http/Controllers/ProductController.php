<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\helpers\ResponseFormatter;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;


class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $id =  $request->input('id');
            $limit =  $request->input('limit', 6);
            $name =  $request->input('name');
            $description =  $request->input('description');
            $tags =  $request->input('tags');
            $categories =  $request->input('categories');

            $price_from =  $request->input('price_from');
            $price_to =  $request->input('price_to');

            if ($id) {
                $product = Product::with(['categories', 'gallery'])->find($id);

                if ($product) {
                    return ResponseFormatter::success(
                        $product,
                        'Data fetched successfully'
                    );
                } else {
                    return ResponseFormatter::error(
                        null,
                        'Data not found',
                        404
                    );
                }
            }

            $product = Product::with(['categories', 'gallery']);

            if ($name) {
                $product->where('name', 'like', '%' . $name . '%');
            }
            if ($description) {
                $product->where('description', 'like', '%' . $description . '%');
            }
            if ($tags) {
                $product->where('tags', 'like', '%' . $tags . '%');
            }
            if ($categories) {
                $product->where('categories', $categories);
            }
            if ($price_from) {
                $product->where('price', '>=', $price_from);
            }
            if ($price_to) {
                $product->where('price', '<=', $price_to);
            }

            return ResponseFormatter::success(
                $product->paginate($limit),
                'Product data fetched successfully',
            );
        } catch (\Exception $e) {
            return ResponseFormatter::error(
                null,
                'Internal Server Error: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        //
    }
}
