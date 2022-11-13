<?php

namespace App\Event\Notification;

use App\Entity\User;

class RegisterEvent extends AbstractNotificationEvent
{
    public function __construct(User $user)
    {
        $this->setRelatedUser($user);
    }

}