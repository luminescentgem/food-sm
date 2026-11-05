<?php

declare(strict_types=1);

namespace App\TasteCollection\Infrastructure\Http\ValueResolver;

use App\Identity\Api\IdentityApi;
use App\Shared\Domain\Model\UserId;
use App\TasteCollection\Domain\Exception\UnknownUser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsTargetedValueResolver;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

#[AsTargetedValueResolver('existing_user')]
final readonly class UserIdValueResolver implements ValueResolverInterface
{
    public function __construct(
        private IdentityApi $identity,
    ) {}
        
    /** @return array{UserId}|array{} */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if ($argument->getType() !== UserId::class) {
            return [];
        }

        $id = UserId::fromString($request->attributes->get($argument->getName()));

        if (!$this->identity->exists($id)) {
            throw new UnknownUser($id);
        }

        return [$id];
    }
}