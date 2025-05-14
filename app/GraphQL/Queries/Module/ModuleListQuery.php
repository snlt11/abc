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

    protected function handle(array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        try {
            $filters = [
                'search' => $args['search'] ?? null,
                'permission_id' => $args['permission_id'] ?? null,
                'orderBy' => $args['orderBy'] ?? [],
            ];

            $perPage = $args['paginate'] ?? 10;
            $page = $args['page'] ?? 1;

            return $this->moduleService->getAllModules($filters, $perPage, $page);
        } catch (\Exception $e) {
            throw ValidationException::withMessages([
                'message' => $e->getMessage(),
            ]);
        }
    }
}
