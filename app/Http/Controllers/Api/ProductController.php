<?php

namespace App\Http\Controllers\Api;

use App\Product;
use App\Http\Resources\Product as ProductResource;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProductController extends Controller
{
    public function store(Request $request)
    {
        $product = Product::create([
            'name' => $request->name,
            'slug' => str_slug($request->name),
            'price' => $request->price
        ]);

        return response()->json(new ProductResource($product), 201);
    }

    public function show(int $id)
    {
        $product = Product::findOrfail($id);

        return response()->json(new ProductResource($product));
    }

    public function update(Request $request, int $id)
    {
        $product = Product::findOrfail($id);

        $product->update([
            'name' => $request->name,
            'slug' => str_slug($request->name),
            'price' => $request->price
        ]);

        return response()->json(new ProductResource($product));
    }

    public function destroy(int $id)
    {
        $product = Product::findOrfail($id);

        $product->delete();

        return response()->json(null, 204);
    }
}
