<?php

namespace App\Http\Controllers\Api;

use App\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductCollection;
use App\Http\Requests\ProductStoreRequest;
use App\Http\Resources\Product as ProductResource;

class ProductController extends Controller
{
    public function index()
    {
        return new ProductCollection(Product::paginate());
    }

    public function store(ProductStoreRequest $request)
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
