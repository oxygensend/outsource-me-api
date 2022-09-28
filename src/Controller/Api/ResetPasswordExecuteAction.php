<?php

namespace App\Controller\Api;

use App\DTO\PasswordResetExecuteRequest;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class ResetPasswordExecuteAction extends AbstractController
{
    public function __construct(private  readonly UserService $userService)
    {
    }

    public function __invoke(PasswordResetExecuteRequest $request): JsonResponse
    {
       $this->userService->executePasswordReset($request->getPassword(), $request->getConfirmationToken());

       return new JsonResponse(['description' => 'Password reset successfully.']);

    }

}