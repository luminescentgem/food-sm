<?php

declare(strict_types=1);

namespace App\DevFixtures;

use App\Identity\Infrastructure\Fixtures\UserFixtures;
use App\TasteCollection\Infrastructure\Fixtures\IngredientFixtures;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Hooks the Foundry story into `php bin/console doctrine:fixtures:load`.
 */
final class DevTastesFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        DevTastesStory::load();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            IngredientFixtures::class,
        ];
    }
}
