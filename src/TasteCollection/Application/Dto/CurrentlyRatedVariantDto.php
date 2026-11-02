<?php

declare(strict_types=1);

namespace App\TasteCollection\Application\Dto;

use DateTimeImmutable;

final readonly class CurrentlyRatedVariantDto
{
    public function __construct(
        public string $variantId,
        public string $ingredientId,
        public string $ingredientName,
        public string $preparationId,
        public string $preparationName,
        public float $score,
        public DateTimeImmutable $ratedAt,
    ) {}
}
