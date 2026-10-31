<?php

declare(strict_types=1);

namespace App\TasteCollection\Application\Dto;

use App\TasteCollection\Domain\Model\TasteJudgment;
use DateTimeImmutable;

final readonly class SwipeVariantDto
{
    private function __construct(
        public string $id,
        public string $userId,
        public string $variantId,
        public bool   $liked,
        public DateTimeImmutable $createdAt,
    ) {}

    public static function fromJudgment(TasteJudgment $judgment): self {
        return new self(
            (string) $judgment->id,
            (string) $judgment->userId,
            (string) $judgment->variantId,
            $judgment->liked ?? throw new \LogicException(
                                            'Not a swipe: ' . $judgment->id),
            $judgment->createdAt,
        );
    }
}
