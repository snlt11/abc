<?php

namespace App\GraphQL\Mutations\Module;

use App\Services\Tenant\ModuleService;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Illuminate\Validation\ValidationException;

class CreateModuleMutation
{
    protected $moduleService;

    public function __construct(ModuleService $moduleService)
    {
        $this->moduleService = $moduleService;
    }

    protected function handle(array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        try {
            return $this->moduleService->createModule($args['input']);
        } catch (\Exception $e) {
            throw ValidationException::withMessages([
                'message' => 'Failed to create module. Please try again.',
            ]);
        }
    }
}
