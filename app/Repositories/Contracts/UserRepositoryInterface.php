<?php

namespace App\Repositories\Contracts;

interface UserRepositoryInterface
{
    public function getAll(array $filters = []);

    public function findById($id);

    public function findByEmail($email);

    public function create(array $data);

    public function update($id, array $data);

    public function delete($id);
}