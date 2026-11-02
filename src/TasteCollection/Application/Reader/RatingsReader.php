<?php

declare(strict_types=1);

namespace App\TasteCollection\Application\Reader;

use App\TasteCollection\Application\Dto\CurrentlyRatedVariantDto;
use App\TasteCollection\Domain\Specification\CurrentRatings;
use App\TasteCollection\Domain\Specification\TopRated;

interface RatingsReader
{
    /** @return list<CurrentlyRatedVariantDto> */
    public function topRated(TopRated $spec): array;
}
