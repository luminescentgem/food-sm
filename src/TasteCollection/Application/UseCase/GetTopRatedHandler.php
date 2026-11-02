<?php

declare(strict_types=1);

namespace App\TasteCollection\Application\UseCase;

use App\TasteCollection\Application\Dto\TopRatedDto;
use App\TasteCollection\Domain\Specification\CurrentRatings;
use App\TasteCollection\Application\Reader\RatingsReader;
use App\TasteCollection\Domain\Specification\TopRated;

final readonly class GetTopRatedHandler
{
    public function __construct(
        private RatingsReader $ratings,
    ) {}

    public function __invoke(GetTopRated $command): TopRatedDto
    {
        $top = $this->ratings->topRated(new TopRated(
            new CurrentRatings($command->userId),
            $command->limit,
        ));

        return TopRatedDto::fromVariants($command->userId, $top);
    }
}
