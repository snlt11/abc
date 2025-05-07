<?php

namespace App\GraphQL\Queries\User;

use App\Services\Tenant\UserService;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class UserListQuery
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Get a list of users with optional filtering
     *
     * @param  mixed  $root
     * @param  array  $args
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function __invoke($root, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        return $this->userService->getUsers($args);
    }
}