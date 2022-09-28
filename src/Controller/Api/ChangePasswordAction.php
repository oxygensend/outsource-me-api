<?php

namespace App\Controller\Api;

use App\DTO\ChangePasswordRequest;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class ChangePasswordAction extends AbstractController
{
    public function __construct(private readonly UserService $userService)
    {
    }

    public function __invoke(ChangePasswordRequest $request): JsonResponse
    {
        $user = $this->getUser();

        $this->userService->changePassword($user, $request->getOldPassword(), $request->getNewPassword());

        return new JsonResponse(['description' => 'Password changed successfully.']);


    }

}