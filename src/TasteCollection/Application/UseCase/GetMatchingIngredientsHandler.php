<?php

declare(strict_types=1);

namespace App\TasteCollection\Application\UseCase;

use App\TasteCollection\Application\Dto\MatchingIngredientsDto;
use App\TasteCollection\Application\Reader\IngredientsReader;
use App\TasteCollection\Domain\Specification\MatchingIngredients;

final readonly class GetMatchingIngredientsHandler
{
    public function __construct(
        private IngredientsReader $ingredients,
    ) {}

    public function __invoke(GetMatchingIngredients $command): MatchingIngredientsDto
    {
        $matching = $this->ingredients->matching(new MatchingIngredients(
            $command->query,
            $command->limit,
        ));

        return MatchingIngredientsDto::fromIngredients($matching);
    }
}
