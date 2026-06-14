<?php

namespace App\Repositories;

use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Contracts\Pagination\CursorPaginator;

class ProductRepository implements ProductRepositoryInterface
{
    public function paginate(array $filters, int $perPage): CursorPaginator
    {
        $query = Product::with(['category', 'suppliers'])
            ->select(['id', 'category_id', 'name', 'sku', 'description', 'price', 'stock_quantity', 'is_active', 'created_at', 'updated_at', 'deleted_at'])
            ->active();

        if (! empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (isset($filters['price_min'])) {
            $query->where('price', '>=', $filters['price_min']);
        }

        if (isset($filters['price_max'])) {
            $query->where('price', '<=', $filters['price_max']);
        }

        if (! empty($filters['stock_max'])) {
            $query->where('stock_quantity', '<=', $filters['stock_max']);
        }

        if (! empty($filters['low_stock'])) {
            $query->lowStock();
        }

        return $query->cursorPaginate($perPage);
    }

    public function findWithRelations(int $id): Product
    {
        return Product::with(['category', 'suppliers'])->findOrFail($id);
    }

    public function findTrashed(int $id): Product
    {
        return Product::withTrashed()->findOrFail($id);
    }

    public function create(array $data): Product
    {
        return Product::create($data);
    }

    public function update(Product $product, array $data): Product
    {
        $product->update($data);

        return $product;
    }

    public function delete(Product $product): void
    {
        $product->delete();
    }

    public function restore(Product $product): void
    {
        $product->restore();
    }
}
