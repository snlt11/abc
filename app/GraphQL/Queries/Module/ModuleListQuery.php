<?php

namespace App\GraphQL\Queries\Module;

use App\Services\Tenant\ModuleService;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Illuminate\Validation\ValidationException;

class ModuleListQuery
{
    protected $moduleService;

    public function __construct(ModuleService $moduleService)
    {
        $this->moduleService = $moduleService;
    }

    public function __invoke($root, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        try {
            $filters = [
                'search' => $args['search'] ?? null,
                'permission_id' => $args['permission_id'] ?? null,
                'orderBy' => $args['orderBy'] ?? [],
                'page' => $args['page'] ?? 1,
                'limit' => $args['limit'] ?? 10,
            ];

            return $this->moduleService->getAllModules($filters);
        } catch (\Exception $e) {
            throw ValidationException::withMessages([
                'message' => $e->getMessage(),
            ]);
        }
    }
}
