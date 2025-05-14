<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserRepository implements UserRepositoryInterface
{
    protected $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function getAll(array $filters = [])
    {
        $query = $this->model->newQuery();

        if (isset($filters['name'])) {
            $query->where('name', 'like', "%{$filters['name']}%");
        }

        $page = $filters['page'] ?? 1;
        $perPage = $filters['limit'] ?? $filters['per_page'] ?? 10;
        
        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    public function findById($id)
    {
        return $this->model->find($id);
    }

    public function findByEmail($email)
    {
        return $this->model->where('email', $email)->first();
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $user = $this->findById($id);
        
        if (!$user) {
            throw new ModelNotFoundException("User with ID {$id} not found");
        }
        
        $user->update($data);
        
        return $user->fresh();
    }

    public function delete($id)
    {
        $user = $this->findById($id);
        
        if (!$user) {
            throw new ModelNotFoundException("User with ID {$id} not found");
        }
        
        return $user->delete();
    }
}