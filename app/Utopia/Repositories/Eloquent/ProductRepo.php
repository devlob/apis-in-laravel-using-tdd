<?php

namespace App\Utopia\Repositories\Eloquent;

use App\Image;
use App\Product;
use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Utopia\Repositories\Interfaces\ProductRepoInterface;

class ProductRepo extends AbstractRepo implements ProductRepoInterface
{
    public function __construct()
    {
        parent::__construct('Product');
    }

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

    public function update(ProductUpdateRequest $request, $product)
    {
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('product_images', 'public');

            $imageId = Image::create([
                'path' => $path,
            ])->id;
        } else {
            $imageId = $product->image_id;
        }

        $product->update([
            'image_id' => $imageId,
            'name' => $request->name,
            'slug' => str_slug($request->name),
            'price' => $request->price,
        ]);

        return $product;
    }

    public function delete($product)
    {
        $product->delete();
    }
}
