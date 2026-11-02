<?php

declare(strict_types=1);

namespace App\TasteCollection\Domain\Specification;


final readonly class TopRated
{
    public function __construct(
        public CurrentRatings $ratings,
        public int $limit
    ) {
        if ($limit < 1) {
            throw new \InvalidArgumentException(
                sprintf('Top needs at least a limit of 1, got %d.', $limit)
            );
        }
    }
}
