<?php

declare(strict_types=1);

namespace App\TasteCollection\Application\UseCase;

use App\Shared\Domain\Service\IdGenerator;
use App\TasteCollection\Application\Dto\SwipeVariantDto;
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

    public function __invoke(SwipeVariant $command): SwipeVariantDto
    {
        /** @var TasteJudgmentId $id */
        $id = $this->ids->next(TasteJudgmentId::class);

        $judgment = TasteJudgment::swipe(
            $id,
            $command->userId,
            $command->variantId,
            $command->liked,
            $this->clock->now(),
        );

        $this->judgments->append($judgment);

        return SwipeVariantDto::fromJudgment($judgment);
    }
}
