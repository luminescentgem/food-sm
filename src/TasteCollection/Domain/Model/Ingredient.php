<?php

declare(strict_types=1);

namespace App\TasteCollection\Domain\Model;

final class Ingredient
{
    private function __construct(
        public readonly IngredientId $id,
        public readonly string $name,
        public readonly string $category,
        public readonly string $region,
        public readonly float $commonness,
        public readonly Preparation $defaultPreparation,
        public readonly \DateTimeImmutable $createdAt,
    ) {}

    public static function create(
        IngredientId $id,
        string $name,
        string $category,
        string $region,
        float $commonness,
        Preparation $defaultPreparation,
        \DateTimeImmutable $createdAt,
    ): self {
        if (trim($name) === '') {
            throw new \InvalidArgumentException('Ingredient name cannot be empty.');
        }
        if ($commonness < 0.0 || $commonness > 1.0) {
            throw new \InvalidArgumentException(sprintf('Commonness must be between 0 and 1, got %s.', $commonness));
        }

        return new self($id, $name, $category, $region, $commonness, $defaultPreparation, $createdAt);
    }
}
