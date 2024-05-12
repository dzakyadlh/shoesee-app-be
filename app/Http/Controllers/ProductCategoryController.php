<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductCategory;
use App\helpers\ResponseFormatter;

class ProductCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $id =  $request->input('id');
        $limit =  $request->input('limit');
        $name =  $request->input('name');
        $show =  $request->input('show');

        if ($id) {
            $product = ProductCategory::with(['products'])->find('id');

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

        $category = ProductCategory::query();

        if ($name) {
            $category->where('name', 'like', '%' . $name . '%');
        }

        if ($show) {
            $category->with('products');
        }

        return ResponseFormatter::success(
            $category->paginate($limit),
            'Categories data fetched successfully',
        );
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductCategory $productCategory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductCategory $productCategory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProductCategory $productCategory)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductCategory $productCategory)
    {
        //
    }
}
