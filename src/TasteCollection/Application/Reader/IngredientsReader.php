<?php

declare(strict_types=1);

namespace App\TasteCollection\Application\Reader;

use App\TasteCollection\Application\Dto\IngredientDto;
use App\TasteCollection\Domain\Specification\MatchingIngredients;

interface IngredientsReader
{
    /** @return list<IngredientDto> */
    public function matching(MatchingIngredients $spec): array;
}
