<?php

declare(strict_types=1);

namespace App\Identity\Infrastructure\Persistence\Doctrine;

use App\Identity\Domain\Model\User;
use App\Identity\Domain\Repository\UserRepository;
use App\Shared\Domain\Model\UserId;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineUserRepository implements UserRepository
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {}

    #[\Override]
    public function exists(UserId $id): bool
    {
        $n = $this->em->getRepository(User::class)
                      ->count(['id' => $id]);
        return $n > 0;
    }
};
