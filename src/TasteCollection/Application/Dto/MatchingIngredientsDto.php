<?php

declare(strict_types=1);

namespace App\TasteCollection\Application\Dto;

final readonly class MatchingIngredientsDto
{
    private function __construct(
        /** @var list<IngredientDto> $ingredients */
        public array $ingredients,
    ) {}

    /** @param list<IngredientDto> $ingredients*/
    public static function fromIngredients(
        array $ingredients
    ): self {
        return new self(
            $ingredients,
        );
    }
}
