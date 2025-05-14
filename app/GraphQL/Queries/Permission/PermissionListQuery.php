<?php

namespace App\GraphQL\Queries\Permission;


use App\Services\Tenant\PermissionService;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Illuminate\Validation\ValidationException;

class PermissionListQuery
{
    /**
     * The permission service
     *
     * @var \App\Services\Tenant\PermissionService
     */
    protected $permissionService;

    /**
     * Create a new query instance
     *
     * @param  \App\Services\Tenant\PermissionService  $permissionService
     * @return void
     */
    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    /**
     * Handle the query
     *
     * @param  array<string, mixed>  $args
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     * @throws \Illuminate\Validation\ValidationException
     */
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
