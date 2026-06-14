<?php

namespace App\Repositories\Contracts;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

interface CategoryRepositoryInterface
{
    public function all(): Collection;

    public function find(int $id): Category;

    public function create(array $data): Category;

    public function update(Category $category, array $data): Category;

    public function delete(Category $category): void;
}
