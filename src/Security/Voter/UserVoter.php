<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Twig\Sandbox\SecurityPolicyInterface;

class UserVoter extends Voter
{
    public const EDIT = 'USER_EDIT';

    public function __construct(readonly private Security $security)
    {
    }

    protected function supports(string $attribute, $subject): bool
    {
        return $attribute == self::EDIT && $subject instanceof User;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::EDIT:
                if($user === $subject) return true;
                break;
            default:
                break;
        }

        return false;
    }
}
