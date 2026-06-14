<?php

namespace App\Http\Controllers;

use App\Helpers\Response;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(
        private readonly ProductService $service,
        private readonly Response $response,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['category_id', 'price_min', 'price_max', 'stock_max', 'low_stock']);
        $products = $this->service->list($filters, $request->integer('per_page', 15));

        return $this->response->success(ProductResource::collection($products));
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = $this->service->create(
            $request->safe()->except('supplier_ids'),
            $request->input('supplier_ids', [])
        );

        return $this->response->success(new ProductResource($product), 'Product created successfully.', 201);
    }

    public function show(Product $product): JsonResponse
    {
        $product->load(['category', 'suppliers']);

        return $this->response->success(new ProductResource($product));
    }

    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        $supplierIds = $request->has('supplier_ids') ? $request->input('supplier_ids', []) : null;

        $product = $this->service->update(
            $product,
            $request->safe()->except('supplier_ids'),
            $supplierIds
        );

        return $this->response->success(new ProductResource($product));
    }

    public function destroy(Product $product): JsonResponse
    {
        $this->service->delete($product);

        return $this->response->success(null, 'Product deleted successfully.');
    }

    public function restore(int $id): JsonResponse
    {
        $this->service->restore($id);

        return $this->response->success(null, 'Product restored successfully.');
    }
}
