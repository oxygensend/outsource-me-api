<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Exception\AccountStatusException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user)
    {
        if (!$user instanceof User)
            return;

        if($user->getEmailConfirmedAt() === null){
            throw new CustomUserMessageAccountStatusException('Your address email is not confirmed.');
        }
    }


    public function checkPostAuth(UserInterface $user)
    {
        if (!$user instanceof User)
            return;
    }

}