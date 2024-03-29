<?php

namespace App\Controller\Api;

use App\DTO\PasswordResetSendLinkRequest;
use App\Repository\UserRepository;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

#[AsController]
class ResetPasswordSendLinkAction extends AbstractController
{
    public function __construct(private readonly UserRepository $userRepository,
                                private readonly UserService $userService)
    {
    }

    public function __invoke(PasswordResetSendLinkRequest $request): JsonResponse
    {
        $user = $this->userRepository->findOneBy(['email' => $request->getEmail()]);

        if (!$user) {
            throw new UnauthorizedHttpException('Unauthorized.', 'Invalid email address');
        }

        $this->userService->sendPasswordResetMessage($user);

        return new JsonResponse(['description' => 'Email with configured link will be send to given user.']);

    }

}