<?php

declare(strict_types=1);

namespace App\TasteCollection\Domain\Repository;

use App\TasteCollection\Domain\Model\TasteJudgment;

/**
 * Append-only: judgments are never updated or deleted.
 */
interface TasteJudgmentRepository
{
    public function append(TasteJudgment $judgment): void;
}
