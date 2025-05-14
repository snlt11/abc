<?php

namespace App\Repositories\Eloquent;

use App\Models\Module;
use App\Repositories\Contracts\ModuleRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ModuleRepository implements ModuleRepositoryInterface
{
    protected $model;

    public function __construct(Module $model)
    {
        $this->model = $model;
    }

    public function create(array $data): Module
    {
        try {
            return $this->model->create($data);
        } catch (\Exception $e) {
            logger()->error('Failed to create module', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    public function update(string $id, array $data): ?Module
    {
        try {
            $module = $this->findById($id);
            if ($module) {
                $module->update($data);
                return $module->fresh();
            }
            return null;
        } catch (\Exception $e) {
            logger()->error('Failed to update module', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function delete(string $id): bool
    {
        try {
            $module = $this->findById($id);
            if ($module) {
                return $module->delete();
            }
            return false;
        } catch (\Exception $e) {
            logger()->error('Failed to delete module', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    public function findById(string $id): ?Module
    {
        return $this->model->find($id);
    }

    public function getAll(array $filters = [], int $perPage = 15, int $page = 1): LengthAwarePaginator
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
        
        $perPage = $perPage > 0 ? $perPage : 15;
        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    public function createMany(array $modules): bool
    {
        try {
            return $this->model->insert($modules);
        } catch (\Exception $e) {
            logger()->error('Failed to create multiple modules', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    public function deleteByPermissionId(string $permissionId): bool
    {
        try {
            return $this->model->where('permission_id', $permissionId)->delete() > 0;
        } catch (\Exception $e) {
            logger()->error('Failed to delete modules by permission ID', [
                'permission_id' => $permissionId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    
    public function getByPermissionId(string $permissionId): Collection
    {
        return $this->model->where('permission_id', $permissionId)->get();
    }
}
