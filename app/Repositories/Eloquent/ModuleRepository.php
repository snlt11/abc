<?php

namespace App\Repositories\Eloquent;

use App\Models\Module;
use App\Repositories\Contracts\ModuleRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ModuleRepository implements ModuleRepositoryInterface
{
    protected $model;

    public function __construct(Module $model)
    {
        $this->model = $model;
    }

    /**
     * Create a new module
     *
     * @param  array  $data  Module data
     * @return \App\Models\Module
     * @throws \Exception
     */
    public function create(array $data): \App\Models\Module
    {
        try {
            return $this->model->create($data);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to create module', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Update an existing module
     *
     * @param  string  $id  Module ID
     * @param  array  $data  Module data
     * @return \App\Models\Module|null
     * @throws \Exception
     */
    public function update(string $id, array $data): ?\App\Models\Module
    {
        try {
            $module = $this->findById($id);
            if ($module) {
                $module->update($data);
                return $module->fresh();
            }
            return null;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to update module', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Delete a module
     *
     * @param  string  $id  Module ID
     * @return bool  True if deleted, false otherwise
     */
    public function delete(string $id): bool
    {
        try {
            $module = $this->findById($id);
            if ($module) {
                return $module->delete();
            }
            return false;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to delete module', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Find a module by ID
     *
     * @param  string  $id
     * @return \App\Models\Module|null
     */
    public function findById(string $id): ?\App\Models\Module
    {
        return $this->model->find($id);
    }

    /**
     * Get all modules with optional filtering and pagination
     *
     * @param  array  $filters
     * @param  int  $perPage  Number of items per page (default: 15)
     * @param  int  $page  Page number (default: 1)
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAll(array $filters = [], int $perPage = 15, int $page = 1): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = $this->model->newQuery();
        
        // Apply filters
        if (isset($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', "%{$filters['search']}%")
                  ->orWhere('display_name', 'like', "%{$filters['search']}%");
            });
        }
        
        if (isset($filters['permission_id'])) {
            $query->where('permission_id', $filters['permission_id']);
        }
        
        // Apply ordering
        if (isset($filters['orderBy']) && is_array($filters['orderBy'])) {
            foreach ($filters['orderBy'] as $field => $direction) {
                $query->orderBy($field, $direction);
            }
        } else {
            $query->latest('created_at');
        }
        
        // Always return paginated results for consistency
        // Use a reasonable default perPage if not specified
        $perPage = $perPage > 0 ? $perPage : 15;
        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * Create multiple modules at once
     *
     * @param  array  $modules  Array of module data arrays
     * @return bool  True if the modules were created successfully
     */
    public function createMany(array $modules): bool
    {
        try {
            return $this->model->insert($modules);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to create multiple modules', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Delete all modules associated with a permission
     *
     * @param  string  $permissionId  The ID of the permission
     * @return bool  True if modules were deleted, false otherwise
     */
    public function deleteByPermissionId(string $permissionId): bool
    {
        try {
            return $this->model->where('permission_id', $permissionId)->delete() > 0;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to delete modules by permission ID', [
                'permission_id' => $permissionId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Get all modules associated with a permission
     *
     * @param  string  $permissionId  The ID of the permission
     * @return \Illuminate\Database\Eloquent\Collection  Collection of modules
     */
    public function getByPermissionId(string $permissionId): \Illuminate\Database\Eloquent\Collection
    {
        return $this->model->where('permission_id', $permissionId)->get();
    }
}
