<?php

namespace App\Repositories\Contracts;

use App\Models\Permission;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface PermissionRepositoryInterface
{
    /**
     * Create a new permission
     *
     * @param  array  $data  Permission data
     * @return \App\Models\Permission
     * @throws \Exception
     */
    public function create(array $data): \App\Models\Permission;
    
    /**
     * Update an existing permission
     *
     * @param  string  $id  Permission ID
     * @param  array  $data  Permission data
     * @return \App\Models\Permission|null
     * @throws \Exception
     */
    public function update(string $id, array $data): ?\App\Models\Permission;
    
    /**
     * Delete a permission
     *
     * @param  string  $id  Permission ID
     * @return bool  True if deleted, false otherwise
     */
    public function delete(string $id): bool;
    
    /**
     * Find a permission by ID
     *
     * @param  string  $id  Permission ID
     * @return \App\Models\Permission|null
     */
    public function findById(string $id): ?\App\Models\Permission;
    
    /**
     * Get all permissions with optional filtering and pagination
     *
     * @param  array  $filters
     * @param  int  $perPage
     * @param  int  $page
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
     */
    public function getAll(array $filters = [], int $perPage = 15, int $page = 1);
    
    /**
     * Create a new permission with associated modules
     *
     * @param  array  $data  Permission data including modules
     * @return \App\Models\Permission
     * @throws \Exception
     */
    public function createWithModules(array $data): \App\Models\Permission;
    
    /**
     * Update an existing permission with associated modules
     *
     * @param  string  $id  Permission ID
     * @param  array  $data  Permission data including modules
     * @return \App\Models\Permission
     * @throws \Exception
     */
    public function updateWithModules(string $id, array $data): \App\Models\Permission;
}
