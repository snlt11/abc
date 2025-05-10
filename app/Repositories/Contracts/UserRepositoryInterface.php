<?php

namespace App\Repositories\Contracts;

interface UserRepositoryInterface
{
    /**
     * Get all users with optional filtering
     *
     * @param array $filters
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getAll(array $filters = []);

    /**
     * Find user by ID
     *
     * @param string $id
     * @return \App\Models\User|null
     */
    public function findById($id);

    /**
     * Find user by email
     *
     * @param string $email
     * @return \App\Models\User|null
     */
    public function findByEmail($email);

    /**
     * Create a new user
     *
     * @param array $data
     * @return \App\Models\User
     */
    public function create(array $data);

    /**
     * Update an existing user
     *
     * @param string $id
     * @param array $data
     * @return \App\Models\User
     */
    public function update($id, array $data);

    /**
     * Delete a user
     *
     * @param string $id
     * @return bool
     */
    public function delete($id);
}