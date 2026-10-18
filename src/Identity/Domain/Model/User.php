<?php

declare(strict_types=1);

namespace App\Identity\Domain\Model;

use App\Shared\Domain\Model\UserId;

final class User
{
    private function __construct(
        public readonly UserId $id,
        public readonly string $name,
        public readonly \DateTimeImmutable $createdAt,
    ) {}

    public static function create(UserId $id, string $name, \DateTimeImmutable $createdAt): self
    {
        if (trim($name) === '') {
            throw new \InvalidArgumentException('User name cannot be empty.');
        }

        return new self($id, $name, $createdAt);
    }
}
