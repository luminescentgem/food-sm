<?php

declare(strict_types=1);

namespace App\TasteCollection\Application\UseCase;

use App\Shared\Domain\Service\IdGenerator;
use App\TasteCollection\Domain\Model\TasteJudgment;
use App\TasteCollection\Domain\Model\TasteJudgmentId;
use App\TasteCollection\Domain\Repository\TasteJudgmentRepository;
use Psr\Clock\ClockInterface;

final readonly class SwipeVariantHandler
{
    public function __construct(
        private IdGenerator $ids,
        private TasteJudgmentRepository $judgments,
        private ClockInterface $clock,
    ) {}

    public function __invoke(SwipeVariant $command): void
    {
        /** @var TasteJudgmentId $id */
        $id = $this->ids->next(TasteJudgmentId::class);
        
        $this->judgments->append(TasteJudgment::swipe(
            $id,
            $command->userId,
            $command->variantId,
            $command->liked,
            $this->clock->now(),
        ));
    }
}
