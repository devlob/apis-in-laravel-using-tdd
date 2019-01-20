<?php

namespace App\Utopia\Repositories\Interfaces;

use App\Product;
use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;

interface ProductRepoInterface
{
    public function create(ProductStoreRequest $request);

    public function update(ProductUpdateRequest $request, Product $product);

    public function delete(Product $product);
}
