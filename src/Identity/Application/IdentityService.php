<?php

declare(strict_types=1);

namespace App\Identity\Application;

use App\Identity\Api\IdentityApi;
use App\Identity\Domain\Repository\UserRepository;
use App\Shared\Domain\Model\UserId;

class IdentityService implements IdentityApi
{
    public function __construct(
        private UserRepository $users,
    ) {}

    public function exists(UserId $id): bool
    {
        return $this->users->exists($id);
    }
};