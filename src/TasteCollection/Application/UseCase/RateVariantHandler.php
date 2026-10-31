<?php

declare(strict_types=1);

namespace App\TasteCollection\Application\UseCase;

use App\Shared\Domain\Service\IdGenerator;
use App\TasteCollection\Application\Dto\RateVariantDto;
use App\TasteCollection\Domain\Exception\VariantNotFound;
use App\TasteCollection\Domain\Model\TasteJudgment;
use App\TasteCollection\Domain\Model\TasteJudgmentId;
use App\TasteCollection\Domain\Repository\IngredientVariantRepository;
use App\TasteCollection\Domain\Repository\TasteJudgmentRepository;
use Psr\Clock\ClockInterface;

final readonly class RateVariantHandler
{
    public function __construct(
        private IdGenerator $ids,
        private TasteJudgmentRepository $judgments,
        private IngredientVariantRepository $variants,
        private ClockInterface $clock,
    ) {}

    public function __invoke(RateVariant $command): RateVariantDto
    {
        if (!$this->variants->exists($command->variantId)) {
            throw new VariantNotFound($command->variantId);
        }

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

        return RateVariantDto::fromJudgment($judgment);
    }
}
