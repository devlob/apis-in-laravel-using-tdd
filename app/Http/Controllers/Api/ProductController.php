<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductCollection;
use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Http\Resources\Product as ProductResource;
use App\Utopia\Repositories\Interfaces\ProductRepoInterface;

class ProductController extends Controller
{
    protected $productRepo;

    public function __construct(ProductRepoInterface $productRepo)
    {
        $this->productRepo = $productRepo;
    }

    public function index()
    {
        try {
            return new ProductCollection($this->productRepo->paginate());
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function store(ProductStoreRequest $request)
    {
        try {
            DB::beginTransaction();

            $product = $this->productRepo->create($request);

            DB::commit();

            return response()->json(new ProductResource($product), 201);
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function show(int $id)
    {
        $product = $this->productRepo->findOrFail($id);

        try {
            return response()->json(new ProductResource($product));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function update(ProductUpdateRequest $request, int $id)
    {
        $product = $this->productRepo->findOrFail($id);

        try {
            DB::beginTransaction();

            $product = $this->productRepo->update($request, $product);

            DB::commit();

            return response()->json(new ProductResource($product));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy(int $id)
    {
        $product = $this->productRepo->findOrFail($id);

        try {
            DB::beginTransaction();

            $this->productRepo->delete($product);

            DB::commit();

            return response()->json(null, 204);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
