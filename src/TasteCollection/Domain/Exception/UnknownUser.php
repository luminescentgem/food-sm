<?php

declare(strict_types=1);

namespace App\TasteCollection\Domain\Exception;

use App\Shared\Domain\Model\UserId;
use App\TasteCollection\Domain\Exception\TasteCollectionException;

final class UnknownUser extends TasteCollectionException
{
    public function __construct(
        public UserId $userId,
    ) {
        parent::__construct(sprintf("User %s doesn't exist.", $userId));
    }
}