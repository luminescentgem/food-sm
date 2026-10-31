<?php

declare(strict_types=1);

namespace App\TasteCollection\Infrastructure\Http\Controller;

use App\Shared\Domain\Model\UserId;
use App\TasteCollection\Application\UseCase\RateVariant;
use App\TasteCollection\Application\UseCase\RateVariantHandler;
use App\TasteCollection\Domain\Model\VariantId;
use App\TasteCollection\Infrastructure\Http\Request\RateVariantRequest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Serializer\SerializerInterface;

#[AsController]
final readonly class RateVariantController
{
    public function __construct(
        private RateVariantHandler $handler,
        private SerializerInterface $serializer,
    ) {}

    #[Route(
        '/users/{userId}/ratings',
        methods: ['POST'],
        requirements: ['userId' => Requirement::UUID],
    )]
    public function __invoke(
        string $userId,
        #[MapRequestPayload] RateVariantRequest $request,
    ): Response {
        $rating = ($this->handler)(new RateVariant(
            UserId::fromString($userId),
            VariantId::fromString($request->variantId),
            $request->score,
        ));

        $jsonContent = $this->serializer->serialize($rating, 'json');

        return JsonResponse::fromJsonString(
            $jsonContent, 
            Response::HTTP_CREATED,
        );
    }
}
