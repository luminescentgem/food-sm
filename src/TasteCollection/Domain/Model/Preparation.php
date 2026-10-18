<?php

declare(strict_types=1);

namespace App\TasteCollection\Domain\Model;

/**
 * A node of the preparation tree: raw / cooked → grilled, steamed…
 */
final class Preparation
{
    private function __construct(
        public readonly PreparationId $id,
        public readonly string $name,
        public readonly ?Preparation $parent,
    ) {}

    public static function create(PreparationId $id, string $name, ?Preparation $parent = null): self
    {
        if (trim($name) === '') {
            throw new \InvalidArgumentException('Preparation name cannot be empty.');
        }

        return new self($id, $name, $parent);
    }
}
