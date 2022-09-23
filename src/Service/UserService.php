<?php

namespace App\Service;

use App\Entity\ConfirmationToken;
use App\Entity\User;

class UserService
{
    public function __construct(private readonly ConfirmationTokenService $confirmationTokenService,
                                private readonly EmailSenderService $emailSender)
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

}