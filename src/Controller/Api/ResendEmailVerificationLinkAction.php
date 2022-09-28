<?php

namespace App\Controller\Api;

use App\DTO\ResendEmailVerificationLinkRequest;
use App\Repository\UserRepository;
use App\Service\UserService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;


#[AsController]
class ResendEmailVerificationLinkAction
{
    public function __construct(private readonly UserService $userService,
                                private readonly UserRepository $userRepository)
    {
    }

    public function __invoke(ResendEmailVerificationLinkRequest $request): JsonResponse
    {
        $user = $this->userRepository->findOneBy(['email' => $request->getEmail()]);

        if (!$user) {
            throw new UnauthorizedHttpException('Unauthorized.', 'Invalid email address.');
        }

        if ($user->getEmailConfirmedAt() !== null) {
            throw new UnauthorizedHttpException('Unauthorized.', 'Email for this user is confirmed.');
        }

        $this->userService->sendRegistrationConfirmationMessage($user);

        return new JsonResponse(['description' => 'Email with configured link will be send to given user.']);

    }

}