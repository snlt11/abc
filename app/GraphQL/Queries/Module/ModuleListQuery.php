<?php

namespace App\GraphQL\Queries\Module;


use App\Services\Tenant\ModuleService;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Illuminate\Validation\ValidationException;

class ModuleListQuery
{
    /**
     * The module service
     *
     * @var \App\Services\Tenant\ModuleService
     */
    protected $moduleService;

    /**
     * Create a new query instance
     *
     * @param  \App\Services\Tenant\ModuleService  $moduleService
     * @return void
     */
    public function __construct(ModuleService $moduleService)
    {
        $this->moduleService = $moduleService;
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
