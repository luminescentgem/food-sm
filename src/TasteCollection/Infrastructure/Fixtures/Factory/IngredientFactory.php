<?php

declare(strict_types=1);

namespace App\TasteCollection\Infrastructure\Fixtures\Factory;

use App\TasteCollection\Domain\Model\Ingredient;
use App\TasteCollection\Domain\Model\IngredientId;
use Symfony\Component\Uid\Uuid;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Ingredient>
 */
final class IngredientFactory extends PersistentObjectFactory
{
    public static function class(): string
    {
        return Ingredient::class;
    }

    protected function defaults(): array
    {
        return [
            'id' => IngredientId::fromString(Uuid::v7()->toRfc4122()),
            'name' => self::faker()->unique()->word(),
            // Same vocabularies as data/ingredients.json.
            'category' => self::faker()->randomElement(['algue', 'cereale', 'champignon', 'charcuterie', 'condiment', 'epice', 'feculent', 'fromage', 'fruit', 'fruit_de_mer', 'herbe', 'legume', 'legumineuse', 'matiere_grasse', 'noix_graine', 'oeuf', 'poisson', 'produit_laitier', 'sucre', 'viande', 'volaille']),
            'region' => self::faker()->randomElement(['monde', 'europe', 'mediterranee', 'moyen_orient', 'asie', 'afrique', 'ameriques', 'tropiques']),
            'commonness' => self::faker()->randomFloat(2, 0, 1),
            'defaultPreparation' => PreparationFactory::new(),
            'createdAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTimeBetween('-1 year')),
        ];
    }

    public function common(): self
    {
        return $this->with(['commonness' => self::faker()->randomFloat(2, 0.7, 1)]);
    }

    public function niche(): self
    {
        return $this->with(['commonness' => self::faker()->randomFloat(2, 0, 0.2)]);
    }

    protected function initialize(): static
    {
        return $this->instantiateWith(
            static fn (array $a): Ingredient => Ingredient::create(
                $a['id'],
                $a['name'],
                $a['category'],
                $a['region'],
                $a['commonness'],
                $a['defaultPreparation'],
                $a['createdAt'],
            ),
        );
    }
}
