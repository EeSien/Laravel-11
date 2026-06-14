<?php

namespace App\Services;

use App\Models\Supplier;
use App\Repositories\Contracts\SupplierRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class SupplierService
{
    public function __construct(private readonly SupplierRepositoryInterface $repo) {}

    public function all(): Collection
    {
        return $this->repo->all();
    }

    public function find(int $id): Supplier
    {
        return $this->repo->find($id);
    }

    public function create(array $data): Supplier
    {
        return $this->repo->create($data);
    }

    public function update(Supplier $supplier, array $data): Supplier
    {
        return $this->repo->update($supplier, $data);
    }

    public function delete(Supplier $supplier): void
    {
        $this->repo->delete($supplier);
    }
}
