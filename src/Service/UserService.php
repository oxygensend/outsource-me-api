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

}