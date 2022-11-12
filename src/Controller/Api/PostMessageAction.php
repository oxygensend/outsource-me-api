<?php

namespace App\Controller\Api;

use App\DTO\PostMessageRequest;
use App\Service\MessagingService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class PostMessageAction extends AbstractController
{
    public function __construct(readonly private MessagingService $messagingService)
    {
    }

    public function __invoke(PostMessageRequest $request): Response
    {

        $this->messagingService->sendEmailMessage(
            $request->getReceiverIri(),
            $request->getContent(),
            $request->getSubject()
        );

        return new JsonResponse('Message was sent.', 201);
    }

}