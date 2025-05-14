<?php

namespace App\Repositories\Contracts;

use App\Models\Permission;
interface PermissionRepositoryInterface
{
    public function create(array $data): Permission;
    
    public function update(string $id, array $data): ?Permission;
    
    public function delete(string $id): bool;

    public function findById(string $id): ?Permission;
    
    public function getAll(array $filters = [], int $perPage = 15, int $page = 1);
    
    public function createWithModules(array $data): Permission;
    
    public function updateWithModules(string $id, array $data): Permission;
    
    public function model();
}
