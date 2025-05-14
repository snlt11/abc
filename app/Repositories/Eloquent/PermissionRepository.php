<?php

namespace App\Repositories\Eloquent;

use App\Models\Permission;
use App\Repositories\Contracts\ModuleRepositoryInterface as ModuleRepositoryContract;
use App\Repositories\Contracts\PermissionRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class PermissionRepository implements PermissionRepositoryInterface
{
    protected $model;

    protected $moduleRepository;

    public function __construct(
        Permission $model,
        ModuleRepositoryContract $moduleRepository
    ) {
        $this->model = $model;
        $this->moduleRepository = $moduleRepository;
    }
    
    public function model(): Model
    {
        return $this->model;
    }

    public function create(array $data): Permission
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

    public function update(string $id, array $data): ?Permission
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
            throw $e;
        }
    }

    public function findById(string $id): ?Permission
    {
        return $this->model->find($id);
    }

    public function getAll(array $filters = [], int $perPage = 10, int $page = 1)
    {
        $query = $this->model->with('modules');

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    public function createWithModules(array $data): Permission
    {
        try {
            return DB::transaction(function () use ($data) {
                $moduleIds = $data['module_ids'] ?? [];
                unset($data['module_ids']);

                $permission = $this->create($data);

                if (!empty($moduleIds)) {
                    $permission->modules()->attach($moduleIds);
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

    public function updateWithModules(string $id, array $data): Permission
    {
        try {
            return DB::transaction(function () use ($id, $data) {
                $moduleIds = $data['module_ids'] ?? [];
                unset($data['module_ids']);

                $permission = $this->update($id, $data);

                if ($permission) {
                    $permission->modules()->sync($moduleIds);
                    return $permission->load('modules');
                }

                throw new \Exception("Permission not found");
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

    public function deleteWithModules(string $id): bool
    {
        try {
            return DB::transaction(function () use ($id) {
                $permission = $this->findById($id);
                if ($permission) {
                    $permission->modules()->detach();
                    return $permission->delete();
                }
                return false;
            });
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to delete permission with modules', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}