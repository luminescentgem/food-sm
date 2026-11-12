<?php

declare(strict_types=1);

namespace App\TasteCollection\Application\UseCase;

final readonly class GetMatchingIngredients
{
    public function __construct(
        public string $query,
        public int $limit,
    ) {}
}
