<?php

namespace App\Service;

use ApiPlatform\Api\IriConverterInterface;
use App\Entity\ConfirmationToken;
use App\Entity\Technology;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;

class UserService
{
    public function __construct(private readonly ConfirmationTokenService    $confirmationTokenService,
                                private readonly EmailSenderService          $emailSender,
                                private readonly EntityManagerInterface      $em,
                                private readonly LoggerInterface             $logger,
                                private readonly UserPasswordHasherInterface $passwordHasher,
                                private readonly IriConverterInterface       $iriConverter)
    {
    }

    /**
     * @throws \Exception
     */
    public function sendRegistrationConfirmationMessage(User $user): void
    {
        $token = $this->confirmationTokenService->findOrCreateConfirmationToken($user, ConfirmationToken::REGISTRATION_TYPE);
        $this->emailSender->sendRegistrationConfirmationEmail($user, $token);

    }

    /**
     * @throws \Exception
     */
    public function sendPasswordResetMessage(User $user): void
    {
        $token = $this->confirmationTokenService->findOrCreateConfirmationToken($user, ConfirmationToken::RESET_PASSWORD_TYPE);
        $this->emailSender->sendResetPasswordLinkEmail($user, $token);

    }


    public function executePasswordReset(string $plainPassword, string $token): void
    {
        $tokens = $this->em->getRepository(ConfirmationToken::class)->findValidConfirmationTokens($token, ConfirmationToken::RESET_PASSWORD_EXECUTE_TYPE);

        if (count($tokens) === 0) {
            $this->logger->warning('Invalid confirmation token.', ['token' => $token]);
            throw new UnauthorizedHttpException('Unauthorized', 'Invalid or expired confirmation token.');
        }

        /** @var ConfirmationToken $confirmationToken */
        $confirmationToken = array_pop($tokens);

        $user = $confirmationToken->getUser();
        if (!$user) {
            $this->logger->warning('User not found', ['token' => $token]);
            throw new UnauthorizedHttpException('Unauthorized', 'User not found');
        }

        $user->setPassword($this->passwordHasher->hashPassword($user, $plainPassword));

        $this->em->getRepository(ConfirmationToken::class)->removeUserTokensOfType($user, $confirmationToken->getType());
        $this->em->flush();


    }

    /**
     * @throws \Exception
     */
    public function changePassword(User $user, string $oldPassword, string $newPassword): void
    {
        if (!$this->passwordHasher->isPasswordValid($user, $oldPassword)) {
            $this->logger->warning('UserService::changePassword - Invalid old password.', ['user' => $user]);
            throw new UnauthorizedHttpException("Unauthorized", "Invalid old password.");
        }

        $user->setPassword(
            $this->passwordHasher->hashPassword($user, $newPassword)
        );
        $this->em->flush();

    }


    public function addTechnology(User $user, string $technologyIri): Technology
    {
        /** @var Technology $technology */
        $technology = $this->iriConverter->getResourceFromIri($technologyIri);

        if ($user->getTechnologies()->contains($technology)) {
            throw new BadRequestHttpException("User has already contain this technology");
        }

        $user->addTechnology($technology);
        $this->em->flush();

        return $technology;

    }

    public function uploadPhoto(User $user, File $file): void
    {
        $user->setImageFile($file);
        $this->em->flush();

    }

}