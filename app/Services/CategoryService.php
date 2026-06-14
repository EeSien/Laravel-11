<?php

namespace App\Services;

use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CategoryService
{
    public function __construct(private readonly CategoryRepositoryInterface $repo) {}

    public function all(): Collection
    {
        return $this->repo->all();
    }

    public function find(int $id): Category
    {
        return $this->repo->find($id);
    }

    public function create(array $data): Category
    {
        return $this->repo->create($data);
    }

    public function update(Category $category, array $data): Category
    {
        return $this->repo->update($category, $data);
    }

    public function delete(Category $category): void
    {
        $this->repo->delete($category);
    }
}
