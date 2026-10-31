<?php

declare(strict_types=1);

namespace App\TasteCollection\Infrastructure\Persistence\Doctrine;

use App\TasteCollection\Domain\Model\IngredientVariant;
use App\TasteCollection\Domain\Model\VariantId;
use App\TasteCollection\Domain\Repository\IngredientVariantRepository;
use Doctrine\ORM\EntityManagerInterface;

final readonly class DoctrineIngredientVariantRepository implements IngredientVariantRepository
{
    public function __construct(private EntityManagerInterface $em) {}

    #[\Override]
    public function exists(VariantId $variantId): bool
    {
        $n = $this->em->getRepository(IngredientVariant::class)
                      ->count(['id' => $variantId]);
        return $n > 0;
    }
}
