<?php

namespace App\GraphQL\Queries\User;

use App\Services\Tenant\UserService;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class UserQuery
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function __invoke($root, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        if (isset($args['id'])) {
            return $this->userService->getUserById($args['id']);
        }
        
        if (isset($args['email'])) {
            return $this->userService->getUserByEmail($args['email']);
        }
        
        return null;
    }
}