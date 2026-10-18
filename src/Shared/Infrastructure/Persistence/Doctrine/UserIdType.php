<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Persistence\Doctrine;

use App\Shared\Domain\Model\UserId;

final class UserIdType extends IdType
{
    protected function idClass(): string
    {
        return UserId::class;
    }
}
