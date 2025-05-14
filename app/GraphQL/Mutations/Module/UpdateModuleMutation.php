<?php

namespace App\GraphQL\Mutations\Module;

use App\Services\Tenant\ModuleService;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class UpdateModuleMutation
{
    protected $moduleService;

    public function __construct(ModuleService $moduleService)
    {
        $this->moduleService = $moduleService;
    }

    public function __invoke($root, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        try {
            $input = $args['input'] ?? $args;
            
            if (!is_array($input) || !isset($input['id'])) {
                throw new \Exception('Invalid input or missing module ID');
            }

            return $this->moduleService->updateModule($input);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
