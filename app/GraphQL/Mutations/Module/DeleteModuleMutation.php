<?php

namespace App\GraphQL\Mutations\Module;

use App\Models\Module;
use App\Services\Tenant\ModuleService;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class DeleteModuleMutation
{
    protected $moduleService;

    public function __construct(ModuleService $moduleService)
    {
        $this->moduleService = $moduleService;
    }

    public function __invoke($root, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        try {
            if (!$module = Module::find($args['id'])) {
                return ['success' => false, 'message' => 'Module not found'];
            }
            
            return $module->delete()
                ? ['success' => true, 'message' => 'Module deleted successfully']
                : ['success' => false, 'message' => 'Failed to delete module'];
                
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'An error occurred while deleting the module'];

        }
    }
}
