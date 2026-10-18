<?php

declare(strict_types=1);

namespace App\TasteCollection\Infrastructure\Fixtures\Factory;

use App\TasteCollection\Domain\Model\IngredientVariant;
use App\TasteCollection\Domain\Model\VariantId;
use Symfony\Component\Uid\Uuid;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<IngredientVariant>
 */
final class IngredientVariantFactory extends PersistentObjectFactory
{
    public static function class(): string
    {
        return IngredientVariant::class;
    }

    protected function defaults(): array
    {
        return [
            'id' => VariantId::fromString(Uuid::v7()->toRfc4122()),
            'ingredient' => IngredientFactory::new(),
            'preparation' => null, // null = the ingredient's default preparation
        ];
    }

    protected function initialize(): static
    {
        return $this->instantiateWith(
            static fn (array $a): IngredientVariant => IngredientVariant::create(
                $a['id'],
                $a['ingredient'],
                $a['preparation'] ?? $a['ingredient']->defaultPreparation,
            ),
        );
    }
}
