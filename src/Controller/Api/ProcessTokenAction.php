<?php

namespace App\Controller\Api;

use App\Entity\ConfirmationToken;
use App\Service\ConfirmationTokenService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
class ProcessTokenAction extends AbstractController
{
    public function __construct(private readonly ConfirmationTokenService $confirmationTokenService)
    {
    }

    /**
     * @Route("/api/confirmation_token/{type}/{token}", name="user_confirmation_token", methods={"GET"})
     */
    #[Route(
        path: '/api/confirmation_token/{type}/{token}',
        name: 'user_confirmation_token',
        defaults: [
            '_api_resource_class' => ConfirmationToken::class,
            '_api_operation_name' => '_api_/confirmation_token/{type}/{token}',
        ],
        methods: "GET",
    )]
    public function processToken(string $type, string $token): Response
    {
        return $this->confirmationTokenService->processToken($token, $type);
    }

}