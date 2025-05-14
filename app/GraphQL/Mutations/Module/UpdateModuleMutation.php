<?php

namespace App\GraphQL\Mutations\Module;


use App\Services\Tenant\ModuleService;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Illuminate\Validation\ValidationException;

class UpdateModuleMutation
{
    /**
     * The module service
     *
     * @var \App\Services\Tenant\ModuleService
     */
    protected $moduleService;

    /**
     * Create a new mutation instance
     *
     * @param  \App\Services\Tenant\ModuleService  $moduleService
     * @return void
     */
    public function __construct(ModuleService $moduleService)
    {
        $this->moduleService = $moduleService;
    }

    /**
     * Handle the mutation
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
            return $this->moduleService->updateModule($args['input']);
        } catch (\Exception $e) {
            throw ValidationException::withMessages([
                'message' => $e->getMessage(),
            ]);
        }
    }
}
