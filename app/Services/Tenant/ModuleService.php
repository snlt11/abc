<?php

namespace App\Services\Tenant;

use App\Repositories\Contracts\ModuleRepositoryInterface;
use App\Repositories\Contracts\PermissionRepositoryInterface;
use Illuminate\Support\Facades\DB;

class ModuleService
{
    protected $moduleRepository;
    protected $permissionRepository;

    public function __construct(
        ModuleRepositoryInterface $moduleRepository,
        PermissionRepositoryInterface $permissionRepository
    ) {
        $this->moduleRepository = $moduleRepository;
        $this->permissionRepository = $permissionRepository;
    }

    /**
     * Create a new module
     *
     * @param  array  $data
     * @return \App\Models\Module
     * @throws \Exception
     */
    public function createModule(array $data)
    {
        return DB::transaction(function () use ($data) {
            return $this->moduleRepository->create($data);
        });
    }

    /**
     * Update an existing module
     *
     * @param  array  $data
     * @return \App\Models\Module|null
     * @throws \Exception
     */
    public function updateModule(array $data)
    {
        if (!isset($data['id'])) {
            throw new \InvalidArgumentException('Module ID is required');
        }
        
        return DB::transaction(function () use ($data) {
            return $this->moduleRepository->update($data['id'], $data);
        });
    }

    /**
     * Delete a module
     *
     * @param  string|int  $id
     * @return bool
     * @throws \Exception
     */
    public function deleteModule($id)
    {
        return DB::transaction(function () use ($id) {
            return $this->moduleRepository->delete($id);
        });
    }

    /**
     * Get a module by ID
     *
     * @param  string|int  $id
     * @return \App\Models\Module
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getModule($id)
    {
        $module = $this->moduleRepository->findById($id);
        
        if (!$module) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException("Module not found");
        }
        
        return $module->load('permission');
    }

    /**
     * Get all modules with pagination and filtering
     *
     * @param  array  $filters
     * @param  int  $perPage
     * @param  int  $page
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAllModules(array $filters = [], int $perPage = 10, int $page = 1)
    {
        return $this->moduleRepository->getAll($filters, $perPage, $page);
    }
    
    /**
     * Get modules with optional filtering
     * 
     * @param  array  $filters
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getModules(array $filters = [])
    {
        return $this->moduleRepository->getAll($filters);
    }

    public function getModulesByPermission($permissionId)
    {
        return $this->moduleRepository->getByPermissionId($permissionId);
    }

    /**
     * Sync modules for a permission
     *
     * @param  string|int  $permissionId
     * @param  array  $modules
     * @return \Illuminate\Database\Eloquent\Collection
     * @throws \Exception
     */
    public function syncPermissionModules($permissionId, array $modules)
    {
        return DB::transaction(function () use ($permissionId, $modules) {
            $permission = $this->permissionRepository->findById($permissionId);
            if (!$permission) {
                throw new \Exception('Permission not found');
            }

            $this->moduleRepository->deleteByPermissionId($permissionId);
            
            if (!empty($modules)) {
                $this->moduleRepository->createMany(
                    array_map(fn($module) => array_merge($module, [
                        'permission_id' => $permissionId,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]), $modules)
                );
            }
            
            return $this->moduleRepository->getByPermissionId($permissionId);
        });
    }
}
