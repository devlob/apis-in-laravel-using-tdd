<?php

namespace App\Utopia\Repositories\Eloquent;

use App\Product;
use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Utopia\Repositories\Interfaces\ProductRepoInterface;

class ProductRepo implements ProductRepoInterface
{
    public function create(ProductStoreRequest $request)
    {
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('product_images', 'public');

            $imageId = Image::create([
                'path' => $path,
            ])->id;
        } else {
            $imageId = null;
        }

        return Product::create([
            'image_id' => $imageId,
            'name' => $request->name,
            'slug' => str_slug($request->name),
            'price' => $request->price,
        ]);
    }

    public function update(ProductUpdateRequest $request, Product $product)
    {
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('product_images', 'public');

            $imageId = Image::create([
                'path' => $path,
            ])->id;
        } else {
            $imageId = $product->image_id;
        }

        return $product->update([
            'image_id' => $imageId,
            'name' => $request->name,
            'slug' => str_slug($request->name),
            'price' => $request->price,
        ]);
    }

    public function delete(Product $product)
    {
        $product->delete();
    }
}
