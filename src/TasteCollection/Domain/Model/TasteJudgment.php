<?php

declare(strict_types=1);

namespace App\TasteCollection\Domain\Model;

use App\Shared\Domain\Model\UserId;
use App\TasteCollection\Domain\Exception\InvalidScore;
use App\TasteCollection\Domain\Model\TasteJudgmentId;

final class TasteJudgment
{
    private function __construct(
        public readonly TasteJudgmentId    $id,
        public readonly UserId             $userId,
        public readonly VariantId          $variantId,
        public readonly ?bool              $liked,
        public readonly ?int               $score,
        public readonly \DateTimeImmutable $createdAt,
    ) {}

    public function isSwipe(): bool
    {
        return $this->liked !== null;
    }

    public static function swipe(
        TasteJudgmentId $id, UserId $userId, VariantId $variantId,
        bool $liked, \DateTimeImmutable $at,
    ): self {
        return new self($id, $userId, $variantId, $liked, null, $at);
    }

    public static function rating(
        TasteJudgmentId $id, UserId $userId, VariantId $variantId,
        int $score, \DateTimeImmutable $at,
    ): self {
        if ($score < 0 || $score > 10) {
            throw new InvalidScore($score);
        }
        return new self($id, $userId, $variantId, null, $score, $at);
    }
}
