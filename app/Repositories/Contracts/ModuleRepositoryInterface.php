<?php

namespace App\Repositories\Contracts;

use App\Models\Module;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface ModuleRepositoryInterface
{
    public function create(array $data): Module;
    
    public function update(string $id, array $data): ?Module;
    
    public function delete(string $id): bool;
    
    public function findById(string $id): ?Module;
    
    public function getAll(array $filters = [], int $perPage = 15, int $page = 1): LengthAwarePaginator;
    
    public function createMany(array $modules): bool;

    public function deleteByPermissionId(string $permissionId): bool;

    public function getByPermissionId(string $permissionId): Collection;
}
