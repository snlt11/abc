<?php

namespace App\GraphQL\Mutations\Module;


use App\Services\Tenant\ModuleService;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Illuminate\Validation\ValidationException;

class DeleteModuleMutation
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
     * @return bool
     * @throws \Illuminate\Validation\ValidationException
     */
    /**
     * Handle the mutation
     *
     * @param  array<string, mixed>  $args
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo
     * @return bool
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function handle(array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        try {
            $result = $this->moduleService->deleteModule($args['id']);
            
            if (!$result) {
                throw new \Exception('Failed to delete module');
            }
            
            return $result;
        } catch (\Exception $e) {
            // Log the actual error for debugging
            \Illuminate\Support\Facades\Log::error('Failed to delete module', [
                'id' => $args['id'],
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Return a user-friendly error message
            throw ValidationException::withMessages([
                'message' => 'Failed to delete module. Please try again.',
            ]);
        }
    }
}
