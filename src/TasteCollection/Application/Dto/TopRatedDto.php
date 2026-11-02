<?php

declare(strict_types=1);

namespace App\TasteCollection\Application\Dto;

use App\Shared\Domain\Model\UserId;

final readonly class TopRatedDto
{
    private function __construct(
        public string $userId,
        /** @var list<CurrentlyRatedVariantDto> */
        public array $ratedVariants,
    ) {}

    /** @param list<CurrentlyRatedVariantDto> $ratedVariants*/
    public static function fromVariants(
        UserId $userId,
        array $ratedVariants
    ): self {
        return new self(
            (string) $userId,
            $ratedVariants,
        );
    }
}
