<?php

declare(strict_types=1);

namespace App\TasteCollection\Infrastructure\Http\Controller;

use App\Shared\Domain\Model\UserId;
use App\TasteCollection\Application\UseCase\GetTopRated;
use App\TasteCollection\Application\UseCase\GetTopRatedHandler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\HttpKernel\Attribute\ValueResolver;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Serializer\SerializerInterface;

#[AsController]
final readonly class GetTopRatedController
{
    public function __construct(
        private GetTopRatedHandler $handler,
        private SerializerInterface $serializer,
    ) {}

    #[Route(
        '/users/{userId}/top',
        methods: ['GET'],
        requirements: ['userId' => Requirement::UUID],
    )]
    public function __invoke(
        #[ValueResolver('existing_user')] UserId $userId,
        #[MapQueryParameter(
            filter: \FILTER_VALIDATE_INT,
            options: ['min_range' => 1, 'max_range' => 50],
            validationFailedStatusCode: 422,
        )] int $limit = 5,
    ): Response {
        $top = ($this->handler)(new GetTopRated($userId, $limit));
        $jsonContent = $this->serializer->serialize($top, 'json');

        return JsonResponse::fromJsonString(
            $jsonContent,
            Response::HTTP_OK,
        );
    }
}
