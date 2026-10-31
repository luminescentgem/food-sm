<?php

declare(strict_types=1);

namespace App\TasteCollection\Application\UseCase;

use App\Shared\Domain\Service\IdGenerator;
use App\TasteCollection\Application\Dto\RatingVariantDto;
use App\TasteCollection\Domain\Model\TasteJudgment;
use App\TasteCollection\Domain\Model\TasteJudgmentId;
use App\TasteCollection\Domain\Repository\TasteJudgmentRepository;
use Psr\Clock\ClockInterface;

final readonly class RatingVariantHandler
{
    public function __construct(
        private IdGenerator $ids,
        private TasteJudgmentRepository $judgments,
        private ClockInterface $clock,
    ) {}

    public function __invoke(RatingVariant $command): RatingVariantDto
    {
        /** @var TasteJudgmentId $id */
        $id = $this->ids->next(TasteJudgmentId::class);

        $judgment = TasteJudgment::rating(
            $id,
            $command->userId,
            $command->variantId,
            $command->score,
            $this->clock->now(),
        );

        $this->judgments->append($judgment);

        return RatingVariantDto::fromJudgment($judgment);
    }
}
