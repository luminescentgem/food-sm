<?php

declare(strict_types=1);

namespace App\DevFixtures;

use App\Identity\Infrastructure\Fixtures\Factory\UserFactory;
use App\TasteCollection\Infrastructure\Fixtures\Factory\IngredientVariantFactory;
use App\TasteCollection\Infrastructure\Fixtures\Factory\TasteJudgmentFactory;
use Zenstruck\Foundry\Story;
use function Zenstruck\Foundry\faker;

/**
 * Random but plausible taste history for local development.
 *
 * Lives outside the modules because it combines Identity and TasteCollection.
 * Requires the real ingredients/variants to be loaded first (JSON seed).
 */
final class DevTastesStory extends Story
{
    private const SWIPES_PER_USER = 80;
    private const RATING_RATIO = 30; // % of swiped variants that also get a rating

    public function build(): void
    {
        $users = UserFactory::all() ?: UserFactory::createMany(3);

        foreach ($users as $user) {
            foreach (IngredientVariantFactory::randomSet(self::SWIPES_PER_USER) as $variant) {
                $swipe = TasteJudgmentFactory::new()->swipe()->create([
                    'userId' => $user->id,
                    'variant' => $variant,
                ]);

                if (faker()->boolean(self::RATING_RATIO)) {
                    // Rating after the swipe, consistent with the verdict.
                    $liked = $swipe->liked;
                    TasteJudgmentFactory::new()->rating(
                        $liked ? faker()->numberBetween(6, 10) : faker()->numberBetween(0, 4),
                    )->create([
                        'userId' => $user->id,
                        'variant' => $variant,
                        'createdAt' => $swipe->createdAt->modify('+' . faker()->numberBetween(1, 20) . ' days'),
                    ]);
                }
            }
        }
    }
}
