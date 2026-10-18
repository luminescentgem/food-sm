<?php

declare(strict_types=1);

namespace App\TasteCollection\Application\UseCase;

use App\Shared\Domain\Model\UserId;
use App\TasteCollection\Domain\Model\VariantId;

final readonly class SwipeVariant
{
    public function __construct(
        public UserId    $userId,
        public VariantId $variantId,
        public bool      $liked,
    ) {}
}
