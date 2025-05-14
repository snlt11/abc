<?php

namespace App\GraphQL\Queries\Permission;

use App\Services\Tenant\PermissionService;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Illuminate\Validation\ValidationException;

class PermissionQuery
{
    protected $permissionService;

    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    public function __invoke($root, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        try {
            return $this->permissionService->getPermission($args['id']);
        } catch (\Exception $e) {
            throw ValidationException::withMessages([
                'message' => $e->getMessage(),
            ]);
        }
    }
}
