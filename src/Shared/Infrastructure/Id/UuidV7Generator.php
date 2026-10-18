<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Id;

use App\Shared\Domain\Model\Id;
use App\Shared\Domain\Service\IdGenerator;
use Symfony\Component\Uid\Uuid;

final class UuidV7Generator implements IdGenerator
{
    public function next(string $type): Id
    {
        return $type::fromString(Uuid::v7()->toRfc4122());
    }
}
