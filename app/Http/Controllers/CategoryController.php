<?php

namespace App\Http\Controllers;

use App\Helpers\Response;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    public function __construct(
        private readonly CategoryService $service,
        private readonly Response $response,
    ) {}

    public function index(): JsonResponse
    {
        return $this->response->success(CategoryResource::collection($this->service->all()));
    }

    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $category = $this->service->create($request->validated());

        return $this->response->success(new CategoryResource($category), 'Category created successfully.', 201);
    }

    public function show(Category $category): JsonResponse
    {
        return $this->response->success(new CategoryResource($category));
    }

    public function update(UpdateCategoryRequest $request, Category $category): JsonResponse
    {
        return $this->response->success(new CategoryResource($this->service->update($category, $request->validated())));
    }

    public function destroy(Category $category): JsonResponse
    {
        $this->service->delete($category);

        return $this->response->success(null, 'Category deleted successfully.');
    }
}
