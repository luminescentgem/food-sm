<?php

declare(strict_types=1);

namespace App\TasteCollection\Infrastructure\Http\Request;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class RateVariantRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Uuid]
        public string $variantId = '',

        #[Assert\NotNull]
        #[Assert\Range(min: 0, max: 10)]
        public ?int $score = null,
    ) {}
}
