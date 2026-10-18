<?php

declare(strict_types=1);

namespace App\Identity\Infrastructure\Fixtures\Factory;

use App\Identity\Domain\Model\User;
use App\Shared\Domain\Model\UserId;
use Symfony\Component\Uid\Uuid;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<User>
 */
final class UserFactory extends PersistentObjectFactory
{
    public static function class(): string
    {
        return User::class;
    }

    protected function defaults(): array
    {
        return [
            'id' => UserId::fromString(Uuid::v7()->toRfc4122()),
            'name' => self::faker()->firstName(),
            'createdAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTimeBetween('-1 year')),
        ];
    }

    protected function initialize(): static
    {
        return $this->instantiateWith(
            static fn (array $a): User => User::create($a['id'], $a['name'], $a['createdAt']),
        );
    }
}
