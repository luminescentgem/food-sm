<?php

declare(strict_types=1);

namespace App\TasteCollection\Application\Dto;

final readonly class IngredientDto
{
    public function __construct(
        public string $id,
        public string $name,
        public string $category,
        public string $region,
        public float  $commonness,
        public string $defaultPreparationName,
        public string $defaultVariantId,
    ) {}
}
