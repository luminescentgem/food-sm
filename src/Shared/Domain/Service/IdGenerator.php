<?php

declare(strict_types=1);

namespace App\Shared\Domain\Service;

use App\Shared\Domain\Model\Id;

interface IdGenerator
{
    /**
     * @template T of Id
     * @param class-string<T> $type
     * @return T
     */
    public function next(string $type): Id;
}