<?php

namespace App\GraphQL\Mutations\User;

use App\Services\Tenant\UserService;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class UpdateUserMutation
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Update an existing user
     *
     * @param  mixed  $root
     * @param  array  $args
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context
     * @return \App\Models\User
     */
    public function __invoke($root, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        return $this->userService->updateUser($args['id'], $args);
    }
}