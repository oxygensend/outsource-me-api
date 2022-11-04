<?php

namespace App\Controller\Api;

use App\DTO\PostApplicationRequest;
use App\Service\ApplicationService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class PostApplicationController
{
    public function __construct(readonly private ApplicationService $applicationService)
    {
    }

    public function __invoke(PostApplicationRequest $request): JsonResponse
    {

        $this->applicationService->createApplicationForJobOffer(
            $request->getJobOfferIri(),
            $request->getDescription(),
            $request->getAttachments()
        );

        return new JsonResponse(['description' => 'Application was created successfully.'], 201);

    }
}