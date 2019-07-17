<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Http\Resources\Product as ProductResource;
use App\Product;

class ProductController extends Controller
{
    public function index()
    {
        $products = ProductResource::collection(Product::latest()->get());

        return response()->json($products);
    }

    public function store(ProductRequest $request)
    {
        $product = Product::create(
            $request->only(['photo', 'price', 'category_id'])
        );

        foreach ($request->input('translations') as $translation) {
            $product->translations()->create($translation);
        }

        return response()->json($product, 201);
    }

    public function show($id)
    {
        $product = Product::findOrFail($id);

        return response()->json($product);
    }

    public function update(ProductRequest $request, $id)
    {
        $product = Product::findOrFail($id);
        $product->update($request->all());

        return response()->json($product, 200);
    }

    public function destroy($id)
    {
        Product::destroy($id);

        return response()->json(null, 204);
    }
}
