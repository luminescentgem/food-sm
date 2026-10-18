<?php

declare(strict_types=1);

namespace App\TasteCollection\Domain\Model;

/**
 * What a user actually judges: an ingredient in a given preparation (raw tomato, tomato sauce…).
 */
final class IngredientVariant
{
    private function __construct(
        public readonly VariantId $id,
        public readonly Ingredient $ingredient,
        public readonly Preparation $preparation,
    ) {}

    public static function create(VariantId $id, Ingredient $ingredient, Preparation $preparation): self
    {
        return new self($id, $ingredient, $preparation);
    }

    /** The variant every ingredient has: its default preparation. */
    public static function defaultOf(VariantId $id, Ingredient $ingredient): self
    {
        return new self($id, $ingredient, $ingredient->defaultPreparation);
    }
}
