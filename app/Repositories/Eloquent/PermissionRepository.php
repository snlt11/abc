<?php

namespace App\Repositories\Eloquent;

use App\Models\Permission;
use App\Repositories\Contracts\ModuleRepositoryInterface as ModuleRepositoryContract;
use App\Repositories\Contracts\PermissionRepositoryInterface;
use Illuminate\Support\Facades\DB;

class PermissionRepository implements PermissionRepositoryInterface
{
    /**
     * The permission model instance
     *
     * @var \App\Models\Permission
     */
    protected $model;

    /**
     * The module repository instance
     *
     * @var \App\Repositories\Contracts\ModuleRepositoryInterface|ModuleRepositoryContract
     */
    protected $moduleRepository;

    /**
     * Create a new repository instance
     *
     * @param  \App\Models\Permission  $model
     * @param  ModuleRepositoryContract  $moduleRepository
     * @return void
     */
    public function __construct(
        Permission $model,
        ModuleRepositoryContract $moduleRepository
    ) {
        $this->model = $model;
        $this->moduleRepository = $moduleRepository;
    }

    /**
     * Create a new permission
     *
     * @param  array  $data  Permission data
     * @return \App\Models\Permission
     * @throws \Exception
     */
    public function create(array $data): \App\Models\Permission
    {
        try {
            return $this->model->create($data);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to create permission', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Update an existing permission
     *
     * @param  string  $id  Permission ID
     * @param  array  $data  Permission data
     * @return \App\Models\Permission|null
     * @throws \Exception
     */
    public function update(string $id, array $data): ?\App\Models\Permission
    {
        try {
            $permission = $this->findById($id);
            if ($permission) {
                $permission->update($data);
                return $permission->fresh();
            }
            return null;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to update permission', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Delete a permission
     *
     * @param  string  $id  Permission ID
     * @return bool  True if deleted, false otherwise
     */
    public function delete(string $id): bool
    {
        try {
            $permission = $this->findById($id);
            if ($permission) {
                return $permission->delete();
            }
            return false;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to delete permission', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Find a permission by ID
     *
     * @param  string  $id  Permission ID
     * @return \App\Models\Permission|null
     */
    public function findById(string $id): ?\App\Models\Permission
    {
        return $this->model->find($id);
    }

    /**
     * Get all permissions with optional filtering and pagination
     *
     * @param  array  $filters
     * @param  int  $perPage
     * @param  int  $page
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
     */
    public function getAll(array $filters = [], int $perPage = 15, int $page = 1)
    {
        $query = $this->model->newQuery();
        
        // Apply filters
        if (isset($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', "%{$filters['search']}%")
                  ->orWhere('display_name', 'like', "%{$filters['search']}%");
            });
        }
        
        if (isset($filters['is_default'])) {
            $query->where('is_default', $filters['is_default']);
        }
        
        // Apply ordering
        if (isset($filters['orderBy']) && is_array($filters['orderBy'])) {
            foreach ($filters['orderBy'] as $field => $direction) {
                $query->orderBy($field, $direction);
            }
        } else {
            $query->latest('created_at');
        }
        
        // Return paginated results
        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * Create a new permission with associated modules
     *
     * @param  array  $data  Permission data including modules
     * @return \App\Models\Permission
     * @throws \Exception
     */
    public function createWithModules(array $data): \App\Models\Permission
    {
        try {
            return DB::transaction(function () use ($data) {
                $modules = $data['modules'] ?? [];
                unset($data['modules']);
                
                $permission = $this->create($data);
                
                if (!empty($modules)) {
                    $this->moduleRepository->deleteByPermissionId($permission->id);
                    $this->moduleRepository->createMany(
                        array_map(fn($module) => array_merge($module, [
                            'permission_id' => $permission->id,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]), $modules)
                    );
                }
                
                return $permission->load('modules');
            });
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to create permission with modules', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
    
    /**
     * Update an existing permission with associated modules
     *
     * @param  string  $id  Permission ID
     * @param  array  $data  Permission data including modules
     * @return \App\Models\Permission
     * @throws \Exception
     */
    public function updateWithModules(string $id, array $data): \App\Models\Permission
    {
        try {
            return DB::transaction(function () use ($id, $data) {
                $modules = $data['modules'] ?? [];
                unset($data['modules']);
                
                $permission = $this->update($id, $data);
                
                if ($permission === null) {
                    throw new \Exception("Permission not found");
                }
                
                // Always update modules to handle both additions and removals
                $this->moduleRepository->deleteByPermissionId($permission->id);
                
                if (!empty($modules)) {
                    $this->moduleRepository->createMany(
                        array_map(fn($module) => array_merge($module, [
                            'permission_id' => $permission->id,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]), $modules)
                    );
                }
                
                return $permission->load('modules');
            });
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to update permission with modules', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
}
