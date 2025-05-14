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

    protected function handle(array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        try {
            return $this->permissionService->deletePermission($args['id']);
        } catch (\Exception $e) {
            throw ValidationException::withMessages([
                'message' => $e->getMessage(),
            ]);
        }
    }
}
