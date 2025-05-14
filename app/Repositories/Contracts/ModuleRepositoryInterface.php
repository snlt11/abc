<?php

namespace App\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface ModuleRepositoryInterface
{
    /**
     * Create a new module
     *
     * @param  array  $data  Module data
     * @return \App\Models\Module
     * @throws \Exception
     */
    public function create(array $data): \App\Models\Module;
    
    /**
     * Update an existing module
     *
     * @param  string  $id  Module ID
     * @param  array  $data  Module data
     * @return \App\Models\Module|null
     * @throws \Exception
     */
    public function update(string $id, array $data): ?\App\Models\Module;
    
    /**
     * Delete a module
     *
     * @param  string  $id  Module ID
     * @return bool  True if deleted, false otherwise
     */
    public function delete(string $id): bool;
    
    /**
     * Find a module by ID
     *
     * @param  string  $id
     * @return \App\Models\Module|null
     */
    public function findById(string $id): ?\App\Models\Module;
    
    /**
     * Get all modules with optional filtering and pagination
     *
     * @param  array  $filters
     * @param  int  $perPage  Number of items per page (default: 15)
     * @param  int  $page  Page number (default: 1)
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAll(array $filters = [], int $perPage = 15, int $page = 1): \Illuminate\Contracts\Pagination\LengthAwarePaginator;
    
    /**
     * Create multiple modules at once
     *
     * @param  array  $modules  Array of module data arrays
     * @return bool  True if the modules were created successfully
     */
    public function createMany(array $modules): bool;

    /**
     * Delete all modules associated with a permission
     *
     * @param  string  $permissionId  The ID of the permission
     * @return bool  True if modules were deleted, false otherwise
     */
    public function deleteByPermissionId(string $permissionId): bool;

    /**
     * Get all modules associated with a permission
     *
     * @param  string  $permissionId  The ID of the permission
     * @return \Illuminate\Database\Eloquent\Collection  Collection of modules
     */
    public function getByPermissionId(string $permissionId): \Illuminate\Database\Eloquent\Collection;
}
