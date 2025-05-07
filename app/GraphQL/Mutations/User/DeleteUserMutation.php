<?php

namespace App\GraphQL\Mutations\User;

use App\Services\Tenant\UserService;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class DeleteUserMutation
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Delete a user
     *
     * @param  mixed  $root
     * @param  array  $args
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context
     * @return bool
     */
    public function __invoke($root, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        return $this->userService->deleteUser($args['id']);
    }
}