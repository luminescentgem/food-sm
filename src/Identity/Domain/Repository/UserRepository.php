<?php

declare(strict_types=1);

namespace App\Identity\Domain\Repository;

use App\Shared\Domain\Model\UserId;

interface UserRepository
{
    public function exists(UserId $id): bool;
};
