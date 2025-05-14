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

    /**
     * Get a permission by ID
     *
     * @param  string|int  $id
     * @return \App\Models\Permission
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getPermission($id)
    {
        $permission = $this->permissionRepository->findById($id);
        
        if (!$permission) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException("Permission not found");
        }
        
        return $permission->load('modules');
    }

    /**
     * Get all permissions with pagination and filtering
     *
     * @param  array  $filters
     * @param  int  $perPage
     * @param  int  $page
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAllPermissions(array $filters = [], int $perPage = 10, int $page = 1)
    {
        return $this->permissionRepository->getAll($filters, $perPage, $page);
    }

    /**
     * Get permissions with optional filtering
     * 
     * @param  array  $filters
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPermissions(array $filters = [])
    {
        return $this->permissionRepository->getAll($filters);
    }
}
