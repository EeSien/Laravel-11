<?php

namespace App\Http\Controllers;

use App\Helpers\Response;
use App\Http\Requests\StoreSupplierRequest;
use App\Http\Requests\UpdateSupplierRequest;
use App\Http\Resources\SupplierResource;
use App\Models\Supplier;
use App\Services\SupplierService;
use Illuminate\Http\JsonResponse;

class SupplierController extends Controller
{
    public function __construct(
        private readonly SupplierService $service,
        private readonly Response $response,
    ) {}

    public function index(): JsonResponse
    {
        return $this->response->success(SupplierResource::collection($this->service->all()));
    }

    public function store(StoreSupplierRequest $request): JsonResponse
    {
        $supplier = $this->service->create($request->validated());

        return $this->response->success(new SupplierResource($supplier), 'Supplier created successfully.', 201);
    }

    public function show(Supplier $supplier): JsonResponse
    {
        return $this->response->success(new SupplierResource($supplier));
    }

    public function update(UpdateSupplierRequest $request, Supplier $supplier): JsonResponse
    {
        return $this->response->success(new SupplierResource($this->service->update($supplier, $request->validated())));
    }

    public function destroy(Supplier $supplier): JsonResponse
    {
        $this->service->delete($supplier);

        return $this->response->success(null, 'Supplier deleted successfully.');
    }
}
