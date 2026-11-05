<?php

declare(strict_types=1);

namespace App\Identity\Domain\Exception;

use App\Shared\Domain\Model\UserId;
use App\Identity\Domain\Exception\IdentityException;

final class UnknownUser extends IdentityException
{
    public function __construct(
        public UserId $userId,
    ) {
        parent::__construct(sprintf("User %s doesn't exist.", $userId));
    }
}