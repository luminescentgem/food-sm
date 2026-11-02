<?php

declare(strict_types=1);

namespace App\TasteCollection\Application\UseCase;

use App\Shared\Domain\Model\UserId;

final readonly class GetTopRated
{
    public function __construct(
        public UserId    $userId,
        public int       $limit,
    ) {}
}
