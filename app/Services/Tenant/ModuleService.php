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

    public function createModule(array $data)
    {
        return DB::transaction(function () use ($data) {
            return $this->moduleRepository->create($data);
        });
    }

    public function updateModule(array $data)
    {
        if (!isset($data['id'])) {
            throw new \InvalidArgumentException('Module ID is required');
        }
        
        return DB::transaction(function () use ($data) {
            return $this->moduleRepository->update($data['id'], $data);
        });
    }

    public function deleteModule($id)
    {
        return DB::transaction(function () use ($id) {
            return $this->moduleRepository->delete($id);
        });
    }

    public function getModule($id)
    {
        $module = $this->moduleRepository->findById($id);
        
        if (!$module) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException("Module not found");
        }
        
        return $module->load('permission');
    }

    public function getAllModules(array $filters = [])
    {
        $perPage = $filters['limit'] ?? 10;
        $page = $filters['page'] ?? 1;
        
        unset($filters['limit'], $filters['page']);
        
        return $this->moduleRepository->getAll($filters, $perPage, $page);
    }
    
    public function getModules(array $filters = [])
    {
        return $this->moduleRepository->getAll($filters);
    }

    public function getModulesByPermission($permissionId)
    {
        return $this->moduleRepository->getByPermissionId($permissionId);
    }

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
