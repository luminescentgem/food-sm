<?php

declare(strict_types=1);

namespace App\Identity\Api;

use App\Shared\Domain\Model\UserId;

interface IdentityApi
{
    public function exists(UserId $id): bool;
};