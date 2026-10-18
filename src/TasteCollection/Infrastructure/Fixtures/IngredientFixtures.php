<?php

declare(strict_types=1);

namespace App\TasteCollection\Infrastructure\Fixtures;

use App\Shared\Domain\Service\IdGenerator;
use App\TasteCollection\Domain\Model\Ingredient;
use App\TasteCollection\Domain\Model\IngredientId;
use App\TasteCollection\Domain\Model\IngredientVariant;
use App\TasteCollection\Domain\Model\Preparation;
use App\TasteCollection\Domain\Model\PreparationId;
use App\TasteCollection\Domain\Model\VariantId;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/**
 * Seeds the preparation tree, ingredients and their default variants
 * from data/ingredients.json, through the domain constructors so the
 * seed obeys the same invariants as real data.
 */
final class IngredientFixtures extends Fixture
{
    private const string DATA_FILE = __DIR__ . '/data/ingredients.json';

    public function __construct(private readonly IdGenerator $ids) {}

    public function load(ObjectManager $manager): void
    {
        /** @var array{preparations: list<array{slug: string, name: string, parent: ?string}>, ingredients: list<array{slug: string, name: string, category: string, region: string, commonness: float|int, default_preparation: string}>} $data */
        $data = json_decode((string) file_get_contents(self::DATA_FILE), true, flags: \JSON_THROW_ON_ERROR);
        $now = new \DateTimeImmutable();

        // Parents are listed before their children in the JSON.
        $preparations = [];
        foreach ($data['preparations'] as $p) {
            $parent = null;
            if ($p['parent'] !== null) {
                $parent = $preparations[$p['parent']]
                    ?? throw new \LogicException("Preparation '{$p['slug']}' listed before its parent '{$p['parent']}'.");
            }
            $preparations[$p['slug']] = Preparation::create($this->ids->next(PreparationId::class), $p['name'], $parent);
            $manager->persist($preparations[$p['slug']]);
        }

        foreach ($data['ingredients'] as $i) {
            $ingredient = Ingredient::create(
                $this->ids->next(IngredientId::class),
                $i['name'],
                $i['category'],
                $i['region'],
                (float) $i['commonness'],
                $preparations[$i['default_preparation']]
                    ?? throw new \LogicException("Unknown preparation '{$i['default_preparation']}' for '{$i['slug']}'."),
                $now,
            );
            $manager->persist($ingredient);

            // MVP: only the default variant exists.
            $manager->persist(IngredientVariant::defaultOf($this->ids->next(VariantId::class), $ingredient));
        }

        $manager->flush();
    }
}
