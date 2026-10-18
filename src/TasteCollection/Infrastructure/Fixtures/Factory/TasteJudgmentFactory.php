<?php

declare(strict_types=1);

namespace App\TasteCollection\Infrastructure\Fixtures\Factory;

use App\Shared\Domain\Model\UserId;
use App\TasteCollection\Domain\Model\IngredientVariant;
use App\TasteCollection\Domain\Model\TasteJudgment;
use App\TasteCollection\Domain\Model\TasteJudgmentId;
use Symfony\Component\Uid\Uuid;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * Usage:
 *   TasteJudgmentFactory::new()->swipe(liked: true)->create(['userId' => $id, 'variant' => $variant]);
 *   TasteJudgmentFactory::new()->rating(8)->create([...]);
 *
 * 'userId' is a plain UserId: users belong to Identity, and this module must not
 * import Identity's factories (deptrac). Pass real user ids from a Story.
 *
 * @extends PersistentObjectFactory<TasteJudgment>
 */
final class TasteJudgmentFactory extends PersistentObjectFactory
{
    public static function class(): string
    {
        return TasteJudgment::class;
    }

    protected function defaults(): array
    {
        return [
            'id' => TasteJudgmentId::fromString(Uuid::v7()->toRfc4122()),
            'userId' => UserId::fromString(Uuid::v7()->toRfc4122()),
            'variant' => IngredientVariantFactory::new(),
            'kind' => 'swipe',
            'liked' => self::faker()->boolean(60), // a bit more likes than dislikes
            'score' => self::faker()->numberBetween(0, 10),
            'createdAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTimeBetween('-60 days')),
        ];
    }

    public function swipe(?bool $liked = null): self
    {
        return $this->with(['kind' => 'swipe'] + ($liked === null ? [] : ['liked' => $liked]));
    }

    public function rating(?int $score = null): self
    {
        return $this->with(['kind' => 'rating'] + ($score === null ? [] : ['score' => $score]));
    }

    protected function initialize(): static
    {
        return $this->instantiateWith(
            static function (array $a): TasteJudgment {
                $variantId = $a['variant'] instanceof IngredientVariant ? $a['variant']->id : $a['variant'];

                return $a['kind'] === 'rating'
                    ? TasteJudgment::rating($a['id'], $a['userId'], $variantId, $a['score'], $a['createdAt'])
                    : TasteJudgment::swipe($a['id'], $a['userId'], $variantId, $a['liked'], $a['createdAt']);
            },
        );
    }
}
