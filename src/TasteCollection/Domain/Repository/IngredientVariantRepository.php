<?php

declare(strict_types=1);

namespace App\TasteCollection\Domain\Repository;

use App\TasteCollection\Domain\Model\VariantId;

interface IngredientVariantRepository
{
    public function exists(VariantId $variantId): bool;
}
