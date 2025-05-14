<?php

namespace App\GraphQL\Mutations\User;

use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Validation\ValidationException;

class AssignPermissionToUsersMutation
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function __invoke($_, array $args)
    {
        try {
            $userIds = $args['userIds'];
            $permissionId = $args['permissionId'];

            if (!is_array($userIds)) {
                $userIds = [$userIds];
            }

            if (empty($userIds)) {
                throw new \InvalidArgumentException('At least one user ID is required');
            }

            $updatedCount = $this->userRepository->updatePermissionsForUsers($userIds, $permissionId);

            return $updatedCount > 0;
        } catch (\Exception $e) {
            throw ValidationException::withMessages([
                'message' => 'Failed to assign permission to users: ' . $e->getMessage(),
            ]);
        }
    }
}
