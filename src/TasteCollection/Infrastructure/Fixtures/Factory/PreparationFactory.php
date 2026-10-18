<?php

declare(strict_types=1);

namespace App\TasteCollection\Infrastructure\Fixtures\Factory;

use App\TasteCollection\Domain\Model\Preparation;
use App\TasteCollection\Domain\Model\PreparationId;
use Symfony\Component\Uid\Uuid;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Preparation>
 */
final class PreparationFactory extends PersistentObjectFactory
{
    public static function class(): string
    {
        return Preparation::class;
    }

    protected function defaults(): array
    {
        return [
            'id' => PreparationId::fromString(Uuid::v7()->toRfc4122()),
            'name' => self::faker()->unique()->word(),
            'parent' => null,
        ];
    }

    /** A child node, e.g. PreparationFactory::new()->childOf($cooked)->create(['name' => 'grilled']) */
    public function childOf(Preparation|self $parent): self
    {
        return $this->with(['parent' => $parent]);
    }

    protected function initialize(): static
    {
        return $this->instantiateWith(
            static fn (array $a): Preparation => Preparation::create(
                $a['id'],
                $a['name'],
                $a['parent'],
            ),
        );
    }
}
