<?php

namespace App\Services;

use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Contracts\Pagination\CursorPaginator;

class ProductService
{
    public function __construct(private readonly ProductRepositoryInterface $repo) {}

    public function list(array $filters, int $perPage): CursorPaginator
    {
        return $this->repo->paginate($filters, $perPage);
    }

    public function find(int $id): Product
    {
        return $this->repo->findWithRelations($id);
    }

    public function create(array $data, array $supplierIds = []): Product
    {
        $product = $this->repo->create($data);

        if (! empty($supplierIds)) {
            $product->suppliers()->sync($supplierIds);
        }

        $product->load(['category', 'suppliers']);

        return $product;
    }

    public function update(Product $product, array $data, ?array $supplierIds): Product
    {
        $this->repo->update($product, $data);

        if ($supplierIds !== null) {
            $product->suppliers()->sync($supplierIds);
        }

        $product->load(['category', 'suppliers']);

        return $product;
    }

    public function delete(Product $product): void
    {
        $this->repo->delete($product);
    }

    public function restore(int $id): void
    {
        $product = $this->repo->findTrashed($id);
        $this->repo->restore($product);
    }
}
