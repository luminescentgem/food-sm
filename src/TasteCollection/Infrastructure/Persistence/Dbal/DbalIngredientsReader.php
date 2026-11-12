<?php

declare(strict_types=1);

namespace App\TasteCollection\Infrastructure\Persistence\Dbal;

use App\TasteCollection\Application\Dto\IngredientDto;
use App\TasteCollection\Application\Reader\IngredientsReader;
use App\TasteCollection\Domain\Specification\MatchingIngredients;
use Doctrine\DBAL\Connection;
use Override;

final readonly class DbalIngredientsReader implements IngredientsReader
{
    public function __construct(private Connection $db) {}

    #[Override]
    /** @return list<IngredientDto> */
    public function matching(MatchingIngredients $spec): array
    {
        [$command, $params] = $this->allIngredientsQuery($spec->query);
        $rows = $this->db->fetchAllAssociative(
            "SELECT * FROM ($command) AS r
             ORDER BY r.commonness DESC
             LIMIT :limit
             ;",
            $params + ["limit" => $spec->limit],
        );

        return array_map($this->newIngredientDto(...), $rows);
    }

    /** @return array{string, array{query: string}} */
    private function allIngredientsQuery(string $query): array
    {
        return [
            "SELECT i.id, i.name, i.category, i.region, i.commonness,
                    p.name AS def_prep_name,
                    v.id AS def_variant_id
             FROM ingredients AS i
             JOIN preparations AS p ON i.default_preparation_id = p.id
             JOIN ingredient_variants AS v ON v.preparation_id = p.id
                                           AND v.ingredient_id = i.id
             WHERE i.name LIKE :query
             ",
            ['query' => '%'.$query.'%']
        ];
    }

    /** @param array{
     *      id: string,
     *      name: string,
     *      category: string,
     *      region: string,
     *      commonness: float,
     *      def_prep_name: string,
     *      def_variant_id: string,
     * } $row */
    private function newIngredientDto(array $row): IngredientDto
    {
        return new IngredientDto(
            $row["id"],
            $row["name"],
            $row["category"],
            $row["region"],
            (float) $row["commonness"],
            $row["def_prep_name"],
            $row["def_variant_id"],
        );
    }
}