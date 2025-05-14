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

    public function createUser(array $data)
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        return $this->userRepository->create($data);
    }

    public function updateUser($id, array $data)
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        return $this->userRepository->update($id, $data);
    }

    public function deleteUser($id)
    {
        return $this->userRepository->delete($id);
    }

    public function getUserById($id)
    {
        return $this->userRepository->findById($id);
    }

    public function getUserByEmail($email)
    {
        return $this->userRepository->findByEmail($email);
    }

    public function getUsers(array $filters = [])
    {
        return $this->userRepository->getAll($filters);
    }

    public function getAllUsers()
    {
        return $this->userRepository->getAll();
    }
}