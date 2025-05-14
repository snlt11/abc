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

    protected function handle(array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        try {
            $filters = [
                'search' => $args['search'] ?? null,
                'orderBy' => $args['orderBy'] ?? [],
            ];

            $perPage = $args['paginate'] ?? 10;
            $page = $args['page'] ?? 1;

            return $this->permissionService->getAllPermissions($filters, $perPage, $page);
        } catch (\Exception $e) {
            throw ValidationException::withMessages([
                'message' => $e->getMessage(),
            ]);
        }
    }
}
