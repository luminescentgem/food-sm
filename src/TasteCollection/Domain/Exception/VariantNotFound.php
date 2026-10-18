<?php

declare(strict_types=1);

namespace App\TasteCollection\Domain\Exception;

use App\TasteCollection\Domain\Model\VariantId;

final class VariantNotFound extends TasteCollectionException
{
    public function __construct(public readonly VariantId $variantId)
    {
        parent::__construct(sprintf('Variant %s not found.', $variantId));
    }
}