<?php

declare(strict_types=1);

namespace App\TasteCollection\Infrastructure\Persistence\Doctrine\Type;

use App\Shared\Infrastructure\Persistence\Doctrine\IdType;
use App\TasteCollection\Domain\Model\VariantId;

final class VariantIdType extends IdType
{
    protected function idClass(): string
    {
        return VariantId::class;
    }
}
