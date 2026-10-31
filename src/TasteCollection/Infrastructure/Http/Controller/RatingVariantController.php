<?php

declare(strict_types=1);

namespace App\TasteCollection\Infrastructure\Http\Controller;

use App\Shared\Domain\Model\UserId;
use App\TasteCollection\Application\UseCase\RatingVariant;
use App\TasteCollection\Application\UseCase\RatingVariantHandler;
use App\TasteCollection\Domain\Model\VariantId;
use App\TasteCollection\Infrastructure\Http\Request\RatingVariantRequest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Serializer\SerializerInterface;

#[AsController]
final readonly class RatingVariantController
{
    public function __construct(
        private RatingVariantHandler $handler,
        private SerializerInterface $serializer,
    ) {}

    #[Route(
        '/users/{userId}/ratings',
        methods: ['POST'],
        requirements: ['userId' => Requirement::UUID],
    )]
    public function __invoke(
        string $userId,
        #[MapRequestPayload] RatingVariantRequest $request,
    ): Response {
        $swipe = ($this->handler)(new RatingVariant(
            UserId::fromString($userId),
            VariantId::fromString($request->variantId),
            $request->score,
        ));

        $jsonContent = $this->serializer->serialize($swipe, 'json');

        return JsonResponse::fromJsonString(
            $jsonContent, 
            Response::HTTP_CREATED,
        );
    }
}
