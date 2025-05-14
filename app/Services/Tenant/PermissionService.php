<?php

namespace App\Services\Tenant;

use App\Enums\AccessScope;
use App\Enums\Module as ModuleEnum;
use App\Repositories\Contracts\PermissionRepositoryInterface;
use App\Repositories\Contracts\ModuleRepositoryInterface;
use Illuminate\Support\Facades\DB;

class PermissionService
{
    protected $permissionRepository;
    protected $moduleRepository;

    public function __construct(
        PermissionRepositoryInterface $permissionRepository,
        ModuleRepositoryInterface $moduleRepository
    ) {
        $this->permissionRepository = $permissionRepository;
        $this->moduleRepository = $moduleRepository;
    }

    public function createPermission(array $data)
    {
        return $this->permissionRepository->createWithModules($data);
    }

    public function updatePermission(array $data)
    {
        return $this->permissionRepository->updateWithModules($data['id'], $data);
    }

    public function deletePermission($id)
    {
        return $this->permissionRepository->delete($id);
    }

    public function getPermission($id)
    {
        $permission = $this->permissionRepository->findById($id);
        
        if (!$permission) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException("Permission not found");
        }
        
        return $permission->load('modules');
    }

    public function getAllPermissionsQuery(array $filters = [])
    {
        $query = $this->permissionRepository->model()
            ->with('modules')
            ->when(isset($filters['search']), function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('description', 'like', '%' . $filters['search'] . '%');
            });
            
        return $query;
    }
    
    public function getAllPermissions(array $filters = [])
    {
        $perPage = $filters['limit'] ?? 10;
        $page = $filters['page'] ?? 1;
        
        unset($filters['limit'], $filters['page']);
        
        return $this->permissionRepository->getAll($filters, $perPage, $page);
    }

    public function getPermissions(array $filters = [])
    {
        return $this->permissionRepository->getAll($filters);
    }
}
