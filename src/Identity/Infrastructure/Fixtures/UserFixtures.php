<?php

declare(strict_types=1);

namespace App\Identity\Infrastructure\Fixtures;

use App\Identity\Domain\Model\User;
use App\Shared\Domain\Model\UserId;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/**
 * Hardcoded MVP users (no auth). Fixed ids so the frontend can hardcode them.
 */
final class UserFixtures extends Fixture
{
    public const array USERS = [
        '01920000-0000-7000-8000-000000000001' => 'Garice',
        '01920000-0000-7000-8000-000000000002' => 'Adam',
        '01920000-0000-7000-8000-000000000003' => 'Testeur',
    ];

    public function load(ObjectManager $manager): void
    {
        $now = new \DateTimeImmutable();

        foreach (self::USERS as $id => $name) {
            $manager->persist(User::create(UserId::fromString($id), $name, $now));
        }

        $manager->flush();
    }
}
