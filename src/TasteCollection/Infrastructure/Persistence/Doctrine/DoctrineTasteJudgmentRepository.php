<?php

declare(strict_types=1);

namespace App\TasteCollection\Infrastructure\Persistence\Doctrine;

use App\TasteCollection\Domain\Model\TasteJudgment;
use App\TasteCollection\Domain\Repository\TasteJudgmentRepository;
use Doctrine\ORM\EntityManagerInterface;

final readonly class DoctrineTasteJudgmentRepository implements TasteJudgmentRepository
{
    public function __construct(private EntityManagerInterface $em) {}

    #[\Override]
    public function append(TasteJudgment $judgment): void
    {
        $this->em->persist($judgment);
        $this->em->flush();
    }
}
