<?php

declare(strict_types=1);

namespace App\TasteCollection\Domain\Specification;

use App\Shared\Domain\Model\UserId;

/**
 * Latest rating per variant counts.
 */
final readonly class CurrentRatings
{
    public function __construct(
        public UserId $id,
    ) {}
}
