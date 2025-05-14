<?php

namespace App\GraphQL\Queries\Permission;

use App\Services\Tenant\PermissionService;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Illuminate\Validation\ValidationException;

class PermissionListQuery
{
    protected $permissionService;

    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    public function __invoke($root, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        try {
            $page = $args['page'] ?? 1;
            $limit = $args['limit'] ?? 10;
            
            return $this->permissionService->getAllPermissions([
                'search' => $args['search'] ?? null,
                'page' => $page,
                'limit' => $limit
            ]);
        } catch (\Exception $e) {
            throw ValidationException::withMessages([
                'message' => $e->getMessage(),
            ]);
        }
    }
}
