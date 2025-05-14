<?php

namespace App\GraphQL\Mutations\Permission;


use App\Services\Tenant\PermissionService;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Illuminate\Validation\ValidationException;

class CreatePermissionMutation
{
    protected $permissionService;

    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    protected function handle(array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        try {
            return $this->permissionService->createPermission($args['input']);
        } catch (\Exception $e) {
            throw ValidationException::withMessages([
                'message' => $e->getMessage(),
            ]);
        }
    }
}
