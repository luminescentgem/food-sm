<?php

declare(strict_types=1);

namespace App\TasteCollection\Infrastructure\Http\Controller;

use App\TasteCollection\Application\UseCase\GetMatchingIngredients;
use App\TasteCollection\Application\UseCase\GetMatchingIngredientsHandler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[AsController]
final readonly class GetMatchingIngredientsController
{
    public function __construct(
        private GetMatchingIngredientsHandler $handler,
        private SerializerInterface $serializer,
    ) {}

    #[Route(
        '/ingredients',
        methods: ['GET'],
    )]
    public function __invoke(
        #[MapQueryParameter(
            name: 'q',
            filter: \FILTER_VALIDATE_REGEXP,
            options: ['regexp' => '/\S/'],
            validationFailedStatusCode: 422,
        )] string $query,
        #[MapQueryParameter(
            filter: \FILTER_VALIDATE_INT,
            options: ['min_range' => 1, 'max_range' => 50],
            validationFailedStatusCode: 422,
        )] int $limit = 50,
    ): Response {
        $matching = ($this->handler)(new GetMatchingIngredients($query, $limit));
        $jsonContent = $this->serializer->serialize($matching, 'json');

        return JsonResponse::fromJsonString(
            $jsonContent,
            Response::HTTP_OK,
        );
    }
}
