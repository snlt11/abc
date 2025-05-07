<?php

namespace App\Services\Tenant;

use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class UserService
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Create a new user
     *
     * @param array $data
     * @return \App\Models\User
     */
    public function createUser(array $data)
    {
        // Hash password if provided
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        return $this->userRepository->create($data);
    }

    /**
     * Update an existing user
     *
     * @param int $id
     * @param array $data
     * @return \App\Models\User
     */
    public function updateUser($id, array $data)
    {
        // Hash password if provided
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        return $this->userRepository->update($id, $data);
    }

    /**
     * Delete a user
     *
     * @param int $id
     * @return bool
     */
    public function deleteUser($id)
    {
        return $this->userRepository->delete($id);
    }

    /**
     * Get a user by ID
     *
     * @param int $id
     * @return \App\Models\User|null
     */
    public function getUserById($id)
    {
        return $this->userRepository->findById($id);
    }

    /**
     * Get a user by email
     *
     * @param string $email
     * @return \App\Models\User|null
     */
    public function getUserByEmail($email)
    {
        return $this->userRepository->findByEmail($email);
    }

    /**
     * Get users with optional filtering
     *
     * @param array $filters
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getUsers(array $filters = [])
    {
        return $this->userRepository->getAll($filters);
    }
}