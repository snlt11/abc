<?php

namespace App\GraphQL\Mutations\Permission;


use App\Services\Tenant\PermissionService;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Illuminate\Validation\ValidationException;

class UpdatePermissionMutation
{
    protected $permissionService;

    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    public function __invoke($root, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        try {
            $input = $args['input'] ?? $args;
            
            if (!isset($input['id'])) {
                throw new \Exception('Permission ID is required');
            }
            
            $permission = $this->permissionService->updatePermission($input);
            
            if (!$permission) {
                throw new \Exception('Failed to update permission');
            }
            
            return [
                'success' => true,
                'message' => 'Permission updated successfully',
                'permission' => $permission
            ];
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'permission' => null
            ];
        }
    }
}
