<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\DTO\PasswordResetSendLinkDto;
use App\Repository\UserRepository;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class ResetPasswordSendLinkProcessor implements ProcessorInterface
{
    public function __construct(private readonly UserService $userService,
                                private readonly UserRepository $userRepository)
    {
    }

    /**
     * @param PasswordResetSendLinkDto $data
     * @throws \Exception
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $user = $this->userRepository->findOneBy(['email' => $data->email]);

        if (!$user) {
            throw new UnauthorizedHttpException('Unauthorized.', 'Invalid email address');
        }

        $this->userService->sendPasswordResetMessage($user);

        return new JsonResponse(['description' => 'Email with configured link will be send to given user.']);

    }
}
