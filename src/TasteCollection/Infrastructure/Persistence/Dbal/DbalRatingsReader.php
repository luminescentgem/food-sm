<?php

declare(strict_types=1);

namespace App\TasteCollection\Infrastructure\Persistence\Dbal;

use App\TasteCollection\Application\Dto\CurrentlyRatedVariantDto;
use App\TasteCollection\Application\Reader\RatingsReader;
use App\TasteCollection\Domain\Specification\CurrentRatings;
use App\TasteCollection\Domain\Specification\UserRatings;
use App\TasteCollection\Domain\Specification\TopRated;
use Doctrine\DBAL\Connection;
use Override;

final readonly class DbalRatingsReader implements RatingsReader
{
    public function __construct(private Connection $db) {}

    #[Override]
    /** @return list<CurrentlyRatedVariantDto> */
    public function topRated(TopRated $spec): array
    {
        [$command, $params] = $this->currentRatingsQuery($spec->ratings);
        $rows = $this->db->fetchAllAssociative(
            "SELECT * FROM ($command) AS r
             ORDER BY r.score DESC
             LIMIT :limit
             ;",
            $params + ["limit" => $spec->limit],
        );

        return array_map($this->newCurrentlyRatedVariantDto(...), $rows);
    }

    /** @return array{string, array{user: string}} */
    private function currentRatingsQuery(CurrentRatings $spec): array
    {
        [$command, $params] = $this->fullRatingsQuery(new UserRatings($spec->id));

        return [
            "SELECT * FROM ($command) as r
             WHERE r.recency_rank = 1
            ",
            $params,
        ];
    }

    /** @return array{string, array{user: string}} */
    private function fullRatingsQuery(UserRatings $spec): array
    {
        return [
            'SELECT j.id, j.variant_id, j.score, j.created_at AS rated_at,
                    p.id AS preparation_id, p.name AS preparation_name,
                    i.id AS ingredient_id, i.name AS ingredient_name,
                    ROW_NUMBER() OVER (
                        PARTITION BY j.variant_id
                        ORDER BY j.created_at DESC
                    ) AS recency_rank
             FROM taste_judgments AS j
             JOIN ingredient_variants AS v ON variant_id = v.id
             JOIN preparations AS p ON v.preparation_id = p.id
             JOIN ingredients AS i ON v.ingredient_id = i.id
             WHERE user_id = :user AND j.score IS NOT NULL
            ',
            ['user' => (string) $spec->id]
        ];
    }

    /** @param array{
     *      variant_id: string,
     *      ingredient_id: string,
     *      ingredient_name: string,
     *      preparation_id: string,
     *      preparation_name: string,
     *      score: int,
     *      rated_at: string,
     * } $row */
    private function newCurrentlyRatedVariantDto(array $row): CurrentlyRatedVariantDto
    {
        return new CurrentlyRatedVariantDto(
            $row["variant_id"],
            $row["ingredient_id"],
            $row["ingredient_name"],
            $row["preparation_id"],
            $row["preparation_name"],
            (float) $row["score"],
            new \DateTimeImmutable($row["rated_at"]),
        );
    }
}