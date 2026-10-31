<?php

declare(strict_types=1);

namespace App\TasteCollection\Infrastructure\Http\Controller;

use App\Shared\Domain\Model\UserId;
use App\TasteCollection\Application\UseCase\SwipeVariant;
use App\TasteCollection\Application\UseCase\SwipeVariantHandler;
use App\TasteCollection\Domain\Model\VariantId;
use App\TasteCollection\Infrastructure\Http\Request\SwipeVariantRequest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Serializer\SerializerInterface;

#[AsController]
final readonly class SwipeVariantController
{
    public function __construct(
        private SwipeVariantHandler $handler,
        private SerializerInterface $serializer,
    ) {}

    #[Route(
        '/users/{userId}/swipes',
        methods: ['POST'],
        requirements: ['userId' => Requirement::UUID],
    )]
    public function __invoke(
        string $userId,
        #[MapRequestPayload] SwipeVariantRequest $request,
    ): Response {
        $swipe = ($this->handler)(new SwipeVariant(
            UserId::fromString($userId),
            VariantId::fromString($request->variantId),
            $request->liked,
        ));

        $jsonContent = $this->serializer->serialize($swipe, 'json');

        return JsonResponse::fromJsonString(
            $jsonContent, 
            Response::HTTP_CREATED,
        );
    }
}
