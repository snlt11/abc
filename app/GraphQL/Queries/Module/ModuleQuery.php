<?php

namespace App\GraphQL\Queries\Module;


use App\Services\Tenant\ModuleService;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Illuminate\Validation\ValidationException;

class ModuleQuery
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
     * @return \App\Models\Module
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function handle(array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        try {
            return $this->moduleService->getModule($args['id']);
        } catch (\Exception $e) {
            throw ValidationException::withMessages([
                'message' => $e->getMessage(),
            ]);
        }
    }
}
