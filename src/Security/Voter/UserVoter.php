<?php

namespace App\Security\Voter;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class UserVoter extends Voter
{
    public const USER_EDIT = 'USER_EDIT';
    public const DELETE_TECHNOLOGY = 'DELETE_TECHNOLOGY';
    public const EDIT_OPINION = 'EDIT_OPINION';
    public const GET_NOTIFICATIONS = 'GET_NOTIFICATIONS';


    public function __construct(readonly private UserRepository $userRepository)
    {
    }

    protected function supports(string $attribute, $subject): bool
    {
        return (($attribute == self::USER_EDIT || $attribute == self::EDIT_OPINION)
                && $subject instanceof User)
            || (in_array($attribute, [self::DELETE_TECHNOLOGY, self::GET_NOTIFICATIONS]) && is_array($subject));

    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::USER_EDIT:
            case self::EDIT_OPINION:
                if ($user === $subject) return true;
                break;
            case self::DELETE_TECHNOLOGY:
                $resource = $this->userRepository->find($subject['userId']);
                if ($resource === $user) return true;
                break;
            case self::GET_NOTIFICATIONS:
                $resource = $this->userRepository->find($subject['id']);
                if ($resource === $user) return true;
                break;
            default:
                break;
        }

        return false;
    }
}
