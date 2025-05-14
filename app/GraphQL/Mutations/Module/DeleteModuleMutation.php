<?php

namespace App\GraphQL\Mutations\Module;


use App\Services\Tenant\ModuleService;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Illuminate\Validation\ValidationException;

class DeleteModuleMutation
{
    protected $moduleService;

    public function __construct(ModuleService $moduleService)
    {
        $this->moduleService = $moduleService;
    }

    protected function handle(array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        try {
            $result = $this->moduleService->deleteModule($args['id']);
            
            if (!$result) {
                throw new \Exception('Failed to delete module');
            }
            
            return $result;
        } catch (\Exception $e) {
            throw ValidationException::withMessages([
                'message' => 'Failed to delete module. Please try again.',
            ]);
        }
    }
}
