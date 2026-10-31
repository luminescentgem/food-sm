<?php

declare(strict_types=1);

namespace App\TasteCollection\Infrastructure\Http\Request;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class RatingVariantRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Uuid]
        public string $variantId = '',

        #[Assert\NotNull]
        public ?int $score = null,
    ) {}
}
