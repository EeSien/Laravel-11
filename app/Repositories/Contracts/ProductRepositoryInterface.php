<?php

namespace App\Repositories\Contracts;

use App\Models\Product;
use Illuminate\Contracts\Pagination\CursorPaginator;

interface ProductRepositoryInterface
{
    public function paginate(array $filters, int $perPage): CursorPaginator;

    public function findWithRelations(int $id): Product;

    public function findTrashed(int $id): Product;

    public function create(array $data): Product;

    public function update(Product $product, array $data): Product;

    public function delete(Product $product): void;

    public function restore(Product $product): void;
}
