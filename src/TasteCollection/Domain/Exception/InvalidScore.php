<?php

declare(strict_types=1);

namespace App\TasteCollection\Domain\Exception;

final class InvalidScore extends TasteCollectionException
{
    public function __construct(int $score) {
        parent::__construct(sprintf('Score must be between 0 and 10, got %d.', $score));
    }
}