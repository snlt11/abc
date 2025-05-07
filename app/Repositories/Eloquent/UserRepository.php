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

    /**
     * Get all users with optional filtering and pagination
     *
     * @param array $filters
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getAll(array $filters = [])
    {
        $query = $this->model->newQuery();

        // Apply name filter if provided
        if (isset($filters['name'])) {
            $query->where('name', 'like', "%{$filters['name']}%");
        }

        // Handle pagination from GraphQL
        $page = $filters['page'] ?? 1; // Default to page 1
        $perPage = $filters['limit'] ?? $filters['per_page'] ?? 10; // Support both 'limit' and 'per_page'
        
        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * Find user by ID
     *
     * @param int $id
     * @return \App\Models\User|null
     */
    public function findById($id)
    {
        return $this->model->find($id);
    }

    /**
     * Find user by email
     *
     * @param string $email
     * @return \App\Models\User|null
     */
    public function findByEmail($email)
    {
        return $this->model->where('email', $email)->first();
    }

    /**
     * Create a new user
     *
     * @param array $data
     * @return \App\Models\User
     */
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * Update an existing user
     *
     * @param int $id
     * @param array $data
     * @return \App\Models\User
     */
    public function update($id, array $data)
    {
        $user = $this->findById($id);
        
        if (!$user) {
            throw new ModelNotFoundException("User with ID {$id} not found");
        }
        
        $user->update($data);
        
        return $user->fresh();
    }

    /**
     * Delete a user
     *
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        $user = $this->findById($id);
        
        if (!$user) {
            throw new ModelNotFoundException("User with ID {$id} not found");
        }
        
        return $user->delete();
    }
}