<?php

namespace App\GraphQL\Mutations\Permission;


use App\Services\Tenant\PermissionService;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Illuminate\Validation\ValidationException;

class DeletePermissionMutation
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
            $id = $input['id'] ?? null;
            
            if (!$id) {
                throw new \Exception('Permission ID is required');
            }
            
            $result = $this->permissionService->deletePermission($id);
            
            if (!$result) {
                throw new \Exception('Failed to delete permission');
            }
            
            return [
                'success' => true,
                'message' => 'Permission deleted successfully'
            ];
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}
